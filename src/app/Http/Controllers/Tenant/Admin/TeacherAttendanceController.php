<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\TeacherAttendance;
use App\Models\Teacher;
use App\Models\Department;
use App\Models\AttendanceSummary;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TeacherAttendanceController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display teacher attendance dashboard
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
        $departmentId = $request->get('department_id');

        // Get filter options
        $departments = Department::forTenant($tenant->id)->active()->get();

        // Get today's attendance stats
        $todayStats = $this->getTodayStats($tenant->id, $departmentId);

        // Get monthly attendance data
        $monthlyData = $this->getMonthlyData($tenant->id, $month, $year, $departmentId);

        return view('tenant.admin.attendance.teachers.index', compact(
            'tenant',
            'departments',
            'departmentId',
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
        $departmentId = $request->get('department_id');

        $departments = Department::forTenant($tenant->id)->active()->get();

        // Get all active teachers or filtered by department
        $query = Teacher::forTenant($tenant->id)->active()->with('department');

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $teachers = $query->orderBy('full_name')->get();

        // Get existing attendance for this date
        $existingAttendance = TeacherAttendance::forDate($date)
            ->whereIn('teacher_id', $teachers->pluck('id'))
            ->get()
            ->keyBy('teacher_id');

        return view('tenant.admin.attendance.teachers.mark', compact(
            'tenant',
            'departments',
            'teachers',
            'existingAttendance',
            'departmentId',
            'date'
        ));
    }

    /**
     * Save teacher attendance
     */
    public function save(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'attendance' => 'required|array|min:1',
            'attendance.*.teacher_id' => 'required|exists:teachers,id',
            'attendance.*.status' => 'required|in:present,absent,late,half_day,on_leave,holiday',
            'attendance.*.check_in_time' => 'nullable',
            'attendance.*.check_out_time' => 'nullable',
            'attendance.*.remarks' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            foreach ($request->attendance as $record) {
                // Calculate total hours if both times provided
                $totalHours = null;
                $checkInTime = !empty($record['check_in_time']) ? $record['check_in_time'] : null;
                $checkOutTime = !empty($record['check_out_time']) ? $record['check_out_time'] : null;

                if ($checkInTime && $checkOutTime) {
                    try {
                        $checkIn = Carbon::createFromFormat('H:i', $checkInTime);
                        $checkOut = Carbon::createFromFormat('H:i', $checkOutTime);
                        $totalHours = round($checkIn->diffInHours($checkOut, true), 2);
                    } catch (\Exception $e) {
                        // If time parsing fails, set to null
                        $totalHours = null;
                    }
                }

                // Clear times if status is absent or on_leave
                if (in_array($record['status'], ['absent', 'on_leave', 'holiday'])) {
                    $checkInTime = null;
                    $checkOutTime = null;
                    $totalHours = null;
                }

                TeacherAttendance::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'teacher_id' => $record['teacher_id'],
                        'attendance_date' => $request->date,
                    ],
                    [
                        'status' => $record['status'],
                        'check_in_time' => $checkInTime,
                        'check_out_time' => $checkOutTime,
                        'total_hours' => $totalHours,
                        'remarks' => isset($record['remarks']) ? $record['remarks'] : null,
                        'marked_by' => auth()->id(),
                        'marked_at' => now(),
                    ]
                );
            }

            // Update monthly summary for affected teachers
            $month = Carbon::parse($request->date)->month;
            $year = Carbon::parse($request->date)->year;

            foreach ($request->attendance as $record) {
                AttendanceSummary::calculateSummary(
                    $tenant->id,
                    'teacher',
                    $record['teacher_id'],
                    $month,
                    $year
                );
            }

            DB::commit();

            return redirect('/admin/attendance/teachers')
                ->with('success', 'Attendance marked successfully for ' . count($request->attendance) . ' teachers!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save attendance: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Get today's statistics
     */
    private function getTodayStats($tenantId, $departmentId = null)
    {
        $query = TeacherAttendance::where('tenant_id', $tenantId)
            ->forDate(now()->format('Y-m-d'));

        if ($departmentId) {
            $query->whereHas('teacher', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $total = $query->count();
        $present = (clone $query)->where('status', 'present')->count();
        $absent = (clone $query)->where('status', 'absent')->count();
        $late = (clone $query)->where('status', 'late')->count();
        $onLeave = (clone $query)->where('status', 'on_leave')->count();

        $percentage = $total > 0 ? round((($present + $late) / $total) * 100, 2) : 0;

        // Calculate average hours
        $avgHours = $query->whereNotNull('total_hours')->avg('total_hours');
        $avgHours = $avgHours ? round($avgHours, 2) : 0;

        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'on_leave' => $onLeave,
            'percentage' => $percentage,
            'avg_hours' => $avgHours,
        ];
    }

    /**
     * Get monthly attendance data
     */
    private function getMonthlyData($tenantId, $month, $year, $departmentId = null)
    {
        $query = TeacherAttendance::where('tenant_id', $tenantId)
            ->forMonth($month, $year);

        if ($departmentId) {
            $query->whereHas('teacher', function($q) use ($departmentId) {
                $q->where('department_id', $departmentId);
            });
        }

        $records = $query->get();

        return [
            'total_records' => $records->count(),
            'present' => $records->where('status', 'present')->count(),
            'absent' => $records->where('status', 'absent')->count(),
            'late' => $records->where('status', 'late')->count(),
            'on_leave' => $records->where('status', 'on_leave')->count(),
            'avg_hours' => $records->where('total_hours', '>', 0)->avg('total_hours'),
        ];
    }
}

