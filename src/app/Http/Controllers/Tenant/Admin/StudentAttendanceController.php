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
}

