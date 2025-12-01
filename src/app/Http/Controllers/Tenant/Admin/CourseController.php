<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Services\LmsService;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    protected $lmsService;
    protected $tenantService;

    public function __construct(LmsService $lmsService, TenantService $tenantService)
    {
        $this->lmsService = $lmsService;
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        $query = Course::forTenant($tenant->id)
            ->with(['schoolClass', 'subject', 'teacher']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('course_name', 'like', "%{$search}%")
                  ->orWhere('course_code', 'like', "%{$search}%");
        }

        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $courses = $query->latest()->paginate(10);
        $classes = SchoolClass::forTenant($tenant->id)->get();

        return view('tenant.admin.lms.courses.index', compact('courses', 'classes', 'tenant'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);
        
        $classes = SchoolClass::forTenant($tenant->id)->get();
        $subjects = Subject::forTenant($tenant->id)->get();
        $teachers = Teacher::forTenant($tenant->id)->active()->get();

        return view('tenant.admin.lms.courses.create', compact('classes', 'subjects', 'teachers', 'tenant'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:50',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'description' => 'nullable|string',
            'course_image' => 'nullable|image|max:2048',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $this->lmsService->createCourse($tenant, $validated);

        return redirect(url('/admin/lms/courses'))
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Course $course)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);
        
        if ($course->tenant_id !== $tenant->id) {
            abort(403);
        }

        $course->load(['chapters.topics', 'schoolClass', 'subject', 'teacher']);

        return view('tenant.admin.lms.courses.show', compact('course', 'tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Course $course)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($course->tenant_id !== $tenant->id) {
            abort(403);
        }

        $classes = SchoolClass::forTenant($tenant->id)->get();
        $subjects = Subject::forTenant($tenant->id)->get();
        $teachers = Teacher::forTenant($tenant->id)->active()->get();

        return view('tenant.admin.lms.courses.edit', compact('course', 'classes', 'subjects', 'teachers', 'tenant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($course->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'course_name' => 'required|string|max:255',
            'course_code' => 'nullable|string|max:50',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:teachers,id',
            'description' => 'nullable|string',
            'course_image' => 'nullable|image|max:2048',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        $this->lmsService->updateCourse($course, $validated);

        return redirect(url('/admin/lms/courses'))
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Course $course)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if ($course->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($course->course_image) {
            Storage::disk('public')->delete($course->course_image);
        }

        $course->delete();

        return redirect(url('/admin/lms/courses'))
            ->with('success', 'Course deleted successfully.');
    }
}

