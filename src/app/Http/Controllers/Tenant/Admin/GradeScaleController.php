<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeScale;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GradeScaleController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of grade scales
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $query = GradeScale::forTenant($tenant->id);

        // Filter by status
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        // Filter by pass/fail
        if ($request->has('is_pass') && $request->is_pass !== '') {
            $query->where('is_pass', $request->is_pass);
        }

        $gradeScales = $query->ordered()->paginate(20)->withQueryString();

        return view('tenant.admin.examinations.grade-scales.index', compact('gradeScales', 'tenant'));
    }

    /**
     * Show the form for creating a new grade scale
     */
    public function create(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        return view('tenant.admin.examinations.grade-scales.create', compact('tenant'));
    }

    /**
     * Store a newly created grade scale in storage
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'grade_name' => 'required|string|max:10|unique:grade_scales,grade_name,NULL,id,tenant_id,' . $tenant->id,
            'min_percentage' => 'required|numeric|min:0|max:100',
            'max_percentage' => 'required|numeric|min:0|max:100|gte:min_percentage',
            'gpa_value' => 'nullable|numeric|min:0|max:10',
            'description' => 'nullable|string|max:255',
            'is_pass' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            GradeScale::create([
                'tenant_id' => $tenant->id,
                'grade_name' => strtoupper($request->grade_name),
                'min_percentage' => $request->min_percentage,
                'max_percentage' => $request->max_percentage,
                'gpa_value' => $request->gpa_value,
                'description' => $request->description,
                'is_pass' => $request->has('is_pass') ? $request->is_pass : true,
                'order' => $request->order ?? 0,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            return redirect('/admin/examinations/grade-scales')
                ->with('success', 'Grade scale created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create grade scale: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Show the form for editing the specified grade scale
     */
    public function edit(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $gradeScale = GradeScale::forTenant($tenant->id)->findOrFail($id);

        return view('tenant.admin.examinations.grade-scales.edit', compact('gradeScale', 'tenant'));
    }

    /**
     * Update the specified grade scale in storage
     */
    public function update(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $gradeScale = GradeScale::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'grade_name' => 'required|string|max:10|unique:grade_scales,grade_name,' . $id . ',id,tenant_id,' . $tenant->id,
            'min_percentage' => 'required|numeric|min:0|max:100',
            'max_percentage' => 'required|numeric|min:0|max:100|gte:min_percentage',
            'gpa_value' => 'nullable|numeric|min:0|max:10',
            'description' => 'nullable|string|max:255',
            'is_pass' => 'boolean',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $gradeScale->update([
                'grade_name' => strtoupper($request->grade_name),
                'min_percentage' => $request->min_percentage,
                'max_percentage' => $request->max_percentage,
                'gpa_value' => $request->gpa_value,
                'description' => $request->description,
                'is_pass' => $request->has('is_pass') ? $request->is_pass : true,
                'order' => $request->order ?? 0,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            return redirect('/admin/examinations/grade-scales')
                ->with('success', 'Grade scale updated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update grade scale: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified grade scale from storage
     */
    public function destroy(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $gradeScale = GradeScale::forTenant($tenant->id)->findOrFail($id);

        try {
            // Check if grade scale is being used in results
            // This would require checking exam_results table
            // For now, we'll just delete if it's not active
            if ($gradeScale->is_active) {
                return back()->with('error', 'Cannot delete active grade scale. Please deactivate it first.');
            }

            $gradeScale->delete();

            return redirect('/admin/examinations/grade-scales')
                ->with('success', 'Grade scale deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete grade scale: ' . $e->getMessage());
        }
    }
}

