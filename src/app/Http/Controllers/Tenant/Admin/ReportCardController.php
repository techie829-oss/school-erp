<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\GradeScale;
use App\Models\ReportCard;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportCardController extends Controller
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
     * Display a listing of report cards
     */
    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);

        $query = ReportCard::forTenant($tenant->id)
            ->with(['exam', 'student', 'schoolClass', 'section']);

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

        // Filter by published status
        if ($request->has('is_published') && $request->is_published !== '') {
            $query->where('is_published', $request->is_published);
        }

        // Search by student name or admission number
        if ($request->has('search') && $request->search) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('admission_number', 'like', '%' . $request->search . '%');
            });
        }

        $reportCards = $query->latest('generated_at')->paginate(20)->withQueryString();

        $exams = Exam::forTenant($tenant->id)->where('status', '!=', 'archived')->get();
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.examinations.report-cards.index', compact('reportCards', 'exams', 'classes', 'tenant'));
    }

    /**
     * Generate report card for a student
     */
    public function generate(Request $request)
    {
        $tenant = $this->getTenant($request);

        $examId = $request->get('exam_id');
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');

        if (!$examId) {
            return redirect(url('/admin/examinations/exams'))
                ->with('error', 'Please select an exam first.');
        }

        $exam = Exam::forTenant($tenant->id)->findOrFail($examId);
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.examinations.report-cards.generate', compact('exam', 'classes', 'tenant'));
    }

    /**
     * Store generated report card
     */
    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'student_id' => 'required|exists:students,id',
        ]);

        try {
            DB::beginTransaction();

            $exam = Exam::forTenant($tenant->id)->findOrFail($request->exam_id);
            $student = Student::forTenant($tenant->id)->findOrFail($request->student_id);
            $enrollment = $student->currentEnrollment;

            if (!$enrollment) {
                return back()->with('error', 'Student does not have an active enrollment.');
            }

            // Get all exam results for this student and exam
            $results = ExamResult::forTenant($tenant->id)
                ->where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->with(['subject', 'examSchedule'])
                ->get();

            if ($results->isEmpty()) {
                return back()->with('error', 'No exam results found for this student.');
            }

            // Calculate totals
            $totalMarks = $results->sum('marks_obtained');
            $maxTotalMarks = $results->sum('max_marks');
            $overallPercentage = $maxTotalMarks > 0 ? round(($totalMarks / $maxTotalMarks) * 100, 2) : 0;

            // Get grade from grade scale
            $gradeScale = GradeScale::forTenant($tenant->id)
                ->where('is_active', true)
                ->where('min_percentage', '<=', $overallPercentage)
                ->where('max_percentage', '>=', $overallPercentage)
                ->first();

            $overallGrade = $gradeScale ? $gradeScale->grade_name : null;
            $overallGpa = $gradeScale ? $gradeScale->gpa_value : null;

            // Count subjects
            $subjectsPassed = $results->where('status', 'pass')->count();
            $subjectsFailed = $results->where('status', 'fail')->count();
            $subjectsAbsent = $results->where('status', 'absent')->orWhere('is_absent', true)->count();

            // Calculate ranks
            $classRank = $this->calculateRank($tenant->id, $exam->id, $enrollment->class_id, null, $overallPercentage);
            $sectionRank = $enrollment->section_id
                ? $this->calculateRank($tenant->id, $exam->id, $enrollment->class_id, $enrollment->section_id, $overallPercentage)
                : null;

            // Get attendance percentage
            $attendancePercentage = $this->getAttendancePercentage($tenant->id, $student->id, $exam->start_date, $exam->end_date);

            // Determine overall status
            $overallStatus = ($subjectsFailed == 0 && $subjectsAbsent == 0) ? 'pass' : 'fail';

            // Create or update report card
            $reportCard = ReportCard::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                ],
                [
                    'class_id' => $enrollment->class_id,
                    'section_id' => $enrollment->section_id,
                    'total_marks' => $totalMarks,
                    'max_total_marks' => $maxTotalMarks,
                    'overall_percentage' => $overallPercentage,
                    'overall_grade' => $overallGrade,
                    'overall_gpa' => $overallGpa,
                    'class_rank' => $classRank,
                    'section_rank' => $sectionRank,
                    'overall_status' => $overallStatus,
                    'subjects_passed' => $subjectsPassed,
                    'subjects_failed' => $subjectsFailed,
                    'subjects_absent' => $subjectsAbsent,
                    'attendance_percentage' => $attendancePercentage,
                    'generated_by' => auth()->id(),
                    'generated_at' => now(),
                ]
            );

            DB::commit();

            return redirect(url('/admin/examinations/report-cards'))->with('success', 'Report card generated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to generate report card: ' . $e->getMessage());
        }
    }

    /**
     * Print report card
     */
    public function print(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $reportCard = ReportCard::forTenant($tenant->id)
            ->with(['exam', 'student', 'schoolClass', 'section'])
            ->findOrFail($id);

        // Get all exam results for this report card
        $results = ExamResult::forTenant($tenant->id)
            ->where('exam_id', $reportCard->exam_id)
            ->where('student_id', $reportCard->student_id)
            ->with(['subject', 'examSchedule'])
            ->orderBy('subject_id')
            ->get();

        return view('tenant.admin.examinations.report-cards.print', compact('reportCard', 'results', 'tenant'));
    }

    /**
     * Bulk generate report cards
     */
    public function bulkGenerate(Request $request)
    {
        $tenant = $this->getTenant($request);

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        try {
            DB::beginTransaction();

            $exam = Exam::forTenant($tenant->id)->findOrFail($request->exam_id);

            // Get all students for the class/section
            $studentsQuery = Student::forTenant($tenant->id)
                ->whereHas('currentEnrollment', function($q) use ($request) {
                    $q->where('class_id', $request->class_id);
                    if ($request->section_id) {
                        $q->where('section_id', $request->section_id);
                    }
                })
                ->active();

            $students = $studentsQuery->get();

            if ($students->isEmpty()) {
                return back()->with('error', 'No students found for the selected class/section.');
            }

            $generated = 0;
            $skipped = 0;

            foreach ($students as $student) {
                $enrollment = $student->currentEnrollment;
                if (!$enrollment) {
                    $skipped++;
                    continue;
                }

                // Get all exam results for this student and exam
                $results = ExamResult::forTenant($tenant->id)
                    ->where('exam_id', $exam->id)
                    ->where('student_id', $student->id)
                    ->get();

                if ($results->isEmpty()) {
                    $skipped++;
                    continue;
                }

                // Calculate totals
                $totalMarks = $results->sum('marks_obtained');
                $maxTotalMarks = $results->sum('max_marks');
                $overallPercentage = $maxTotalMarks > 0 ? round(($totalMarks / $maxTotalMarks) * 100, 2) : 0;

                // Get grade from grade scale
                $gradeScale = GradeScale::forTenant($tenant->id)
                    ->where('is_active', true)
                    ->where('min_percentage', '<=', $overallPercentage)
                    ->where('max_percentage', '>=', $overallPercentage)
                    ->first();

                $overallGrade = $gradeScale ? $gradeScale->grade_name : null;
                $overallGpa = $gradeScale ? $gradeScale->gpa_value : null;

                // Count subjects
                $subjectsPassed = $results->where('status', 'pass')->count();
                $subjectsFailed = $results->where('status', 'fail')->count();
                $subjectsAbsent = $results->where('status', 'absent')->orWhere('is_absent', true)->count();

                // Calculate ranks
                $classRank = $this->calculateRank($tenant->id, $exam->id, $enrollment->class_id, null, $overallPercentage);
                $sectionRank = $enrollment->section_id
                    ? $this->calculateRank($tenant->id, $exam->id, $enrollment->class_id, $enrollment->section_id, $overallPercentage)
                    : null;

                // Get attendance percentage
                $attendancePercentage = $this->getAttendancePercentage($tenant->id, $student->id, $exam->start_date, $exam->end_date);

                // Determine overall status
                $overallStatus = ($subjectsFailed == 0 && $subjectsAbsent == 0) ? 'pass' : 'fail';

                // Create or update report card
                ReportCard::updateOrCreate(
                    [
                        'tenant_id' => $tenant->id,
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                    ],
                    [
                        'class_id' => $enrollment->class_id,
                        'section_id' => $enrollment->section_id,
                        'total_marks' => $totalMarks,
                        'max_total_marks' => $maxTotalMarks,
                        'overall_percentage' => $overallPercentage,
                        'overall_grade' => $overallGrade,
                        'overall_gpa' => $overallGpa,
                        'class_rank' => $classRank,
                        'section_rank' => $sectionRank,
                        'overall_status' => $overallStatus,
                        'subjects_passed' => $subjectsPassed,
                        'subjects_failed' => $subjectsFailed,
                        'subjects_absent' => $subjectsAbsent,
                        'attendance_percentage' => $attendancePercentage,
                        'generated_by' => auth()->id(),
                        'generated_at' => now(),
                    ]
                );

                $generated++;
            }

            DB::commit();

            $message = "Bulk report cards generated successfully! ";
            if ($generated > 0) {
                $message .= "Generated: {$generated}. ";
            }
            if ($skipped > 0) {
                $message .= "Skipped (no results): {$skipped}.";
            }

            return redirect(url('/admin/examinations/report-cards'))->with('success', trim($message));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to generate report cards: ' . $e->getMessage());
        }
    }

    /**
     * Calculate student rank
     */
    private function calculateRank($tenantId, $examId, $classId, $sectionId, $percentage)
    {
        $query = ReportCard::forTenant($tenantId)
            ->where('exam_id', $examId)
            ->where('class_id', $classId)
            ->whereNotNull('overall_percentage');

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $rank = $query->where('overall_percentage', '>', $percentage)->count() + 1;

        return $rank;
    }

    /**
     * Get attendance percentage for date range
     */
    private function getAttendancePercentage($tenantId, $studentId, $startDate, $endDate)
    {
        if (!$startDate || !$endDate) {
            return null;
        }

        $totalDays = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1;

        $presentDays = StudentAttendance::forTenant($tenantId)
            ->where('student_id', $studentId)
            ->whereBetween('date', [$startDate, $endDate])
            ->where('status', 'present')
            ->count();

        $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 2) : 0;

        return $percentage;
    }
}

