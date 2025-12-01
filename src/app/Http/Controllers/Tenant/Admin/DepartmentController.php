<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Teacher;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DepartmentController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of departments
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $query = Department::forTenant($tenant->id)
            ->with(['headTeacher'])
            ->withCount('teachers');

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('department_name', 'like', '%' . $request->search . '%')
                  ->orWhere('department_code', 'like', '%' . $request->search . '%');
        }

        // Filter by status
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $departments = $query->orderBy('department_name')->paginate(20)->withQueryString();

        return view('tenant.admin.departments.index', compact('departments', 'tenant'));
    }

    /**
     * Show the form for creating a new department
     */
    public function create(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $teachers = Teacher::forTenant($tenant->id)->active()->orderBy('full_name')->get();

        return view('tenant.admin.departments.create', compact('teachers', 'tenant'));
    }

    /**
     * Store a newly created department in storage
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'department_name' => 'required|string|max:100|unique:departments,department_name,NULL,id,tenant_id,' . $tenant->id,
            'department_code' => 'nullable|string|max:20|unique:departments,department_code,NULL,id,tenant_id,' . $tenant->id,
            'description' => 'nullable|string|max:1000',
            'head_teacher_id' => 'nullable|exists:teachers,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            Department::create([
                'tenant_id' => $tenant->id,
                'department_name' => $request->department_name,
                'department_code' => $request->department_code,
                'description' => $request->description,
                'head_teacher_id' => $request->head_teacher_id,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            return redirect(url('/admin/departments'))
                ->with('success', 'Department created successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to create department: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified department
     */
    public function show(Request $request, $departmentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $department = Department::with(['headTeacher', 'teachers'])
            ->withCount('teachers')
            ->findOrFail($departmentId);

        // Ensure department belongs to tenant
        if ($department->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        return view('tenant.admin.departments.show', compact('department', 'tenant'));
    }

    /**
     * Show the form for editing the specified department
     */
    public function edit(Request $request, $departmentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $department = Department::findOrFail($departmentId);

        // Ensure department belongs to tenant
        if ($department->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        $teachers = Teacher::forTenant($tenant->id)->active()->orderBy('full_name')->get();

        return view('tenant.admin.departments.edit', compact('department', 'teachers', 'tenant'));
    }

    /**
     * Update the specified department in storage
     */
    public function update(Request $request, $departmentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $department = Department::findOrFail($departmentId);

        // Ensure department belongs to tenant
        if ($department->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'department_name' => 'required|string|max:100|unique:departments,department_name,' . $department->id . ',id,tenant_id,' . $tenant->id,
            'department_code' => 'nullable|string|max:20|unique:departments,department_code,' . $department->id . ',id,tenant_id,' . $tenant->id,
            'description' => 'nullable|string|max:1000',
            'head_teacher_id' => 'nullable|exists:teachers,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $department->update([
                'department_name' => $request->department_name,
                'department_code' => $request->department_code,
                'description' => $request->description,
                'head_teacher_id' => $request->head_teacher_id,
                'is_active' => $request->has('is_active') ? $request->is_active : $department->is_active,
            ]);

            return redirect(url('/admin/departments'))
                ->with('success', 'Department updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to update department: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified department from storage
     */
    public function destroy(Request $request, $departmentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $department = Department::withCount('teachers')->findOrFail($departmentId);

        // Ensure department belongs to tenant
        if ($department->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        // Check if department has teachers
        if ($department->teachers_count > 0) {
            return back()->with('error', 'Cannot delete department with assigned teachers. Please reassign or remove teachers first.');
        }

        try {
            $department->delete();

            return redirect(url('/admin/departments'))
                ->with('success', 'Department deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete department: ' . $e->getMessage());
        }
    }
}

