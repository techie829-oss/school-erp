<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Services\TenantContextService;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    /**
     * Display a listing of classes
     */
    public function index(Request $request)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $query = SchoolClass::forTenant($tenant->id)->withCount('sections');

        // Search
        if ($request->filled('search')) {
            $query->where('class_name', 'like', '%' . $request->search . '%');
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('class_type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active');
        }

        $classes = $query->ordered()->paginate(20);

        return view('tenant.admin.classes.index', compact('classes', 'tenant'));
    }

    /**
     * Show the form for creating a new class
     */
    public function create(Request $request)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        return view('tenant.admin.classes.create', compact('tenant'));
    }

    /**
     * Store a newly created class
     */
    public function store(Request $request)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $validated = $request->validate([
            'class_name' => 'required|string|max:255',
            'class_numeric' => 'required|integer|min:1|max:20',
            'class_type' => 'required|in:school,college,both',
            'is_active' => 'boolean',
        ]);

        // Check if class numeric already exists for this tenant
        $exists = SchoolClass::forTenant($tenant->id)
            ->where('class_numeric', $validated['class_numeric'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['class_numeric' => 'This class number already exists.'])->withInput();
        }

        SchoolClass::create([
            'tenant_id' => $tenant->id,
            'class_name' => $validated['class_name'],
            'class_numeric' => $validated['class_numeric'],
            'class_type' => $validated['class_type'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect(url('/admin/classes'))->with('success', 'Class created successfully!');
    }

    /**
     * Display the specified class
     */
    public function show(Request $request, $id)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $class = SchoolClass::forTenant($tenant->id)
            ->with(['sections.classTeacher', 'enrollments'])
            ->findOrFail($id);

        // Get statistics
        $stats = [
            'total_sections' => $class->sections->count(),
            'active_sections' => $class->sections->where('is_active', true)->count(),
            'total_students' => $class->enrollments()->where('is_current', true)->count(),
            'total_capacity' => $class->sections->sum('capacity'),
        ];

        return view('tenant.admin.classes.show', compact('class', 'tenant', 'stats'));
    }

    /**
     * Show the form for editing the specified class
     */
    public function edit(Request $request, $id)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $class = SchoolClass::forTenant($tenant->id)->findOrFail($id);

        return view('tenant.admin.classes.edit', compact('class', 'tenant'));
    }

    /**
     * Update the specified class
     */
    public function update(Request $request, $id)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $class = SchoolClass::forTenant($tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'class_name' => 'required|string|max:255',
            'class_numeric' => 'required|integer|min:1|max:20',
            'class_type' => 'required|in:school,college,both',
            'is_active' => 'boolean',
        ]);

        // Check if class numeric already exists for another class
        $exists = SchoolClass::forTenant($tenant->id)
            ->where('class_numeric', $validated['class_numeric'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['class_numeric' => 'This class number already exists.'])->withInput();
        }

        $class->update([
            'class_name' => $validated['class_name'],
            'class_numeric' => $validated['class_numeric'],
            'class_type' => $validated['class_type'],
            'is_active' => $validated['is_active'] ?? $class->is_active,
        ]);

        return redirect(url('/admin/classes'))->with('success', 'Class updated successfully!');
    }

    /**
     * Remove the specified class
     */
    public function destroy(Request $request, $id)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $class = SchoolClass::forTenant($tenant->id)->findOrFail($id);

        // Check if class has active enrollments
        $hasActiveEnrollments = $class->enrollments()->where('is_current', true)->exists();

        if ($hasActiveEnrollments) {
            return back()->with('error', 'Cannot delete class with active student enrollments. Please transfer students first.');
        }

        // Check if class has sections
        if ($class->sections()->exists()) {
            return back()->with('error', 'Cannot delete class with sections. Please delete sections first.');
        }

        $class->delete();

        return redirect(url('/admin/classes'))->with('success', 'Class deleted successfully!');
    }
}
