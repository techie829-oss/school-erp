<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamShift;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExamShiftController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Get the current tenant from request attributes or service
     */
    protected function getTenant(Request $request)
    {
        $tenant = $request->attributes->get('current_tenant');

        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        return $tenant;
    }

    /**
     * Display a listing of exam shifts
     */
    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);

        $shifts = ExamShift::forTenant($tenant->id)
            ->ordered()
            ->get();

        return view('tenant.admin.examinations.shifts.index', compact('shifts', 'tenant'));
    }

    /**
     * Show the form for creating a new exam shift
     */
    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);

        return view('tenant.admin.examinations.shifts.create', compact('tenant'));
    }

    /**
     * Store a newly created exam shift
     */
    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'shift_name' => 'required|string|max:255',
            'shift_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('exam_shifts', 'shift_code')->where('tenant_id', $tenant->id),
            ],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'class_ranges' => 'nullable|array',
            'class_ranges.*.min' => 'required|integer|min:0|max:20',
            'class_ranges.*.max' => 'required|integer|min:0|max:20|gte:class_ranges.*.min',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            $start = \Carbon\Carbon::parse($request->start_time);
            $end = \Carbon\Carbon::parse($request->end_time);
            $duration = $start->diffInMinutes($end);

            $shift = ExamShift::create([
                'tenant_id' => $tenant->id,
                'shift_name' => $request->shift_name,
                'shift_code' => $request->shift_code ?? strtoupper(str_replace(' ', '_', $request->shift_name)),
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration_minutes' => $duration,
                'class_ranges' => $request->class_ranges ?? null,
                'description' => $request->description,
                'display_order' => $request->display_order ?? 0,
                'is_active' => $request->is_active ?? true,
            ]);

            return redirect(url('/admin/examinations/shifts'))->with('success', 'Exam shift created successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to create exam shift: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified exam shift
     */
    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $shift = ExamShift::forTenant($tenant->id)->findOrFail($id);

        return view('tenant.admin.examinations.shifts.edit', compact('shift', 'tenant'));
    }

    /**
     * Update the specified exam shift
     */
    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $shift = ExamShift::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'shift_name' => 'required|string|max:255',
            'shift_code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('exam_shifts', 'shift_code')
                    ->where('tenant_id', $tenant->id)
                    ->ignore($shift->id),
            ],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'class_ranges' => 'nullable|array',
            'class_ranges.*.min' => 'required|integer|min:0|max:20',
            'class_ranges.*.max' => 'required|integer|min:0|max:20|gte:class_ranges.*.min',
            'description' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            $start = \Carbon\Carbon::parse($request->start_time);
            $end = \Carbon\Carbon::parse($request->end_time);
            $duration = $start->diffInMinutes($end);

            $shift->update([
                'shift_name' => $request->shift_name,
                'shift_code' => $request->shift_code ?? $shift->shift_code,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration_minutes' => $duration,
                'class_ranges' => $request->class_ranges ?? null,
                'description' => $request->description,
                'display_order' => $request->display_order ?? $shift->display_order,
                'is_active' => $request->is_active ?? $shift->is_active,
            ]);

            return redirect(url('/admin/examinations/shifts'))->with('success', 'Exam shift updated successfully!');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Failed to update exam shift: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified exam shift
     */
    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $shift = ExamShift::forTenant($tenant->id)->findOrFail($id);

        // Check if shift is used in any schedules
        if ($shift->examSchedules()->count() > 0) {
            return back()->with('error', 'Cannot delete shift that is used in exam schedules. Please remove schedules first.');
        }

        try {
            $shift->delete();
            return redirect(url('/admin/examinations/shifts'))->with('success', 'Exam shift deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete exam shift: ' . $e->getMessage());
        }
    }

    /**
     * Create example shifts based on common pattern
     */
    public function createExamplePattern(Request $request)
    {
        $tenant = $this->getTenant($request);

        try {
            DB::beginTransaction();

            // Create example shifts with generic names - users can edit these as needed
            // Shift 1: Morning shift example
            ExamShift::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'shift_code' => 'SHIFT_1',
                ],
                [
                    'shift_name' => 'Shift 1',
                    'start_time' => '09:00',
                    'end_time' => '12:00',
                    'duration_minutes' => 180,
                    'class_ranges' => null, // Let users configure class ranges as needed
                    'description' => 'Example morning shift - customize as needed',
                    'display_order' => 1,
                    'is_active' => true,
                ]
            );

            // Shift 2: Afternoon shift example
            ExamShift::firstOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'shift_code' => 'SHIFT_2',
                ],
                [
                    'shift_name' => 'Shift 2',
                    'start_time' => '13:00',
                    'end_time' => '16:00',
                    'duration_minutes' => 180,
                    'class_ranges' => null, // Let users configure class ranges as needed
                    'description' => 'Example afternoon shift - customize as needed',
                    'display_order' => 2,
                    'is_active' => true,
                ]
            );

            DB::commit();

            return redirect(url('/admin/examinations/shifts'))->with('success', 'Example shift pattern created successfully! You can now edit these shifts to match your school\'s requirements.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create example pattern: ' . $e->getMessage());
        }
    }
}
