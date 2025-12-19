<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassEnrollment;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClassController extends Controller
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
        // Try to get tenant from request attributes (set by middleware) first
        $tenant = $request->attributes->get('current_tenant');

        // Fallback to getting from service
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        return $tenant;
    }

    /**
     * Display a listing of classes
     */
    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);

        $query = SchoolClass::forTenant($tenant->id)
            ->withCount([
                'sections',
                'sections as active_sections_count' => function ($q) {
                    $q->where('is_active', true);
                },
                'subjects as common_subjects_count',
                'enrollments as total_students_count' => function ($q) {
                    $q->where('is_current', true);
                }
            ])
            ->with(['classTeacher.department', 'subjects']);

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

        // Get summary statistics
        $stats = [
            'total_classes' => SchoolClass::forTenant($tenant->id)->count(),
            'active_classes' => SchoolClass::forTenant($tenant->id)->where('is_active', true)->count(),
            'total_sections' => SchoolClass::forTenant($tenant->id)->withCount('sections')->get()->sum('sections_count'),
            'total_students' => ClassEnrollment::forTenant($tenant->id)->where('is_current', true)->count(),
        ];

        return view('tenant.admin.classes.index', compact('classes', 'tenant', 'stats'));
    }

    /**
     * Show the form for creating a new class
     */
    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        // Load all active teachers (not filtered by department, as teachers can work in multiple departments)
        // Classes use teachers table directly (teacher->id), so all teachers can be assigned even without user accounts
        $teachers = \App\Models\Teacher::forTenant($tenant->id)
            ->with('department')
            ->where('is_active', true)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        // Get classes where each teacher is already assigned as class teacher
        $teacherClassAssignments = [];
        foreach ($teachers as $teacher) {
            $assignedClasses = SchoolClass::forTenant($tenant->id)
                ->where('class_teacher_id', $teacher->id)
                ->pluck('class_name')
                ->toArray();
            $teacherClassAssignments[$teacher->id] = $assignedClasses;
        }

        return view('tenant.admin.classes.create', compact('tenant', 'teachers'));
    }

    /**
     * Store a newly created class
     */
    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validated = $request->validate([
            'class_name' => 'required|string|max:255',
            'class_numeric' => 'required|integer|min:0|max:20',
            'class_type' => 'required|in:school,college,both',
            'has_sections' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // Check if class numeric already exists for this tenant
        // Allow multiple classes with class_numeric = 0 (for pre-primary classes like NC, KG)
        // But enforce uniqueness for class_numeric 1-20
        if ($validated['class_numeric'] != 0) {
            $exists = SchoolClass::forTenant($tenant->id)
                ->where('class_numeric', $validated['class_numeric'])
                ->exists();

            if ($exists) {
                return back()->withErrors(['class_numeric' => 'This class number already exists.'])->withInput();
            }
        }

        SchoolClass::create([
            'tenant_id' => $tenant->id,
            'class_name' => $validated['class_name'],
            'class_numeric' => $validated['class_numeric'],
            'class_type' => $validated['class_type'],
            'has_sections' => $validated['has_sections'] ?? false,
            'capacity' => $request->has('has_sections') && $request->has_sections ? null : ($validated['capacity'] ?? null),
            'room_number' => $request->has('has_sections') && $request->has_sections ? null : ($validated['room_number'] ?? null),
            'class_teacher_id' => $request->has('has_sections') && $request->has_sections ? null : ($validated['class_teacher_id'] ?? null),
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect(url('/admin/classes'))->with('success', 'Class created successfully!');
    }

    /**
     * Display the specified class
     */
    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $class = SchoolClass::forTenant($tenant->id)
            ->with([
                'sections.classTeacher.department',
                'sections.subjects',
                'sections.enrollments' => function($q) {
                    $q->where('is_current', true);
                },
                'enrollments',
                'subjects',
                'classTeacher.department'
            ])
            ->findOrFail($id);

        // Get statistics
        $stats = [
            'total_sections' => $class->sections->count(),
            'active_sections' => $class->sections->where('is_active', true)->count(),
            'total_students' => $class->enrollments()->where('is_current', true)->count(),
            'total_capacity' => $class->sections->sum('capacity'),
            'available_seats' => $class->sections->sum('capacity') - $class->enrollments()->where('is_current', true)->count(),
        ];

        // Get exams for this class with optimized queries
        $exams = \App\Models\Exam::forTenant($tenant->id)
            ->whereHas('examSchedules', function($q) use ($class) {
                $q->where('class_id', $class->id);
            })
            ->withCount(['examSchedules' => function($q) use ($class) {
                $q->where('class_id', $class->id);
            }])
            ->orderBy('start_date', 'desc')
            ->get();

        // Get total students for this class (once)
        $totalStudents = \App\Models\ClassEnrollment::forTenant($tenant->id)
            ->where('class_id', $class->id)
            ->where('is_current', true)
            ->distinct('student_id')
            ->count('student_id');

        // Batch query for results per exam
        $examIds = $exams->pluck('id');
        $resultsByExam = \App\Models\ExamResult::forTenant($tenant->id)
            ->whereIn('exam_id', $examIds)
            ->where('class_id', $class->id)
            ->selectRaw('exam_id, COUNT(DISTINCT student_id) as students_with_results')
            ->groupBy('exam_id')
            ->pluck('students_with_results', 'exam_id');

        // Calculate stats for each exam
        foreach ($exams as $exam) {
            $studentsWithResults = $resultsByExam->get($exam->id, 0);

            $exam->class_stats = [
                'total_schedules' => $exam->exam_schedules_count,
                'total_students' => $totalStudents,
                'students_with_results' => $studentsWithResults,
                'results_progress' => $totalStudents > 0 ? round(($studentsWithResults / $totalStudents) * 100, 1) : 0,
            ];
        }

        return view('tenant.admin.classes.show', compact('class', 'tenant', 'stats', 'exams'));
    }

    /**
     * Show the form for editing the specified class
     */
    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $class = SchoolClass::forTenant($tenant->id)
            ->with('sections')
            ->findOrFail($id);

        // Load all active teachers (not filtered by department, as teachers can work in multiple departments)
        // Classes use teachers table directly (teacher->id), so all teachers can be assigned even without user accounts
        $teachers = \App\Models\Teacher::forTenant($tenant->id)
            ->with('department')
            ->where('is_active', true)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        $subjects = Subject::forTenant($tenant->id)->active()->orderBy('subject_name')->get();
        $assignedSubjectIds = $class->allSubjects()->pluck('subjects.id')->toArray();

        // Use database value, but if sections exist, it should be true
        $hasSections = $class->has_sections || $class->sections->count() > 0;

        // Get classes where each teacher is already assigned as class teacher
        $teacherClassAssignments = [];
        foreach ($teachers as $teacher) {
            $assignedClasses = SchoolClass::forTenant($tenant->id)
                ->where('class_teacher_id', $teacher->id)
                ->where('id', '!=', $class->id) // Exclude current class
                ->pluck('class_name')
                ->toArray();
            $teacherClassAssignments[$teacher->id] = $assignedClasses;
        }

        return view('tenant.admin.classes.edit', compact('class', 'tenant', 'teachers', 'hasSections', 'subjects', 'assignedSubjectIds', 'teacherClassAssignments'));
    }

    /**
     * Update the specified class
     */
    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $class = SchoolClass::forTenant($tenant->id)->findOrFail($id);

        $validated = $request->validate([
            'class_name' => 'required|string|max:255',
            'class_numeric' => 'required|integer|min:0|max:20',
            'class_type' => 'required|in:school,college,both',
            'has_sections' => 'boolean',
            'capacity' => 'nullable|integer|min:1|max:200',
            'room_number' => 'nullable|string|max:50',
            'class_teacher_id' => 'nullable|exists:teachers,id',
            'is_active' => 'boolean',
        ]);

        // Check if class numeric already exists for another class
        // Allow multiple classes with class_numeric = 0 (for pre-primary classes like NC, KG)
        // But enforce uniqueness for class_numeric 1-20
        if ($validated['class_numeric'] != 0) {
            $exists = SchoolClass::forTenant($tenant->id)
                ->where('class_numeric', $validated['class_numeric'])
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return back()->withErrors(['class_numeric' => 'This class number already exists.'])->withInput();
            }
        }

        $hasSections = $validated['has_sections'] ?? false;

        $class->update([
            'class_name' => $validated['class_name'],
            'class_numeric' => $validated['class_numeric'],
            'class_type' => $validated['class_type'],
            'has_sections' => $hasSections,
            'capacity' => $hasSections ? null : ($validated['capacity'] ?? null),
            'room_number' => $hasSections ? null : ($validated['room_number'] ?? null),
            'class_teacher_id' => $hasSections ? null : ($validated['class_teacher_id'] ?? null),
            'is_active' => $validated['is_active'] ?? $class->is_active,
        ]);

        // Update subjects if provided
        if ($request->has('subjects')) {
            $subjectIds = $request->input('subjects', []);

            // Sync subjects with is_active = true for selected subjects
            $syncData = [];
            foreach ($subjectIds as $subjectId) {
                $syncData[$subjectId] = ['is_active' => true, 'tenant_id' => $tenant->id];
            }

            // Get current assignments
            $currentAssignments = DB::table('class_subjects')
                ->where('class_id', $class->id)
                ->pluck('subject_id')
                ->toArray();

            // For subjects that were removed, set is_active = false instead of deleting
            $removedSubjects = array_diff($currentAssignments, $subjectIds);
            foreach ($removedSubjects as $removedId) {
                DB::table('class_subjects')
                    ->where('class_id', $class->id)
                    ->where('subject_id', $removedId)
                    ->update(['is_active' => false]);
            }

            // Sync new/active subjects
            $class->allSubjects()->sync($syncData, false);
        }

        return redirect(url('/admin/classes'))->with('success', 'Class updated successfully!');
    }

    /**
     * Remove the specified class
     */
    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

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

    /**
     * Get sections for a class (API endpoint)
     */
    public function getSections(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $class = SchoolClass::forTenant($tenant->id)->findOrFail($id);
        $sections = $class->sections()->active()->get(['id', 'section_name']);

        return response()->json($sections);
    }
}
