<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\SchoolClass;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ExamController extends Controller
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
     * Display a listing of exams
     */
    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);

        $query = Exam::forTenant($tenant->id)
            ->with(['schoolClass']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('exam_name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filter by exam type
        if ($request->has('exam_type') && $request->exam_type) {
            $query->where('exam_type', $request->exam_type);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by class
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by academic year
        if ($request->has('academic_year') && $request->academic_year) {
            $query->where('academic_year', $request->academic_year);
        }

        $exams = $query->latest('start_date')->paginate(20)->withQueryString();
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.examinations.exams.index', compact('exams', 'classes', 'tenant'));
    }

    /**
     * Show the form for creating a new exam
     */
    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.examinations.exams.create', compact('classes', 'tenant'));
    }

    /**
     * Store a newly created exam
     */
    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'exam_name' => 'required|string|max:255',
            'exam_type' => 'required|in:unit_test,mid_term,final,quiz,assignment,preliminary',
            'academic_year' => 'nullable|string|max:50',
            'class_id' => 'nullable|exists:classes,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'status' => 'nullable|in:draft,scheduled,ongoing,completed,published,archived',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            DB::beginTransaction();

            $exam = Exam::create([
                'tenant_id' => $tenant->id,
                'exam_name' => $request->exam_name,
                'exam_type' => $request->exam_type,
                'academic_year' => $request->academic_year,
                'class_id' => $request->class_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description,
                'status' => $request->status ?? 'draft',
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect(url('/admin/examinations/exams'))->with('success', 'Exam created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create exam: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified exam
     */
    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $exam = Exam::forTenant($tenant->id)
            ->with(['schoolClass', 'examSchedules.subject', 'examSchedules.supervisor'])
            ->findOrFail($id);

        // Get statistics
        $stats = [
            'total_schedules' => $exam->examSchedules()->count(),
            'total_results' => $exam->examResults()->count(),
            'total_students' => $exam->examResults()->distinct('student_id')->count('student_id'),
        ];

        return view('tenant.admin.examinations.exams.show', compact('exam', 'stats', 'tenant'));
    }

    /**
     * Show the form for editing the specified exam
     */
    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $exam = Exam::forTenant($tenant->id)->findOrFail($id);
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.examinations.exams.edit', compact('exam', 'classes', 'tenant'));
    }

    /**
     * Update the specified exam
     */
    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $exam = Exam::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'exam_name' => 'required|string|max:255',
            'exam_type' => 'required|in:unit_test,mid_term,final,quiz,assignment,preliminary',
            'academic_year' => 'nullable|string|max:50',
            'class_id' => 'nullable|exists:classes,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'status' => 'nullable|in:draft,scheduled,ongoing,completed,published,archived',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            DB::beginTransaction();

            $exam->update([
                'exam_name' => $request->exam_name,
                'exam_type' => $request->exam_type,
                'academic_year' => $request->academic_year,
                'class_id' => $request->class_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description,
                'status' => $request->status ?? $exam->status,
            ]);

            DB::commit();

            return redirect(url('/admin/examinations/exams'))->with('success', 'Exam updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update exam: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified exam
     */
    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $exam = Exam::forTenant($tenant->id)->findOrFail($id);

        // Check if exam has schedules or results
        if ($exam->examSchedules()->count() > 0) {
            return back()->with('error', 'Cannot delete exam with existing schedules. Please delete schedules first.');
        }

        if ($exam->examResults()->count() > 0) {
            return back()->with('error', 'Cannot delete exam with existing results. Please delete results first.');
        }

        try {
            $exam->delete();
            return redirect(url('/admin/examinations/exams'))->with('success', 'Exam deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete exam: ' . $e->getMessage());
        }
    }
}

