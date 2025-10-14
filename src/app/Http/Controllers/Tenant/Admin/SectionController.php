<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\TenantContextService;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    /**
     * Display a listing of sections
     */
    public function index(Request $request)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $query = Section::forTenant($tenant->id)->with(['schoolClass', 'classTeacher']);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('section_name', 'like', '%' . $request->search . '%')
                  ->orWhere('room_number', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('is_active', $request->status == 'active');
        }

        $sections = $query->paginate(20);

        // Get classes for filter
        $classes = SchoolClass::forTenant($tenant->id)->ordered()->get();

        return view('tenant.admin.sections.index', compact('sections', 'classes', 'tenant'));
    }

    /**
     * Show the form for creating a new section
     */
    public function create(Request $request)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $teachers = User::forTenant($tenant->id)
            ->where('user_type', 'teacher')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.admin.sections.create', compact('tenant', 'classes', 'teachers'));
    }

    /**
     * Store a newly created section
     */
    public function store(Request $request)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_name' => 'required|string|max:10',
            'capacity' => 'nullable|integer|min:1|max:200',
            'room_number' => 'nullable|string|max:50',
            'class_teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        // Verify class belongs to tenant
        $class = SchoolClass::forTenant($tenant->id)->findOrFail($validated['class_id']);

        // Check if section already exists for this class
        $exists = Section::forTenant($tenant->id)
            ->where('class_id', $validated['class_id'])
            ->where('section_name', $validated['section_name'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['section_name' => 'This section already exists for the selected class.'])->withInput();
        }

        Section::create([
            'tenant_id' => $tenant->id,
            'class_id' => $validated['class_id'],
            'section_name' => $validated['section_name'],
            'capacity' => $validated['capacity'],
            'room_number' => $validated['room_number'],
            'class_teacher_id' => $validated['class_teacher_id'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect(url('/admin/sections'))->with('success', 'Section created successfully!');
    }

    /**
     * Display the specified section
     */
    public function show(Request $request, $id)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $section = Section::forTenant($tenant->id)
            ->with(['schoolClass', 'classTeacher', 'enrollments.student'])
            ->findOrFail($id);

        // Get current students in this section
        $students = $section->enrollments()
            ->where('is_current', true)
            ->with('student')
            ->get()
            ->pluck('student');

        $stats = [
            'total_students' => $students->count(),
            'capacity' => $section->capacity,
            'available_seats' => $section->capacity ? ($section->capacity - $students->count()) : null,
            'utilization' => $section->capacity ? round(($students->count() / $section->capacity) * 100, 1) : null,
        ];

        return view('tenant.admin.sections.show', compact('section', 'students', 'stats', 'tenant'));
    }

    /**
     * Show the form for editing the specified section
     */
    public function edit(Request $request, $id)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $section = Section::forTenant($tenant->id)->findOrFail($id);
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $teachers = User::forTenant($tenant->id)
            ->where('user_type', 'teacher')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('tenant.admin.sections.edit', compact('section', 'classes', 'teachers', 'tenant'));
    }

    /**
     * Update the specified section
     */
    public function update(Request $request, $id)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $section = Section::forTenant($tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_name' => 'required|string|max:10',
            'capacity' => 'nullable|integer|min:1|max:200',
            'room_number' => 'nullable|string|max:50',
            'class_teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'boolean',
        ]);

        // Verify class belongs to tenant
        $class = SchoolClass::forTenant($tenant->id)->findOrFail($validated['class_id']);

        // Check if section already exists for another section
        $exists = Section::forTenant($tenant->id)
            ->where('class_id', $validated['class_id'])
            ->where('section_name', $validated['section_name'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['section_name' => 'This section already exists for the selected class.'])->withInput();
        }

        $section->update([
            'class_id' => $validated['class_id'],
            'section_name' => $validated['section_name'],
            'capacity' => $validated['capacity'],
            'room_number' => $validated['room_number'],
            'class_teacher_id' => $validated['class_teacher_id'],
            'is_active' => $validated['is_active'] ?? $section->is_active,
        ]);

        return redirect(url('/admin/sections'))->with('success', 'Section updated successfully!');
    }

    /**
     * Remove the specified section
     */
    public function destroy(Request $request, $id)
    {
        $tenant = TenantContextService::getCurrentTenant();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $section = Section::forTenant($tenant->id)->findOrFail($id);

        // Check if section has active enrollments
        $hasActiveEnrollments = $section->enrollments()->where('is_current', true)->exists();

        if ($hasActiveEnrollments) {
            return back()->with('error', 'Cannot delete section with active student enrollments. Please transfer students first.');
        }

        $section->delete();

        return redirect(url('/admin/sections'))->with('success', 'Section deleted successfully!');
    }
}
