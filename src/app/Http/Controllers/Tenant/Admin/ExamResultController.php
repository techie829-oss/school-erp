<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\ExamSchedule;
use App\Models\GradeScale;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ExamResultController extends Controller
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
     * Display a listing of exam results
     */
    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);

        $query = ExamResult::forTenant($tenant->id)
            ->with(['exam', 'student', 'subject', 'schoolClass', 'section', 'examSchedule']);

        // Filter by exam
        if ($request->has('exam_id') && $request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }

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

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $results = $query->latest()->paginate(20)->withQueryString();

        $exams = Exam::forTenant($tenant->id)->where('status', '!=', 'archived')->get();
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();

        return view('tenant.admin.examinations.results.index', compact('results', 'exams', 'classes', 'subjects', 'tenant'));
    }

    /**
     * Show the form for entering exam results
     */
    public function entry(Request $request)
    {
        $tenant = $this->getTenant($request);

        $examId = $request->get('exam_id');
        $scheduleId = $request->get('schedule_id');
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');

        if (!$examId || !$scheduleId) {
            return redirect(url('/admin/examinations/schedules'))
                ->with('error', 'Please select an exam schedule first.');
        }

        $exam = Exam::forTenant($tenant->id)->findOrFail($examId);
        $schedule = ExamSchedule::forTenant($tenant->id)
            ->with(['subject', 'schoolClass', 'section'])
            ->findOrFail($scheduleId);

        // Get students for the class/section
        $studentsQuery = Student::forTenant($tenant->id)
            ->whereHas('currentEnrollment', function($q) use ($schedule) {
                $q->where('class_id', $schedule->class_id);
                if ($schedule->section_id) {
                    $q->where('section_id', $schedule->section_id);
                }
            })
            ->with('currentEnrollment')
            ->active();

        $students = $studentsQuery->orderBy('full_name')->get();

        // Get existing results
        $existingResults = ExamResult::forTenant($tenant->id)
            ->where('exam_schedule_id', $scheduleId)
            ->pluck('marks_obtained', 'student_id')
            ->toArray();

            // Get grade scales for grade calculation
            $gradeScales = GradeScale::forTenant($tenant->id)->where('is_active', true)->ordered()->get();

        return view('tenant.admin.examinations.results.entry', compact(
            'exam', 'schedule', 'students', 'existingResults', 'gradeScales', 'tenant'
        ));
    }

    /**
     * Store exam results
     */
    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'exam_id' => 'required|exists:exams,id',
            'exam_schedule_id' => 'required|exists:exam_schedules,id',
            'results' => 'required|array',
            'results.*.student_id' => 'required|exists:students,id',
            'results.*.marks_obtained' => 'nullable|numeric|min:0',
            'results.*.is_absent' => 'nullable|boolean',
            'results.*.remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            DB::beginTransaction();

            $schedule = ExamSchedule::forTenant($tenant->id)->findOrFail($request->exam_schedule_id);
            $gradeScale = GradeScale::forTenant($tenant->id)->where('is_active', true)->first();

            $created = 0;
            $updated = 0;

            foreach ($request->results as $resultData) {
                $studentId = $resultData['student_id'];
                $isAbsent = $resultData['is_absent'] ?? false;
                $marksObtained = $isAbsent ? 0 : ($resultData['marks_obtained'] ?? 0);

                // Get student's class and section from current enrollment
                $student = Student::forTenant($tenant->id)->findOrFail($studentId);
                $enrollment = $student->currentEnrollment;

                if (!$enrollment) {
                    continue; // Skip if student has no active enrollment
                }

                // Calculate percentage
                $percentage = $schedule->max_marks > 0
                    ? round(($marksObtained / $schedule->max_marks) * 100, 2)
                    : 0;

                // Calculate grade and GPA from grade scale
                $grade = null;
                $gpa = null;
                if (!$isAbsent) {
                    $gradeEntry = GradeScale::forTenant($tenant->id)
                        ->where('is_active', true)
                        ->where('min_percentage', '<=', $percentage)
                        ->where('max_percentage', '>=', $percentage)
                        ->first();

                    if ($gradeEntry) {
                        $grade = $gradeEntry->grade_name;
                        $gpa = $gradeEntry->gpa_value;
                    }
                }

                // Determine status
                $status = 'pass';
                if ($isAbsent) {
                    $status = 'absent';
                } elseif ($schedule->passing_marks && $marksObtained < $schedule->passing_marks) {
                    $status = 'fail';
                } elseif ($percentage < 33) {
                    $status = 'fail';
                }

                // Update or create result
                $result = ExamResult::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'exam_schedule_id' => $schedule->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'exam_id' => $request->exam_id,
                        'subject_id' => $schedule->subject_id,
                        'class_id' => $enrollment->class_id,
                        'section_id' => $enrollment->section_id,
                        'marks_obtained' => $marksObtained,
                        'max_marks' => $schedule->max_marks,
                        'passing_marks' => $schedule->passing_marks,
                        'percentage' => $percentage,
                        'grade' => $grade,
                        'gpa' => $gpa,
                        'status' => $status,
                        'is_absent' => $isAbsent,
                        'remarks' => $resultData['remarks'] ?? null,
                        'entered_by' => auth()->id(),
                        'entered_at' => now(),
                    ]
                );

                if ($result->wasRecentlyCreated) {
                    $created++;
                } else {
                    $updated++;
                }
            }

            DB::commit();

            $message = "Results saved successfully! ";
            if ($created > 0) {
                $message .= "Created: {$created}. ";
            }
            if ($updated > 0) {
                $message .= "Updated: {$updated}.";
            }

            return redirect(url('/admin/examinations/results'))->with('success', trim($message));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to save results: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified exam result
     */
    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $result = ExamResult::forTenant($tenant->id)
            ->with(['exam', 'student', 'subject', 'schoolClass', 'section', 'examSchedule'])
            ->findOrFail($id);

        $gradeScales = GradeScale::forTenant($tenant->id)->where('is_active', true)->ordered()->get();

        return view('tenant.admin.examinations.results.edit', compact('result', 'gradeScales', 'tenant'));
    }

    /**
     * Update the specified exam result
     */
    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $result = ExamResult::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'marks_obtained' => 'nullable|numeric|min:0|max:' . $result->max_marks,
            'is_absent' => 'nullable|boolean',
            'is_re_exam' => 'nullable|boolean',
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
            $marksObtained = $isAbsent ? 0 : ($request->marks_obtained ?? $result->marks_obtained);

            // Calculate percentage
            $percentage = $result->max_marks > 0
                ? round(($marksObtained / $result->max_marks) * 100, 2)
                : 0;

            // Get grade scale and calculate grade/GPA
            $grade = null;
            $gpa = null;

            if (!$isAbsent) {
                $gradeEntry = GradeScale::forTenant($tenant->id)
                    ->where('is_active', true)
                    ->where('min_percentage', '<=', $percentage)
                    ->where('max_percentage', '>=', $percentage)
                    ->first();

                if ($gradeEntry) {
                    $grade = $gradeEntry->grade_name;
                    $gpa = $gradeEntry->gpa_value;
                }
            }

            // Determine status
            $status = 'pass';
            if ($isAbsent) {
                $status = 'absent';
            } elseif ($result->passing_marks && $marksObtained < $result->passing_marks) {
                $status = 'fail';
            } elseif ($percentage < 33) {
                $status = 'fail';
            }

            $result->update([
                'marks_obtained' => $marksObtained,
                'percentage' => $percentage,
                'grade' => $grade,
                'gpa' => $gpa,
                'status' => $status,
                'is_absent' => $isAbsent,
                'is_re_exam' => $request->is_re_exam ?? false,
                'remarks' => $request->remarks,
            ]);

            DB::commit();

            return redirect(url('/admin/examinations/results'))->with('success', 'Exam result updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update result: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified exam result
     */
    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $result = ExamResult::forTenant($tenant->id)->findOrFail($id);

        try {
            $result->delete();
            return redirect(url('/admin/examinations/results'))->with('success', 'Exam result deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete result: ' . $e->getMessage());
        }
    }
}

