<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Services\TenantContextService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionController extends Controller
{
    /**
     * Display a listing of sections
     * Sections are now managed within classes, so this redirects to classes
     */
    public function index(Request $request)
    {
        return redirect()->route('classes.index');
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
        // Load all active teachers (not filtered by department, as teachers can work in multiple departments)
        $teachers = Teacher::forTenant($tenant->id)
            ->with('department')
            ->where('is_active', true)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        // Get sections and classes where each teacher is already assigned as class teacher
        $teacherAssignments = [];
        foreach ($teachers as $teacher) {
            $assignments = [];

            // Get sections where teacher is class teacher
            $assignedSections = Section::forTenant($tenant->id)
                ->where('class_teacher_id', $teacher->id)
                ->with('schoolClass')
                ->get();

            foreach ($assignedSections as $assignedSection) {
                $assignments[] = $assignedSection->schoolClass->class_name . ' - ' . $assignedSection->section_name;
            }

            // Get classes where teacher is class teacher (using teacher id)
            $assignedClasses = SchoolClass::forTenant($tenant->id)
                ->where('class_teacher_id', $teacher->id)
                ->pluck('class_name')
                ->toArray();

            foreach ($assignedClasses as $className) {
                $assignments[] = $className;
            }

            $teacherAssignments[$teacher->id] = $assignments;
        }

        return view('tenant.admin.sections.create', compact('tenant', 'classes', 'teachers', 'teacherAssignments'));
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
            'section_name' => 'required|string|max:25',
            'group_name' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1|max:200',
            'room_number' => 'nullable|string|max:50',
            'class_teacher_id' => 'nullable|exists:teachers,id',
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
            'group_name' => $validated['group_name'] ?? null,
            'capacity' => $validated['capacity'] ?? null, // Optional - can be null
            'room_number' => $validated['room_number'],
            'class_teacher_id' => $validated['class_teacher_id'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Redirect back to the class show page
        $class = SchoolClass::forTenant($tenant->id)->findOrFail($validated['class_id']);
        return redirect(url('/admin/classes/' . $class->id))->with('success', 'Section created successfully!');
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
        // Load all active teachers (not filtered by department, as teachers can work in multiple departments)
        $teachers = Teacher::forTenant($tenant->id)
            ->with('department')
            ->where('is_active', true)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        // Get sections and classes where each teacher is already assigned as class teacher
        $teacherAssignments = [];
        foreach ($teachers as $teacher) {
            $assignments = [];

            // Get sections where teacher is class teacher
            $assignedSections = Section::forTenant($tenant->id)
                ->where('class_teacher_id', $teacher->id)
                ->where('id', '!=', $section->id) // Exclude current section
                ->with('schoolClass')
                ->get();

            foreach ($assignedSections as $assignedSection) {
                $assignments[] = $assignedSection->schoolClass->class_name . ' - ' . $assignedSection->section_name;
            }

            // Get classes where teacher is class teacher (using teacher id)
            $assignedClasses = SchoolClass::forTenant($tenant->id)
                ->where('class_teacher_id', $teacher->id)
                ->pluck('class_name')
                ->toArray();

            foreach ($assignedClasses as $className) {
                $assignments[] = $className;
            }

            $teacherAssignments[$teacher->id] = $assignments;
        }

        $subjects = Subject::forTenant($tenant->id)->active()->orderBy('subject_name')->get();
        $assignedSubjectIds = $section->allSubjects()->pluck('subjects.id')->toArray();

        // Get subject assignment settings
        $academicSettings = \App\Models\TenantSetting::getAllForTenant($tenant->id, 'academic');
        $sectionSubjectMode = $academicSettings['section_subject_assignment_mode'] ?? 'section_wise';
        $allowSectionWiseAssignment = ($sectionSubjectMode === 'section_wise');

        return view('tenant.admin.sections.edit', compact('section', 'classes', 'teachers', 'tenant', 'subjects', 'assignedSubjectIds', 'teacherAssignments', 'allowSectionWiseAssignment', 'sectionSubjectMode'));
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
            'section_name' => 'required|string|max:25',
            'group_name' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1|max:200',
            'room_number' => 'nullable|string|max:50',
            'class_teacher_id' => 'nullable|exists:teachers,id',
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
            'group_name' => $validated['group_name'] ?? null,
            'capacity' => $validated['capacity'] ?? null, // Optional - can be null
            'room_number' => $validated['room_number'],
            'class_teacher_id' => $validated['class_teacher_id'],
            'is_active' => $validated['is_active'] ?? $section->is_active,
        ]);

        // Update subjects - always process to handle unassignment
        // When checkboxes are unchecked, they don't send values, so we need to handle both cases:
        // 1. subjects parameter exists (some or all checked)
        // 2. subjects parameter doesn't exist (all unchecked)
            $subjectIds = $request->input('subjects', []);

            // Sync subjects with is_active = true for selected subjects
            $syncData = [];
            foreach ($subjectIds as $subjectId) {
                $syncData[$subjectId] = ['is_active' => true, 'tenant_id' => $tenant->id];
            }

            // Get current assignments
            $currentAssignments = DB::table('section_subjects')
                ->where('section_id', $section->id)
                ->pluck('subject_id')
                ->toArray();

            // For subjects that were removed, set is_active = false instead of deleting
            $removedSubjects = array_diff($currentAssignments, $subjectIds);
            foreach ($removedSubjects as $removedId) {
                DB::table('section_subjects')
                    ->where('section_id', $section->id)
                    ->where('subject_id', $removedId)
                    ->update(['is_active' => false]);
            }

            // Sync new/active subjects
            $section->allSubjects()->sync($syncData, false);

        // Redirect back to the class show page
        $class = SchoolClass::forTenant($tenant->id)->findOrFail($validated['class_id']);
        return redirect(url('/admin/classes/' . $class->id))->with('success', 'Section updated successfully!');
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

        $classId = $section->class_id;
        $section->delete();

        // Redirect back to the class show page
        return redirect(url('/admin/classes/' . $classId))->with('success', 'Section deleted successfully!');
    }
}
