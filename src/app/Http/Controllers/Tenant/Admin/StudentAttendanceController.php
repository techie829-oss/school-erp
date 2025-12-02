<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentAttendance;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherSubject;
use App\Models\AttendanceSummary;
use App\Models\AttendanceSettings;
use App\Models\Holiday;
use App\Services\TenantService;
use App\Services\NotificationService;
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
     * Class / Section monthly calendar view (Option B)
     */
    public function calendar(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $month = (int) $request->get('month', now()->month);
        $year = (int) $request->get('year', now()->year);
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');

        // Filters
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $sections = $classId
            ? Section::forTenant($tenant->id)->where('class_id', $classId)->active()->get()
            : Section::forTenant($tenant->id)->active()->get();

        $currentMonth = Carbon::create($year, $month, 1);
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // Fetch holidays for this month for highlighting
        $holidayMap = Holiday::forTenant($tenant->id)
            ->whereBetween('date', [$startOfMonth->format('Y-m-d'), $endOfMonth->format('Y-m-d')])
            ->with('scopes')
            ->get()
            ->keyBy(fn ($h) => $h->date->format('Y-m-d'));

        // Aggregate attendance per day for this class/section
        $rawDaily = StudentAttendance::forTenant($tenant->id)
            ->whereBetween('attendance_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->when($classId, function ($q) use ($classId) {
                $q->where('class_id', $classId);
            })
            ->when($sectionId, function ($q) use ($sectionId) {
                $q->where('section_id', $sectionId);
            })
            ->selectRaw('attendance_date,
                COUNT(*) as total,
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = "late" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = "half_day" THEN 1 ELSE 0 END) as half_day,
                SUM(CASE WHEN status = "on_leave" THEN 1 ELSE 0 END) as on_leave')
            ->groupBy('attendance_date')
            ->orderBy('attendance_date')
            ->get()
            ->keyBy('attendance_date');

        // Build calendar matrix: 6 weeks x 7 days (week starts on Sunday)
        $calendarDays = [];
        // 0 (Sun) - 6 (Sat)
        $startWeekDay = $startOfMonth->dayOfWeek;
        $daysInMonth = $currentMonth->daysInMonth;

        $dayCounter = 1;
        for ($week = 0; $week < 6; $week++) {
            for ($dow = 0; $dow < 7; $dow++) {
                $cell = [
                    'date' => null,
                    'day' => null,
                    'data' => null,
                ];

                if (($week === 0 && $dow < $startWeekDay) || $dayCounter > $daysInMonth) {
                    // Empty cell
                } else {
                    $dateObj = Carbon::create($year, $month, $dayCounter);
                    $dateKey = $dateObj->toDateString();
                    $stats = $rawDaily->get($dateKey);

                    $cell['date'] = $dateKey;
                    $cell['day'] = $dayCounter;

                    $isHoliday = false;
                    $holiday = null;

                    if ($holidayMap->has($dateKey)) {
                        $candidate = $holidayMap->get($dateKey);

                        // If no specific scopes configured, applies to all classes/sections
                        if ($candidate->scopes->isEmpty()) {
                            $isHoliday = true;
                            $holiday = $candidate;
                        } else {
                            // Class/section specific holiday
                            if ($classId) {
                                foreach ($candidate->scopes as $scope) {
                                    if ($scope->class_id == $classId) {
                                        if (!$scope->section_id || !$sectionId || $scope->section_id == $sectionId) {
                                            $isHoliday = true;
                                            $holiday = $candidate;
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($stats || $isHoliday) {
                        $total = (int) $stats->total;
                        $presentLike = (int) $stats->present + (int) $stats->late + (int) $stats->half_day;
                        $percentage = $total > 0 ? round(($presentLike / $total) * 100) : 0;

                        $cell['data'] = [
                            'total' => $total,
                            'present' => (int) ($stats->present ?? 0),
                            'absent' => (int) ($stats->absent ?? 0),
                            'late' => (int) ($stats->late ?? 0),
                            'half_day' => (int) ($stats->half_day ?? 0),
                            'on_leave' => (int) ($stats->on_leave ?? 0),
                            'percentage' => $percentage,
                            'is_holiday' => $isHoliday,
                            'holiday_title' => $holiday?->title,
                            'holiday_type' => $holiday?->type,
                            'holiday_full_day' => $holiday?->is_full_day ?? true,
                        ];
                    }

                    $dayCounter++;
                }

                $calendarDays[$week][$dow] = $cell;
            }
        }

        // Simple month navigation (keeping current filters)
        $prevMonth = $currentMonth->copy()->subMonth();
        $nextMonth = $currentMonth->copy()->addMonth();

        return view('tenant.admin.attendance.students.calendar', compact(
            'tenant',
            'classes',
            'sections',
            'classId',
            'sectionId',
            'month',
            'year',
            'calendarDays',
            'currentMonth',
            'prevMonth',
            'nextMonth'
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

            // Send notifications for absent/low attendance students (non-blocking)
            try {
                $notificationService = new NotificationService($tenant->id);
                $date = Carbon::parse($request->date);
                $class = SchoolClass::find($request->class_id);
                $section = Section::find($request->section_id);

                foreach ($request->attendance as $record) {
                    $student = Student::find($record['student_id']);
                    if (!$student) continue;

                    $status = $record['status'];

                    // Send notification for absent students
                    if ($status === 'absent') {
                        $mobile = $student->father_phone ?? $student->mother_phone ?? $student->guardian_phone ?? $student->phone;
                        $email = $student->father_email ?? $student->mother_email ?? $student->guardian_email ?? $student->email;

                        if ($mobile) {
                            $message = "Dear Parent, {$student->full_name} (Admission: {$student->admission_number}) was absent on {$date->format('d M Y')}. Class: {$class->class_name}" . ($section ? " - {$section->section_name}" : '') . ". Please contact school if needed.";
                            $notificationService->sendSms($mobile, $message, 'attendance');
                        }

                        if ($email) {
                            $subject = "Absence Notice - {$student->full_name}";
                            $body = "<p>Dear Parent,</p><p>This is to inform you that <strong>{$student->full_name}</strong> (Admission Number: {$student->admission_number}) was <strong>absent</strong> on <strong>{$date->format('d M Y')}</strong>.</p><p><strong>Class:</strong> {$class->class_name}" . ($section ? " - {$section->section_name}" : '') . "</p><p>Please contact the school if you have any concerns.</p>";
                            $notificationService->sendEmail($email, $subject, $body);
                        }
                    }

                    // Check for low attendance percentage and send alert
                    $summary = AttendanceSummary::forStudent($tenant->id, $student->id, $date->month, $date->year);
                    if ($summary && $summary->attendance_percentage < 75 && $summary->attendance_percentage > 0) {
                        $mobile = $student->father_phone ?? $student->mother_phone ?? $student->guardian_phone ?? $student->phone;
                        $email = $student->father_email ?? $student->mother_email ?? $student->guardian_email ?? $student->email;

                        if ($mobile) {
                            $message = "Dear Parent, {$student->full_name}'s attendance for {$date->format('F Y')} is {$summary->attendance_percentage}% (below 75%). Please ensure regular attendance.";
                            $notificationService->sendSms($mobile, $message, 'attendance');
                        }

                        if ($email) {
                            $subject = "Low Attendance Alert - {$student->full_name}";
                            $body = "<p>Dear Parent,</p><p>This is to inform you that <strong>{$student->full_name}</strong>'s attendance for <strong>{$date->format('F Y')}</strong> is <strong>{$summary->attendance_percentage}%</strong>, which is below the required 75%.</p><p>Please ensure regular attendance.</p>";
                            $notificationService->sendEmail($email, $subject, $body);
                        }
                    }
                }
            } catch (\Exception $e) {
                // Never block attendance save due to notification failures
                \Log::error('Attendance notification error: ' . $e->getMessage());
            }

            return redirect('/admin/attendance/students')
                ->with('success', 'Attendance marked successfully for ' . count($request->attendance) . ' students!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save attendance: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show period-wise attendance marking page
     */
    public function markPeriod(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $date = $request->get('date', now()->format('Y-m-d'));
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $periodNumber = $request->get('period_number', 1);
        $subjectId = $request->get('subject_id');

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $sections = [];
        $subjects = [];
        $students = collect();
        $existingAttendance = collect();
        $teacher = null;

        if ($classId) {
            $sections = Section::forTenant($tenant->id)
                ->where('class_id', $classId)
                ->active()
                ->get();

            // Get subjects for this class
            $subjects = Subject::forTenant($tenant->id)
                ->active()
                ->whereHas('teachers', function($q) use ($classId) {
                    $q->where('teacher_subjects.class_id', $classId);
                })
                ->get();

            // If subject selected, get the teacher
            if ($subjectId) {
                $teacherSubject = TeacherSubject::forTenant($tenant->id)
                    ->where('class_id', $classId)
                    ->where('subject_id', $subjectId)
                    ->primary()
                    ->first();
                $teacher = $teacherSubject?->teacher;
            }
        }

        if ($classId && $sectionId) {
            $students = Student::forTenant($tenant->id)
                ->whereHas('currentEnrollment', function($q) use ($classId, $sectionId) {
                    $q->where('class_id', $classId)
                      ->where('section_id', $sectionId);
                })
                ->with('currentEnrollment')
                ->orderBy('full_name')
                ->get();

            // Get existing period-wise attendance for this date/period/subject
            $existingAttendance = StudentAttendance::forDate($date)
                ->forClass($classId)
                ->forSection($sectionId)
                ->where('period_number', $periodNumber)
                ->when($subjectId, function($q) use ($subjectId) {
                    $q->where('subject_id', $subjectId);
                })
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');
        }

        return view('tenant.admin.attendance.students.mark-period', compact(
            'tenant',
            'classes',
            'sections',
            'subjects',
            'students',
            'existingAttendance',
            'classId',
            'sectionId',
            'date',
            'periodNumber',
            'subjectId',
            'teacher'
        ));
    }

    /**
     * Save period-wise attendance
     */
    public function savePeriod(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'period_number' => 'required|integer|min:1|max:10',
            'subject_id' => 'nullable|exists:subjects,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.status' => 'required|in:present,absent,late,half_day,on_leave',
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
                        'period_number' => $request->period_number,
                        'subject_id' => $request->subject_id,
                    ],
                    [
                        'class_id' => $request->class_id,
                        'section_id' => $request->section_id,
                        'status' => $record['status'],
                        'teacher_id' => $request->teacher_id,
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
                ->with('success', 'Period-wise attendance marked successfully for ' . count($request->attendance) . ' students!');

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
     * Export to PDF (Print Preview)
     */
    private function exportToPDF($reportData, $tenant)
    {
        // Return view for print preview instead of download
        return view('tenant.admin.attendance.students.exports.pdf', compact('reportData', 'tenant'));
    }

    /**
     * Show bulk operations page
     */
    public function bulk(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $sections = Section::forTenant($tenant->id)->active()->get();

        return view('tenant.admin.attendance.students.bulk', compact(
            'tenant',
            'classes',
            'sections'
        ));
    }

    /**
     * Get students for bulk operations (AJAX)
     */
    public function getStudentsForBulk(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');

        if (!$classId) {
            return response()->json(['students' => []]);
        }

        $query = Student::forTenant($tenant->id)
            ->whereHas('currentEnrollment', function($q) use ($classId, $sectionId) {
                $q->where('class_id', $classId);
                if ($sectionId) {
                    $q->where('section_id', $sectionId);
                }
            })
            ->with('currentEnrollment')
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'admission_number']);

        return response()->json(['students' => $query]);
    }

    /**
     * Bulk save attendance (mark multiple students for date range)
     */
    public function bulkSave(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'status' => 'required|in:present,absent,late,half_day,on_leave',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $dateFrom = Carbon::parse($request->date_from);
            $dateTo = Carbon::parse($request->date_to);
            $dates = [];
            $current = $dateFrom->copy();

            while ($current->lte($dateTo)) {
                $dates[] = $current->format('Y-m-d');
                $current->addDay();
            }

            $count = 0;
            foreach ($request->student_ids as $studentId) {
                foreach ($dates as $date) {
                    StudentAttendance::updateOrCreate(
                        [
                            'tenant_id' => $tenant->id,
                            'student_id' => $studentId,
                            'attendance_date' => $date,
                            'period_number' => null,
                        ],
                        [
                            'class_id' => $request->class_id,
                            'section_id' => $request->section_id ?? null,
                            'status' => $request->status,
                            'marked_by' => auth()->id(),
                            'marked_at' => now(),
                        ]
                    );
                    $count++;
                }
            }

            // Update summaries for affected students
            $month = $dateFrom->month;
            $year = $dateFrom->year;
            foreach ($request->student_ids as $studentId) {
                AttendanceSummary::calculateSummary($tenant->id, 'student', $studentId, $month, $year);
            }

            DB::commit();

            return redirect('/admin/attendance/students/bulk')
                ->with('success', "Bulk attendance saved: {$count} records for " . count($request->student_ids) . " students from {$dateFrom->format('d M Y')} to {$dateTo->format('d M Y')}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save bulk attendance: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Bulk update attendance status
     */
    public function bulkUpdate(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'attendance_ids' => 'required|array',
            'attendance_ids.*' => 'exists:student_attendance,id',
            'status' => 'required|in:present,absent,late,half_day,on_leave',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $updated = StudentAttendance::forTenant($tenant->id)
                ->whereIn('id', $request->attendance_ids)
                ->update([
                    'status' => $request->status,
                    'marked_by' => auth()->id(),
                    'marked_at' => now(),
                ]);

            // Recalculate summaries for affected students
            $studentIds = StudentAttendance::forTenant($tenant->id)
                ->whereIn('id', $request->attendance_ids)
                ->pluck('student_id')
                ->unique();

            foreach ($studentIds as $studentId) {
                $attendance = StudentAttendance::forTenant($tenant->id)
                    ->where('student_id', $studentId)
                    ->first();
                if ($attendance) {
                    $month = Carbon::parse($attendance->attendance_date)->month;
                    $year = Carbon::parse($attendance->attendance_date)->year;
                    AttendanceSummary::calculateSummary($tenant->id, 'student', $studentId, $month, $year);
                }
            }

            DB::commit();

            return back()->with('success', "Updated {$updated} attendance record(s) to " . ucfirst($request->status));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update attendance: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * API: Mark attendance (for biometric/QR systems)
     */
    public function apiMark(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,half_day,on_leave',
            'period_number' => 'nullable|integer|min:1|max:10',
            'subject_id' => 'nullable|exists:subjects,id',
            'remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            $student = Student::forTenant($tenant->id)->findOrFail($request->student_id);
            $enrollment = $student->currentEnrollment;

            if (!$enrollment) {
                return response()->json(['error' => 'Student not enrolled in any class'], 422);
            }

            $attendance = StudentAttendance::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'student_id' => $request->student_id,
                    'attendance_date' => $request->date,
                    'period_number' => $request->period_number ?? null,
                ],
                [
                    'class_id' => $enrollment->class_id,
                    'section_id' => $enrollment->section_id,
                    'status' => $request->status,
                    'subject_id' => $request->subject_id ?? null,
                    'remarks' => $request->remarks ?? null,
                    'marked_by' => auth()->id() ?? null,
                    'marked_at' => now(),
                ]
            );

            // Update summary
            $month = Carbon::parse($request->date)->month;
            $year = Carbon::parse($request->date)->year;
            AttendanceSummary::calculateSummary($tenant->id, 'student', $request->student_id, $month, $year);

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully',
                'data' => [
                    'id' => $attendance->id,
                    'student_id' => $attendance->student_id,
                    'date' => $attendance->attendance_date->format('Y-m-d'),
                    'status' => $attendance->status,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to mark attendance: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: Bulk mark attendance (for biometric/QR systems)
     */
    public function apiMarkBulk(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'attendance' => 'required|array',
            'attendance.*.student_id' => 'required|exists:students,id',
            'attendance.*.date' => 'required|date',
            'attendance.*.status' => 'required|in:present,absent,late,half_day,on_leave',
            'attendance.*.period_number' => 'nullable|integer|min:1|max:10',
            'attendance.*.subject_id' => 'nullable|exists:subjects,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        try {
            DB::beginTransaction();

            $marked = [];
            $errors = [];

            foreach ($request->attendance as $record) {
                try {
                    $student = Student::forTenant($tenant->id)->find($record['student_id']);
                    if (!$student) {
                        $errors[] = "Student {$record['student_id']} not found";
                        continue;
                    }

                    $enrollment = $student->currentEnrollment;
                    if (!$enrollment) {
                        $errors[] = "Student {$record['student_id']} not enrolled";
                        continue;
                    }

                    $attendance = StudentAttendance::updateOrCreate(
                        [
                            'tenant_id' => $tenant->id,
                            'student_id' => $record['student_id'],
                            'attendance_date' => $record['date'],
                            'period_number' => $record['period_number'] ?? null,
                        ],
                        [
                            'class_id' => $enrollment->class_id,
                            'section_id' => $enrollment->section_id,
                            'status' => $record['status'],
                            'subject_id' => $record['subject_id'] ?? null,
                            'marked_by' => auth()->id() ?? null,
                            'marked_at' => now(),
                        ]
                    );

                    $marked[] = $attendance->id;

                    // Update summary
                    $month = Carbon::parse($record['date'])->month;
                    $year = Carbon::parse($record['date'])->year;
                    AttendanceSummary::calculateSummary($tenant->id, 'student', $record['student_id'], $month, $year);

                } catch (\Exception $e) {
                    $errors[] = "Student {$record['student_id']}: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bulk attendance marked',
                'data' => [
                    'marked' => count($marked),
                    'errors' => count($errors),
                    'error_details' => $errors,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to mark bulk attendance: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API: Get attendance status for a student
     */
    public function apiStatus(Request $request, $studentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return response()->json(['error' => 'Tenant not found'], 404);
        }

        $date = $request->get('date', now()->format('Y-m-d'));
        $periodNumber = $request->get('period_number');

        $student = Student::forTenant($tenant->id)->findOrFail($studentId);

        $query = StudentAttendance::forTenant($tenant->id)
            ->where('student_id', $studentId)
            ->where('attendance_date', $date);

        if ($periodNumber) {
            $query->where('period_number', $periodNumber);
        } else {
            $query->whereNull('period_number');
        }

        $attendance = $query->first();

        if (!$attendance) {
            return response()->json([
                'status' => 'not_marked',
                'message' => 'Attendance not marked for this date'
            ], 200);
        }

        return response()->json([
            'status' => 'marked',
            'data' => [
                'id' => $attendance->id,
                'student_id' => $attendance->student_id,
                'date' => $attendance->attendance_date->format('Y-m-d'),
                'status' => $attendance->status,
                'period_number' => $attendance->period_number,
                'subject_id' => $attendance->subject_id,
                'marked_at' => $attendance->marked_at?->toISOString(),
            ]
        ], 200);
    }
}

