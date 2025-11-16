<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeComponent;
use App\Models\FeePlan;
use App\Models\FeePlanItem;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentFeeCard;
use App\Models\StudentFeeItem;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

        $classes = SchoolClass::forTenant($tenant->id)->orderBy('class_name')->get();
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
        $classes = SchoolClass::forTenant($tenant->id)->orderBy('class_name')->get();
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
            ->with([
                'schoolClass',
                'feePlanItems.feeComponent',
                'studentFeeCards.student.currentEnrollment.section',
            ])
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

        $classes = SchoolClass::forTenant($tenant->id)->orderBy('class_name')->get();
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
            ->with(['schoolClass', 'feePlanItems.feeComponent', 'studentFeeCards.student.currentEnrollment'])
            ->findOrFail($id);

        // Auto-sync obvious academic year mismatches for already assigned fee cards
        $syncedCount = 0;
        foreach ($plan->studentFeeCards as $card) {
            $student = $card->student;
            $enrollment = $student?->currentEnrollment;

            if (!$student || !$enrollment) {
                continue;
            }

            // Only consider cards where current class matches plan class
            if ($enrollment->class_id !== $plan->class_id) {
                continue;
            }

            // If enrollment year and plan year match, but card year is different, fix the card
            if (
                $enrollment->academic_year === $plan->academic_year &&
                $card->academic_year !== $enrollment->academic_year
            ) {
                $card->academic_year = $enrollment->academic_year;
                $card->save();
                $syncedCount++;
            }
        }

        $students = Student::forTenant($tenant->id)
            ->with(['currentEnrollment.section'])
            ->whereHas('currentEnrollment', function ($query) use ($plan) {
                $query->where('class_id', $plan->class_id)
                    ->where('is_current', true);
            })
            ->orderBy('full_name')
            ->get();

        $assignedStudentIds = $plan->studentFeeCards->pluck('student_id')->toArray();

        return view('tenant.admin.fees.plans.assign', [
            'plan' => $plan,
            'students' => $students,
            'assignedStudentIds' => $assignedStudentIds,
            'syncedCount' => $syncedCount,
        ]);
    }

    /**
     * Persist fee plan assignments and generate student fee cards/items
     */
    public function assignStore(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $plan = FeePlan::forTenant($tenant->id)
            ->with('feePlanItems')
            ->findOrFail($id);

        $validated = Validator::make($request->all(), [
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'required|exists:students,id',
        ], [
            'student_ids.required' => 'Select at least one student to assign.',
        ]);

        if ($validated->fails()) {
            return redirect()->back()
                ->withErrors($validated)
                ->withInput();
        }

        $studentIds = $request->input('student_ids', []);

        $students = Student::forTenant($tenant->id)
            ->with('currentEnrollment')
            ->whereIn('id', $studentIds)
            ->get();

        $createdCount = 0;
        $skipped = [];

        DB::beginTransaction();
        try {
            $totalAmount = $plan->feePlanItems->sum('amount');

            foreach ($students as $student) {
                $currentEnrollment = $student->currentEnrollment;

                // Ensure student is currently in the same CLASS as the fee plan.
                // We no longer block on academic_year here; the fee card always uses the plan's academic_year.
                if (
                    !$currentEnrollment ||
                    $currentEnrollment->class_id !== $plan->class_id
                ) {
                    $skipped[] = $student->full_name . ' (class mismatch)';
                    continue;
                }

                $alreadyAssigned = StudentFeeCard::forTenant($tenant->id)
                    ->where('student_id', $student->id)
                    ->where('fee_plan_id', $plan->id)
                    ->exists();

                if ($alreadyAssigned) {
                    $skipped[] = $student->full_name;
                    continue;
                }

                $feeCard = StudentFeeCard::create([
                    'tenant_id' => $tenant->id,
                    'student_id' => $student->id,
                    'fee_plan_id' => $plan->id,
                    'academic_year' => $plan->academic_year,
                    'total_amount' => $totalAmount,
                    'discount_amount' => 0,
                    'paid_amount' => 0,
                    'balance_amount' => $totalAmount,
                    'status' => 'active',
                ]);

                foreach ($plan->feePlanItems as $item) {
                    StudentFeeItem::create([
                        'student_fee_card_id' => $feeCard->id,
                        'fee_component_id' => $item->fee_component_id,
                        'original_amount' => $item->amount,
                        'discount_amount' => 0,
                        'net_amount' => $item->amount,
                        'due_date' => $item->due_date,
                        'paid_amount' => 0,
                        'status' => 'unpaid',
                    ]);
                }

                $createdCount++;
            }

            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Failed to assign fee plan: ' . $exception->getMessage());
        }

        $message = "{$createdCount} student(s) assigned successfully.";

        if (!empty($skipped)) {
            $message .= ' Skipped: ' . implode(', ', array_slice($skipped, 0, 3));
            if (count($skipped) > 3) {
                $message .= ' and others.';
            }
        }

        return redirect('/admin/fees/plans/' . $plan->id)
            ->with('success', $message);
    }

    /**
     * Printable summary of the fee plan
     */
    public function print(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $plan = FeePlan::forTenant($tenant->id)
            ->with([
                'schoolClass',
                'feePlanItems.feeComponent',
                'studentFeeCards.student.currentEnrollment.section',
            ])
            ->findOrFail($id);

        return view('tenant.admin.fees.plans.print', compact('plan', 'tenant'));
    }

    /**
     * Export assigned students to CSV
     */
    public function export(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $plan = FeePlan::forTenant($tenant->id)
            ->with(['studentFeeCards.student.currentEnrollment.section'])
            ->findOrFail($id);

        $filename = 'fee-plan-' . Str::slug($plan->name) . '-students.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $columns = ['Student Name', 'Admission No.', 'Roll No.', 'Section', 'Total', 'Paid', 'Balance', 'Status'];

        $callback = function () use ($columns, $plan) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);

            foreach ($plan->studentFeeCards as $card) {
                $student = $card->student;
                $enrollment = $student?->currentEnrollment;

                fputcsv($handle, [
                    $student->full_name ?? 'N/A',
                    $student->admission_number ?? '',
                    $enrollment?->roll_number ?? '',
                    $enrollment?->section?->section_name ?? '',
                    $card->total_amount,
                    $card->paid_amount,
                    $card->balance_amount,
                    ucfirst($card->status),
                ]);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}
