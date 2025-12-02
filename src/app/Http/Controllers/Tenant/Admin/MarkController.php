<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Mark;
use App\Models\GradeScale;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Exam;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class MarkController extends Controller
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
        $tenant = $request->attributes->get('current_tenant');

        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        return $tenant;
    }

    /**
     * Display a listing of marks
     */
    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);

        $query = Mark::forTenant($tenant->id)
            ->with(['student', 'subject', 'schoolClass', 'section', 'exam']);

        // Filter by class
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by section
        if ($request->has('section_id') && $request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        // Filter by subject
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by student
        if ($request->has('student_id') && $request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by exam
        if ($request->has('exam_id') && $request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }

        // Filter by mark type
        if ($request->has('mark_type') && $request->mark_type) {
            $query->where('mark_type', $request->mark_type);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $marks = $query->latest('assessment_date')->latest()->paginate(20)->withQueryString();

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();
        $exams = Exam::forTenant($tenant->id)->where('status', '!=', 'archived')->get();

        return view('tenant.admin.grades.marks.index', compact('marks', 'classes', 'subjects', 'exams', 'tenant'));
    }

    /**
     * Show the form for creating a new mark
     */
    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $subjectId = $request->get('subject_id');
        $examId = $request->get('exam_id');

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();
        $exams = Exam::forTenant($tenant->id)->where('status', '!=', 'archived')->get();

        $students = collect();
        if ($classId) {
            $studentsQuery = Student::forTenant($tenant->id)
                ->whereHas('currentEnrollment', function($q) use ($classId, $sectionId) {
                    $q->where('class_id', $classId);
                    if ($sectionId) {
                        $q->where('section_id', $sectionId);
                    }
                })
                ->active();

            $students = $studentsQuery->orderBy('full_name')->get();
        }

        $sections = collect();
        if ($classId) {
            $sections = Section::forTenant($tenant->id)
                ->where('class_id', $classId)
                ->orderBy('section_name')
                ->get();
        }

        return view('tenant.admin.grades.marks.create', compact(
            'classes', 'sections', 'subjects', 'exams', 'students', 'tenant',
            'classId', 'sectionId', 'subjectId', 'examId'
        ));
    }

    /**
     * Store a newly created mark
     */
    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_id' => 'nullable|exists:exams,id',
            'mark_type' => 'required|in:assignment,quiz,project,test,exam,homework,classwork',
            'title' => 'nullable|string|max:255',
            'assessment_date' => 'nullable|date',
            'marks_obtained' => 'required|numeric|min:0',
            'max_marks' => 'required|numeric|min:0',
            'is_absent' => 'nullable|boolean',
            'remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            DB::beginTransaction();

            $isAbsent = $request->is_absent ?? false;
            $marksObtained = $isAbsent ? 0 : $request->marks_obtained;

            // Calculate percentage
            $percentage = $request->max_marks > 0
                ? round(($marksObtained / $request->max_marks) * 100, 2)
                : 0;

            // Create mark instance to use calculateGrade method
            $mark = new Mark([
                'tenant_id' => $tenant->id,
                'student_id' => $request->student_id,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'subject_id' => $request->subject_id,
                'exam_id' => $request->exam_id,
                'mark_type' => $request->mark_type,
                'title' => $request->title,
                'assessment_date' => $request->assessment_date ?? now(),
                'marks_obtained' => $marksObtained,
                'max_marks' => $request->max_marks,
                'percentage' => $percentage,
                'is_absent' => $isAbsent,
                'remarks' => $request->remarks,
                'entered_by' => auth()->id(),
                'entered_at' => now(),
            ]);

            // Calculate grade
            $mark->calculateGrade($tenant->id);
            $mark->save();

            DB::commit();

            return redirect(url('/admin/grades/marks'))->with('success', 'Mark created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create mark: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for bulk entry
     */
    public function entry(Request $request)
    {
        $tenant = $this->getTenant($request);

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $subjectId = $request->get('subject_id');
        $examId = $request->get('exam_id');

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();
        $exams = Exam::forTenant($tenant->id)->where('status', '!=', 'archived')->get();

        $students = collect();
        $sections = collect();

        // Only load students and sections if class is selected
        if ($classId) {
            // Get sections for the class
            $sections = Section::forTenant($tenant->id)
                ->where('class_id', $classId)
                ->orderBy('section_name')
                ->get();

            // Get students for the class/section if both class and subject are selected
            if ($subjectId) {
                $studentsQuery = Student::forTenant($tenant->id)
                    ->whereHas('currentEnrollment', function($q) use ($classId, $sectionId) {
                        $q->where('class_id', $classId);
                        if ($sectionId) {
                            $q->where('section_id', $sectionId);
                        }
                    })
                    ->with('currentEnrollment')
                    ->active();

                $students = $studentsQuery->orderBy('full_name')->get();
            }
        }

        return view('tenant.admin.grades.marks.entry', compact(
            'classes', 'sections', 'subjects', 'exams', 'students', 'tenant',
            'classId', 'sectionId', 'subjectId', 'examId'
        ));
    }

    /**
     * Store bulk marks entry
     */
    public function bulkEntry(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_id' => 'nullable|exists:exams,id',
            'mark_type' => 'required|in:assignment,quiz,project,test,exam,homework,classwork',
            'title' => 'nullable|string|max:255',
            'assessment_date' => 'nullable|date',
            'max_marks' => 'required|numeric|min:0',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.marks_obtained' => 'nullable|numeric|min:0',
            'marks.*.is_absent' => 'nullable|boolean',
            'marks.*.remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            DB::beginTransaction();

            $created = 0;
            $updated = 0;

            foreach ($request->marks as $markData) {
                $studentId = $markData['student_id'];
                $isAbsent = $markData['is_absent'] ?? false;
                $marksObtained = $isAbsent ? 0 : ($markData['marks_obtained'] ?? 0);

                // Get student enrollment
                $student = Student::forTenant($tenant->id)->with('currentEnrollment')->findOrFail($studentId);
                $enrollment = $student->currentEnrollment;

                if (!$enrollment) {
                    continue;
                }

                // Calculate percentage
                $percentage = $request->max_marks > 0
                    ? round(($marksObtained / $request->max_marks) * 100, 2)
                    : 0;

                // Create mark instance for grade calculation
                $mark = new Mark([
                    'tenant_id' => $tenant->id,
                    'student_id' => $studentId,
                    'class_id' => $enrollment->class_id,
                    'section_id' => $enrollment->section_id,
                    'subject_id' => $request->subject_id,
                    'exam_id' => $request->exam_id,
                    'mark_type' => $request->mark_type,
                    'title' => $request->title,
                    'assessment_date' => $request->assessment_date ?? now(),
                    'marks_obtained' => $marksObtained,
                    'max_marks' => $request->max_marks,
                    'percentage' => $percentage,
                    'is_absent' => $isAbsent,
                    'remarks' => $markData['remarks'] ?? null,
                    'entered_by' => auth()->id(),
                    'entered_at' => now(),
                ]);

                // Calculate grade
                $mark->calculateGrade($tenant->id);
                
                // Check for existing mark with same criteria (optional: update instead of create)
                $existing = Mark::forTenant($tenant->id)
                    ->where('student_id', $studentId)
                    ->where('subject_id', $request->subject_id)
                    ->where('mark_type', $request->mark_type)
                    ->where('title', $request->title)
                    ->where('assessment_date', $request->assessment_date ?? now()->toDateString())
                    ->first();

                if ($existing) {
                    $mark->id = $existing->id;
                    $existing->update($mark->toArray());
                    $updated++;
                } else {
                    $mark->save();
                    $created++;
                }
            }

            DB::commit();

            $message = "Marks saved successfully! ";
            if ($created > 0) {
                $message .= "Created: {$created}. ";
            }
            if ($updated > 0) {
                $message .= "Updated: {$updated}.";
            }

            return redirect(url('/admin/grades/marks'))->with('success', trim($message));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to save marks: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified mark
     */
    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $mark = Mark::forTenant($tenant->id)
            ->with(['student', 'subject', 'schoolClass', 'section', 'exam'])
            ->findOrFail($id);

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();
        $exams = Exam::forTenant($tenant->id)->where('status', '!=', 'archived')->get();

        $sections = Section::forTenant($tenant->id)
            ->where('class_id', $mark->class_id)
            ->orderBy('section_name')
            ->get();

        return view('tenant.admin.grades.marks.edit', compact('mark', 'classes', 'sections', 'subjects', 'exams', 'tenant'));
    }

    /**
     * Update the specified mark
     */
    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $mark = Mark::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'marks_obtained' => 'required|numeric|min:0|max:' . $mark->max_marks,
            'max_marks' => 'nullable|numeric|min:0',
            'is_absent' => 'nullable|boolean',
            'remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            DB::beginTransaction();

            $isAbsent = $request->is_absent ?? false;
            $marksObtained = $isAbsent ? 0 : $request->marks_obtained;
            $maxMarks = $request->max_marks ?? $mark->max_marks;

            // Calculate percentage
            $percentage = $maxMarks > 0
                ? round(($marksObtained / $maxMarks) * 100, 2)
                : 0;

            // Update marks
            $mark->marks_obtained = $marksObtained;
            $mark->max_marks = $maxMarks;
            $mark->percentage = $percentage;
            $mark->is_absent = $isAbsent;
            $mark->remarks = $request->remarks;

            // Recalculate grade
            $mark->calculateGrade($tenant->id);
            $mark->save();

            DB::commit();

            return redirect(url('/admin/grades/marks'))->with('success', 'Mark updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update mark: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified mark
     */
    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $mark = Mark::forTenant($tenant->id)->findOrFail($id);

        try {
            $mark->delete();
            return redirect(url('/admin/grades/marks'))->with('success', 'Mark deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete mark: ' . $e->getMessage());
        }
    }
}
