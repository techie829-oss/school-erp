<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AttendanceSummary;
use App\Models\AttendanceSettings;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentAttendanceController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display student attendance dashboard
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $date = $request->get('date', now()->format('Y-m-d'));
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        // Get filter options
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');

        // Get sections if class selected
        $sections = $classId
            ? Section::forTenant($tenant->id)->where('class_id', $classId)->active()->get()
            : Section::forTenant($tenant->id)->active()->get();

        // Get today's attendance stats
        $todayStats = $this->getTodayStats($tenant->id, $classId, $sectionId);

        // Get monthly attendance data
        $monthlyData = $this->getMonthlyData($tenant->id, $month, $year, $classId, $sectionId);

        return view('tenant.admin.attendance.students.index', compact(
            'tenant',
            'classes',
            'sections',
            'classId',
            'sectionId',
            'date',
            'month',
            'year',
            'todayStats',
            'monthlyData'
        ));
    }

    /**
     * Show mark attendance page
     */
    public function mark(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $date = $request->get('date', now()->format('Y-m-d'));
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $sections = [];
        $students = collect();
        $existingAttendance = collect();

        if ($classId) {
            $sections = Section::forTenant($tenant->id)
                ->where('class_id', $classId)
                ->active()
                ->get();
        }

        if ($classId && $sectionId) {
            // Get students in this section
            $students = Student::forTenant($tenant->id)
                ->whereHas('currentEnrollment', function($q) use ($classId, $sectionId) {
                    $q->where('class_id', $classId)
                      ->where('section_id', $sectionId);
                })
                ->with('currentEnrollment')
                ->orderBy('full_name')
                ->get();

            // Get existing attendance for this date
            $existingAttendance = StudentAttendance::forDate($date)
                ->forClass($classId)
                ->forSection($sectionId)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');
        }

        return view('tenant.admin.attendance.students.mark', compact(
            'tenant',
            'classes',
            'sections',
            'students',
            'existingAttendance',
            'classId',
            'sectionId',
            'date'
        ));
    }

    /**
     * Save attendance
     */
    public function save(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,half_day,on_leave,holiday',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            foreach ($request->attendance as $record) {
                StudentAttendance::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'student_id' => $record['student_id'],
                        'attendance_date' => $request->date,
                        'period_number' => null, // Daily attendance, not period-wise
                    ],
                    [
                        'class_id' => $request->class_id,
                        'section_id' => $request->section_id,
                        'status' => $record['status'],
                        'remarks' => $record['remarks'] ?? null,
                        'marked_by' => auth()->id(),
                        'marked_at' => now(),
                    ]
                );
            }

            // Update monthly summary for affected students
            $month = Carbon::parse($request->date)->month;
            $year = Carbon::parse($request->date)->year;

            foreach ($request->attendance as $record) {
                AttendanceSummary::calculateSummary(
                    $tenant->id,
                    'student',
                    $record['student_id'],
                    $month,
                    $year
                );
            }

            DB::commit();

            return redirect('/admin/attendance/students')
                ->with('success', 'Attendance marked successfully for ' . count($request->attendance) . ' students!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save attendance: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Get today's statistics
     */
    private function getTodayStats($tenantId, $classId = null, $sectionId = null)
    {
        $query = StudentAttendance::where('tenant_id', $tenantId)
            ->forDate(now()->format('Y-m-d'));

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $total = $query->count();
        $present = (clone $query)->where('status', 'present')->count();
        $absent = (clone $query)->where('status', 'absent')->count();
        $late = (clone $query)->where('status', 'late')->count();

        $percentage = $total > 0 ? round((($present + $late) / $total) * 100, 2) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'percentage' => $percentage,
        ];
    }

    /**
     * Get monthly attendance data
     */
    private function getMonthlyData($tenantId, $month, $year, $classId = null, $sectionId = null)
    {
        $query = StudentAttendance::where('tenant_id', $tenantId)
            ->forMonth($month, $year);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $records = $query->get();

        return [
            'total_records' => $records->count(),
            'present' => $records->where('status', 'present')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'late' => $records->where('status', 'late')->count(),
            'on_leave' => $records->where('status', 'on_leave')->count(),
        ];
    }

    /**
     * Generate attendance report
     */
    public function report(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        // Get filter options
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $sections = Section::forTenant($tenant->id)->active()->get();
        $students = Student::forTenant($tenant->id)->active()->get();

        $reportData = null;

        // If filters are applied, generate report
        if ($request->has('date_from') || $request->has('report_type')) {
            $reportType = $request->get('report_type', 'daily');
            $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
            $dateTo = $request->get('date_to', now()->format('Y-m-d'));
            $classId = $request->get('class_id');
            $sectionId = $request->get('section_id');
            $studentId = $request->get('student_id');
            $threshold = $request->get('threshold', 75);

            switch ($reportType) {
                case 'daily':
                    $reportData = $this->generateDailyReport($tenant->id, $dateFrom, $classId, $sectionId);
                    break;
                case 'monthly':
                    $reportData = $this->generateMonthlyReport($tenant->id, $dateFrom, $dateTo, $classId, $sectionId);
                    break;
                case 'student_wise':
                    $reportData = $this->generateStudentWiseReport($tenant->id, $dateFrom, $dateTo, $studentId, $classId, $sectionId);
                    break;
                case 'class_wise':
                    $reportData = $this->generateClassWiseReport($tenant->id, $dateFrom, $dateTo, $classId);
                    break;
                case 'defaulters':
                    $reportData = $this->generateDefaultersReport($tenant->id, $dateFrom, $dateTo, $threshold, $classId, $sectionId);
                    break;
            }
        }

        return view('tenant.admin.attendance.students.report', compact(
            'tenant',
            'classes',
            'sections',
            'students',
            'reportData'
        ));
    }

    /**
     * Export attendance data
     */
    public function export(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $format = $request->get('format', 'excel');
        $reportType = $request->get('report_type', 'daily');
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $studentId = $request->get('student_id');
        $threshold = $request->get('threshold', 75);

        // Generate report data
        switch ($reportType) {
            case 'daily':
                $reportData = $this->generateDailyReport($tenant->id, $dateFrom, $classId, $sectionId);
                break;
            case 'monthly':
                $reportData = $this->generateMonthlyReport($tenant->id, $dateFrom, $dateTo, $classId, $sectionId);
                break;
            case 'student_wise':
                $reportData = $this->generateStudentWiseReport($tenant->id, $dateFrom, $dateTo, $studentId, $classId, $sectionId);
                break;
            case 'class_wise':
                $reportData = $this->generateClassWiseReport($tenant->id, $dateFrom, $dateTo, $classId);
                break;
            case 'defaulters':
                $reportData = $this->generateDefaultersReport($tenant->id, $dateFrom, $dateTo, $threshold, $classId, $sectionId);
                break;
            default:
                $reportData = $this->generateDailyReport($tenant->id, $dateFrom, $classId, $sectionId);
        }

        if ($format === 'excel') {
            return $this->exportToExcel($reportData, $tenant);
        } else {
            return $this->exportToPDF($reportData, $tenant);
        }
    }

    /**
     * Generate daily report
     */
    private function generateDailyReport($tenantId, $date, $classId = null, $sectionId = null)
    {
        $query = StudentAttendance::forTenant($tenantId)
            ->forDate($date)
            ->with(['student.currentEnrollment.schoolClass', 'student.currentEnrollment.section', 'schoolClass', 'section']);

        if ($classId) {
            $query->where('class_id', $classId);
        }
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $records = $query->get();

        return [
            'type' => 'daily',
            'title' => 'Daily Attendance Report - ' . Carbon::parse($date)->format('F d, Y'),
            'date' => $date,
            'records' => $records,
            'summary' => [
                'total' => $records->count(),
                'present' => $records->where('status', 'present')->count(),
                'absent' => $records->where('status', 'absent')->count(),
                'late' => $records->where('status', 'late')->count(),
                'half_day' => $records->where('status', 'half_day')->count(),
                'on_leave' => $records->where('status', 'on_leave')->count(),
                'percentage' => $records->count() > 0
                    ? (($records->where('status', 'present')->count() + $records->where('status', 'late')->count()) / $records->count()) * 100
                    : 0
            ]
        ];
    }

    /**
     * Generate monthly report
     */
    private function generateMonthlyReport($tenantId, $dateFrom, $dateTo, $classId = null, $sectionId = null)
    {
        $query = Student::forTenant($tenantId)->active()->with('currentEnrollment.schoolClass');

        if ($classId) {
            $query->whereHas('currentEnrollment', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }
        if ($sectionId) {
            $query->whereHas('currentEnrollment', function ($q) use ($sectionId) {
                $q->where('section_id', $sectionId);
            });
        }

        $students = $query->get();
        $records = [];
        $totalDays = 0;
        $totalPresent = 0;
        $totalAbsent = 0;
        $totalLate = 0;
        $totalHalfDay = 0;
        $totalLeave = 0;

        foreach ($students as $student) {
            $attendance = StudentAttendance::forTenant($tenantId)
                ->where('student_id', $student->id)
                ->whereBetween('attendance_date', [$dateFrom, $dateTo])
                ->get();

            $present = $attendance->where('status', 'present')->count();
            $absent = $attendance->where('status', 'absent')->count();
            $late = $attendance->where('status', 'late')->count();
            $halfDay = $attendance->where('status', 'half_day')->count();
            $onLeave = $attendance->where('status', 'on_leave')->count();
            $total = $attendance->count();

            $percentage = $total > 0 ? (($present + $late + ($halfDay * 0.5)) / $total) * 100 : 0;

            $records[] = [
                'student' => $student,
                'class_name' => $student->currentEnrollment->schoolClass->class_name ?? 'N/A',
                'total_days' => $total,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'half_day' => $halfDay,
                'on_leave' => $onLeave,
                'percentage' => $percentage
            ];

            $totalDays += $total;
            $totalPresent += $present;
            $totalAbsent += $absent;
            $totalLate += $late;
            $totalHalfDay += $halfDay;
            $totalLeave += $onLeave;
        }

        return [
            'type' => 'monthly',
            'title' => 'Monthly Attendance Summary - ' . Carbon::parse($dateFrom)->format('M d') . ' to ' . Carbon::parse($dateTo)->format('M d, Y'),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'records' => $records,
            'summary' => [
                'total_days' => $totalDays,
                'present_days' => $totalPresent,
                'absent_days' => $totalAbsent,
                'late_days' => $totalLate,
                'half_days' => $totalHalfDay,
                'leave_days' => $totalLeave,
                'percentage' => $totalDays > 0 ? (($totalPresent + $totalLate + ($totalHalfDay * 0.5)) / $totalDays) * 100 : 0
            ]
        ];
    }

    /**
     * Generate student-wise report
     */
    private function generateStudentWiseReport($tenantId, $dateFrom, $dateTo, $studentId = null, $classId = null, $sectionId = null)
    {
        if (!$studentId) {
            // If no specific student, get first student from filters
            $query = Student::forTenant($tenantId)->active();
            if ($classId) {
                $query->whereHas('currentEnrollment', function ($q) use ($classId) {
                    $q->where('class_id', $classId);
                });
            }
            $student = $query->first();
            $studentId = $student?->id;
        } else {
            $student = Student::find($studentId);
        }

        if (!$student) {
            return [
                'type' => 'student_wise',
                'title' => 'Student Attendance History',
                'records' => [],
                'summary' => ['total_days' => 0, 'present' => 0, 'absent' => 0, 'late' => 0, 'half_day' => 0, 'on_leave' => 0, 'percentage' => 0]
            ];
        }

        $records = StudentAttendance::forTenant($tenantId)
            ->where('student_id', $studentId)
            ->whereBetween('attendance_date', [$dateFrom, $dateTo])
            ->with('markedBy')
            ->orderBy('attendance_date', 'desc')
            ->get();

        $summary = [
            'total_days' => $records->count(),
            'present' => $records->where('status', 'present')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'late' => $records->where('status', 'late')->count(),
            'half_day' => $records->where('status', 'half_day')->count(),
            'on_leave' => $records->where('status', 'on_leave')->count(),
        ];

        $summary['percentage'] = $summary['total_days'] > 0
            ? (($summary['present'] + $summary['late'] + ($summary['half_day'] * 0.5)) / $summary['total_days']) * 100
            : 0;

        return [
            'type' => 'student_wise',
            'title' => 'Attendance History - ' . $student->first_name . ' ' . $student->last_name,
            'student' => $student,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'records' => $records,
            'summary' => $summary
        ];
    }

    /**
     * Generate class-wise report
     */
    private function generateClassWiseReport($tenantId, $dateFrom, $dateTo, $classId = null)
    {
        $query = SchoolClass::forTenant($tenantId)->active();
        if ($classId) {
            $query->where('id', $classId);
        }
        $classes = $query->get();

        $records = [];
        $totalClasses = 0;
        $totalStudents = 0;
        $totalDays = 0;
        $allPercentages = [];

        foreach ($classes as $class) {
            $students = Student::forTenant($tenantId)
                ->active()
                ->whereHas('currentEnrollment', function ($q) use ($class) {
                    $q->where('class_id', $class->id);
                })
                ->count();

            $attendance = StudentAttendance::forTenant($tenantId)
                ->where('class_id', $class->id)
                ->whereBetween('attendance_date', [$dateFrom, $dateTo])
                ->get();

            $present = $attendance->where('status', 'present')->count();
            $absent = $attendance->where('status', 'absent')->count();
            $late = $attendance->where('status', 'late')->count();
            $halfDay = $attendance->where('status', 'half_day')->count();
            $onLeave = $attendance->where('status', 'on_leave')->count();
            $total = $attendance->count();

            $percentage = $total > 0 ? (($present + $late + ($halfDay * 0.5)) / $total) * 100 : 0;

            $records[] = [
                'class_name' => $class->class_name,
                'student_count' => $students,
                'total_days' => $total,
                'present' => $present,
                'absent' => $absent,
                'late' => $late,
                'half_day' => $halfDay,
                'on_leave' => $onLeave,
                'percentage' => $percentage
            ];

            $totalClasses++;
            $totalStudents += $students;
            $totalDays += $total;
            if ($percentage > 0) {
                $allPercentages[] = $percentage;
            }
        }

        return [
            'type' => 'class_wise',
            'title' => 'Class-wise Attendance Summary - ' . Carbon::parse($dateFrom)->format('M d') . ' to ' . Carbon::parse($dateTo)->format('M d, Y'),
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'records' => $records,
            'summary' => [
                'total_classes' => $totalClasses,
                'total_students' => $totalStudents,
                'total_days' => $totalDays,
                'avg_attendance' => count($allPercentages) > 0 ? array_sum($allPercentages) / count($allPercentages) : 0
            ]
        ];
    }

    /**
     * Generate defaulters report
     */
    private function generateDefaultersReport($tenantId, $dateFrom, $dateTo, $threshold, $classId = null, $sectionId = null)
    {
        $query = Student::forTenant($tenantId)->active()->with('currentEnrollment.schoolClass');

        if ($classId) {
            $query->whereHas('currentEnrollment', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }
        if ($sectionId) {
            $query->whereHas('currentEnrollment', function ($q) use ($sectionId) {
                $q->where('section_id', $sectionId);
            });
        }

        $students = $query->get();
        $defaulters = [];

        foreach ($students as $student) {
            $attendance = StudentAttendance::forTenant($tenantId)
                ->where('student_id', $student->id)
                ->whereBetween('attendance_date', [$dateFrom, $dateTo])
                ->get();

            if ($attendance->count() == 0) continue;

            $present = $attendance->where('status', 'present')->count();
            $absent = $attendance->where('status', 'absent')->count();
            $late = $attendance->where('status', 'late')->count();
            $halfDay = $attendance->where('status', 'half_day')->count();
            $total = $attendance->count();

            $percentage = (($present + $late + ($halfDay * 0.5)) / $total) * 100;

            if ($percentage < $threshold) {
                $defaulters[] = [
                    'student' => $student,
                    'class_name' => $student->currentEnrollment->schoolClass->class_name ?? 'N/A',
                    'total_days' => $total,
                    'present' => $present,
                    'absent' => $absent,
                    'late' => $late,
                    'half_day' => $halfDay,
                    'percentage' => $percentage
                ];
            }
        }

        // Sort by percentage (lowest first)
        usort($defaulters, function ($a, $b) {
            return $a['percentage'] <=> $b['percentage'];
        });

        return [
            'type' => 'defaulters',
            'title' => 'Defaulters Report - Below ' . $threshold . '% Attendance',
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'threshold' => $threshold,
            'records' => $defaulters,
            'summary' => []
        ];
    }

    /**
     * Export to Excel
     */
    private function exportToExcel($reportData, $tenant)
    {
        $filename = 'student_attendance_' . $reportData['type'] . '_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($reportData, $tenant) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [$tenant->data['name'] ?? 'School ERP']);
            fputcsv($file, [$reportData['title']]);
            fputcsv($file, ['Generated: ' . now()->format('F d, Y h:i A')]);
            fputcsv($file, []);

            // Different headers based on report type
            if ($reportData['type'] === 'daily') {
                fputcsv($file, ['Roll No', 'Student Name', 'Admission No', 'Class', 'Section', 'Status', 'Remarks']);
                foreach ($reportData['records'] as $record) {
                    fputcsv($file, [
                        $record->student->currentEnrollment->roll_number ?? 'N/A',
                        $record->student->first_name . ' ' . $record->student->last_name,
                        $record->student->admission_number,
                        $record->schoolClass->class_name ?? 'N/A',
                        $record->section->section_name ?? 'N/A',
                        ucfirst($record->status),
                        $record->remarks ?? ''
                    ]);
                }
            } elseif ($reportData['type'] === 'monthly' || $reportData['type'] === 'defaulters') {
                fputcsv($file, ['Student Name', 'Admission No', 'Class', 'Total Days', 'Present', 'Absent', 'Late', 'Half Day', 'On Leave', 'Attendance %']);
                foreach ($reportData['records'] as $record) {
                    fputcsv($file, [
                        $record['student']->first_name . ' ' . $record['student']->last_name,
                        $record['student']->admission_number,
                        $record['class_name'],
                        $record['total_days'],
                        $record['present'],
                        $record['absent'],
                        $record['late'],
                        $record['half_day'],
                        $record['on_leave'] ?? 0,
                        number_format($record['percentage'], 2) . '%'
                    ]);
                }
            } elseif ($reportData['type'] === 'class_wise') {
                fputcsv($file, ['Class', 'Students', 'Total Days', 'Present', 'Absent', 'Late', 'Half Day', 'On Leave', 'Attendance %']);
                foreach ($reportData['records'] as $record) {
                    fputcsv($file, [
                        $record['class_name'],
                        $record['student_count'],
                        $record['total_days'],
                        $record['present'],
                        $record['absent'],
                        $record['late'],
                        $record['half_day'],
                        $record['on_leave'],
                        number_format($record['percentage'], 2) . '%'
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export to PDF
     */
    private function exportToPDF($reportData, $tenant)
    {
        // For now, return a simple HTML-to-PDF approach
        // In production, use a package like dompdf or snappy
        $html = view('tenant.admin.attendance.students.exports.pdf', compact('reportData', 'tenant'))->render();

        return response($html)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="attendance_report.pdf"');
    }
}

