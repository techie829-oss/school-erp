<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeePlan;
use App\Models\FeePlanItem;
use App\Models\FeeComponent;
use App\Models\SchoolClass;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FeePlanController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }
    /**
     * Display a listing of fee plans
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $query = FeePlan::forTenant($tenant->id)
            ->with(['schoolClass', 'feePlanItems.feeComponent']);

        // Filter by academic year
        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $plans = $query->orderBy('academic_year', 'desc')
            ->orderBy('class_id', 'asc')
            ->paginate(20);

        $classes = SchoolClass::forTenant($tenant->id)->orderBy('name')->get();
        $years = FeePlan::forTenant($tenant->id)
            ->select('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

        return view('tenant.admin.fees.plans.index', compact('plans', 'classes', 'years'));
    }

    /**
     * Show the form for creating a new fee plan
     */
    public function create(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        $classes = SchoolClass::forTenant($tenant->id)->orderBy('name')->get();
        $components = FeeComponent::forTenant($tenant->id)->active()->get();

        return view('tenant.admin.fees.plans.create', compact('classes', 'components'));
    }

    /**
     * Store a newly created fee plan
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'academic_year' => 'required|string|max:20',
            'class_id' => 'required|exists:school_classes,id',
            'term' => 'required|in:annual,semester_1,semester_2,quarterly_1,quarterly_2,quarterly_3,quarterly_4',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'components' => 'required|array|min:1',
            'components.*.fee_component_id' => 'required|exists:fee_components,id',
            'components.*.amount' => 'required|numeric|min:0',
            'components.*.is_mandatory' => 'boolean',
            'components.*.due_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Create fee plan
            $plan = FeePlan::create([
                'tenant_id' => $tenant->id,
                'academic_year' => $request->academic_year,
                'class_id' => $request->class_id,
                'term' => $request->term,
                'effective_from' => $request->effective_from,
                'effective_to' => $request->effective_to,
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            // Create fee plan items
            foreach ($request->components as $component) {
                FeePlanItem::create([
                    'fee_plan_id' => $plan->id,
                    'fee_component_id' => $component['fee_component_id'],
                    'amount' => $component['amount'],
                    'is_mandatory' => isset($component['is_mandatory']) ? 1 : 0,
                    'due_date' => $component['due_date'] ?? null,
                ]);
            }

            DB::commit();
            return redirect('/admin/fees/plans')
                ->with('success', 'Fee plan created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create fee plan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified fee plan
     */
    public function show(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        $plan = FeePlan::forTenant($tenant->id)
            ->with(['schoolClass', 'feePlanItems.feeComponent', 'studentFeeCards.student'])
            ->findOrFail($id);

        return view('tenant.admin.fees.plans.show', compact('plan'));
    }

    /**
     * Show the form for editing the fee plan
     */
    public function edit(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        $plan = FeePlan::forTenant($tenant->id)
            ->with('feePlanItems.feeComponent')
            ->findOrFail($id);

        $classes = SchoolClass::forTenant($tenant->id)->orderBy('name')->get();
        $components = FeeComponent::forTenant($tenant->id)->active()->get();

        return view('tenant.admin.fees.plans.edit', compact('plan', 'classes', 'components'));
    }

    /**
     * Update the specified fee plan
     */
    public function update(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        $plan = FeePlan::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'academic_year' => 'required|string|max:20',
            'class_id' => 'required|exists:school_classes,id',
            'term' => 'required|in:annual,semester_1,semester_2,quarterly_1,quarterly_2,quarterly_3,quarterly_4',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after:effective_from',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'components' => 'required|array|min:1',
            'components.*.fee_component_id' => 'required|exists:fee_components,id',
            'components.*.amount' => 'required|numeric|min:0',
            'components.*.is_mandatory' => 'boolean',
            'components.*.due_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Update fee plan
            $plan->update([
                'academic_year' => $request->academic_year,
                'class_id' => $request->class_id,
                'term' => $request->term,
                'effective_from' => $request->effective_from,
                'effective_to' => $request->effective_to,
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            // Delete existing items and recreate
            $plan->feePlanItems()->delete();

            foreach ($request->components as $component) {
                FeePlanItem::create([
                    'fee_plan_id' => $plan->id,
                    'fee_component_id' => $component['fee_component_id'],
                    'amount' => $component['amount'],
                    'is_mandatory' => isset($component['is_mandatory']) ? 1 : 0,
                    'due_date' => $component['due_date'] ?? null,
                ]);
            }

            DB::commit();
            return redirect('/admin/fees/plans')
                ->with('success', 'Fee plan updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update fee plan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified fee plan
     */
    public function destroy(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        $plan = FeePlan::forTenant($tenant->id)->findOrFail($id);

        // Check if plan is assigned to students
        if ($plan->studentFeeCards()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete fee plan that is assigned to students!');
        }

        DB::beginTransaction();
        try {
            $plan->feePlanItems()->delete();
            $plan->delete();

            DB::commit();
            return redirect('/admin/fees/plans')
                ->with('success', 'Fee plan deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete fee plan: ' . $e->getMessage());
        }
    }

    /**
     * Assign plan to students
     */
    public function assign(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        $plan = FeePlan::forTenant($tenant->id)
            ->with(['schoolClass', 'feePlanItems.feeComponent'])
            ->findOrFail($id);

        return view('tenant.admin.fees.plans.assign', compact('plan'));
    }
}
