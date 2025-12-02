<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\GradeBook;
use App\Models\GradeScale;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GradeBookController extends Controller
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
     * Display a listing of grade books
     */
    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);

        $query = GradeBook::forTenant($tenant->id)
            ->with(['student', 'schoolClass', 'section']);

        // Filter by class
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by section
        if ($request->has('section_id') && $request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        // Filter by academic year
        if ($request->has('academic_year') && $request->academic_year) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by term
        if ($request->has('term') && $request->term) {
            $query->where('term', $request->term);
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

        $gradeBooks = $query->latest('generated_at')->paginate(20)->withQueryString();

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        
        // Get available academic years from marks
        $academicYears = Mark::forTenant($tenant->id)
            ->distinct()
            ->whereNotNull('assessment_date')
            ->get()
            ->pluck('assessment_date')
            ->map(function($date) {
                // Extract academic year from date (assuming academic year starts in April)
                $year = $date->year;
                $month = $date->month;
                if ($month >= 4) {
                    return ($year . '-' . ($year + 1));
                } else {
                    return (($year - 1) . '-' . $year);
                }
            })
            ->unique()
            ->sort()
            ->values();

        return view('tenant.admin.grades.grade-books.index', compact('gradeBooks', 'classes', 'academicYears', 'tenant'));
    }

    /**
     * Display the specified grade book
     */
    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $gradeBook = GradeBook::forTenant($tenant->id)
            ->with(['student', 'schoolClass', 'section', 'generatedBy'])
            ->findOrFail($id);

        // Get all marks for this student, class, academic year, and term
        $marksQuery = Mark::forTenant($tenant->id)
            ->where('student_id', $gradeBook->student_id)
            ->where('class_id', $gradeBook->class_id);

        if ($gradeBook->section_id) {
            $marksQuery->where('section_id', $gradeBook->section_id);
        }

        // Filter by academic year (based on assessment_date)
        if ($gradeBook->academic_year) {
            $yearParts = explode('-', $gradeBook->academic_year);
            $startYear = (int)$yearParts[0];
            $endYear = (int)$yearParts[1] ?? $startYear + 1;
            
            $marksQuery->whereBetween('assessment_date', [
                $startYear . '-04-01',
                $endYear . '-03-31'
            ]);
        }

        $marks = $marksQuery->with(['subject', 'exam'])
            ->orderBy('subject_id')
            ->orderBy('assessment_date')
            ->get();

        // Group marks by subject
        $marksBySubject = $marks->groupBy('subject_id');

        return view('tenant.admin.grades.grade-books.show', compact('gradeBook', 'marksBySubject', 'marks', 'tenant'));
    }

    /**
     * Show the form for generating grade books
     */
    public function generate(Request $request)
    {
        $tenant = $this->getTenant($request);

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');
        $academicYear = $request->get('academic_year', date('Y') . '-' . (date('Y') + 1));
        $term = $request->get('term');

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        if ($classId) {
            $sections = Section::forTenant($tenant->id)
                ->where('class_id', $classId)
                ->orderBy('section_name')
                ->get();

            // Get available academic years from marks
            $academicYears = Mark::forTenant($tenant->id)
                ->distinct()
                ->whereNotNull('assessment_date')
                ->get()
                ->pluck('assessment_date')
                ->map(function($date) {
                    $year = $date->year;
                    $month = $date->month;
                    if ($month >= 4) {
                        return ($year . '-' . ($year + 1));
                    } else {
                        return (($year - 1) . '-' . $year);
                    }
                })
                ->unique()
                ->sort()
                ->values();
        } else {
            $sections = collect();
            $academicYears = collect();
        }

        return view('tenant.admin.grades.grade-books.generate', compact(
            'classes', 'sections', 'tenant', 'classId', 'sectionId', 'academicYear', 'term', 'academicYears'
        ));
    }

    /**
     * Store generated grade book
     */
    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'academic_year' => 'required|string',
            'term' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            DB::beginTransaction();

            $student = Student::forTenant($tenant->id)->findOrFail($request->student_id);
            $enrollment = $student->currentEnrollment;

            if (!$enrollment) {
                return back()->with('error', 'Student does not have an active enrollment.');
            }

            // Get all marks for this student, class, academic year, and term
            $marksQuery = Mark::forTenant($tenant->id)
                ->where('student_id', $student->id)
                ->where('class_id', $request->class_id);

            if ($request->section_id) {
                $marksQuery->where('section_id', $request->section_id);
            }

            // Filter by academic year
            if ($request->academic_year) {
                $yearParts = explode('-', $request->academic_year);
                $startYear = (int)$yearParts[0];
                $endYear = (int)$yearParts[1] ?? $startYear + 1;
                
                $marksQuery->whereBetween('assessment_date', [
                    $startYear . '-04-01',
                    $endYear . '-03-31'
                ]);
            }

            $marks = $marksQuery->get();

            if ($marks->isEmpty()) {
                return back()->with('error', 'No marks found for this student in the selected period.');
            }

            // Calculate totals
            $totalMarks = $marks->sum('marks_obtained');
            $maxTotalMarks = $marks->sum('max_marks');
            $percentage = $maxTotalMarks > 0 ? round(($totalMarks / $maxTotalMarks) * 100, 2) : 0;

            // Group by subject to count passed/failed
            $marksBySubject = $marks->groupBy('subject_id');
            $totalSubjects = $marksBySubject->count();
            $passedSubjects = $marksBySubject->filter(function($subjectMarks) {
                return $subjectMarks->where('status', 'pass')->count() > 0;
            })->count();
            $failedSubjects = $totalSubjects - $passedSubjects;

            // Create grade book instance
            $gradeBook = new GradeBook([
                'tenant_id' => $tenant->id,
                'student_id' => $student->id,
                'class_id' => $enrollment->class_id,
                'section_id' => $enrollment->section_id,
                'academic_year' => $request->academic_year,
                'term' => $request->term,
                'total_marks' => $totalMarks,
                'max_total_marks' => $maxTotalMarks,
                'percentage' => $percentage,
                'total_subjects' => $totalSubjects,
                'passed_subjects' => $passedSubjects,
                'failed_subjects' => $failedSubjects,
                'generated_by' => auth()->id(),
                'generated_at' => now(),
            ]);

            // Calculate overall grade
            $gradeBook->calculateOverallGrade($tenant->id);

            // Check if grade book already exists
            $existing = GradeBook::forTenant($tenant->id)
                ->where('student_id', $student->id)
                ->where('academic_year', $request->academic_year)
                ->where('term', $request->term)
                ->first();

            if ($existing) {
                $existing->update($gradeBook->toArray());
                $gradeBook = $existing;
            } else {
                $gradeBook->save();
            }

            // Calculate rank (optional - can be done in a separate process)
            $this->calculateRank($tenant->id, $request->academic_year, $request->term, $request->class_id, $request->section_id);

            DB::commit();

            return redirect(url('/admin/grades/grade-books'))->with('success', 'Grade book generated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to generate grade book: ' . $e->getMessage());
        }
    }

    /**
     * Bulk generate grade books
     */
    public function bulkGenerate(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'academic_year' => 'required|string',
            'term' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            DB::beginTransaction();

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
                // Check if grade book already exists
                $existing = GradeBook::forTenant($tenant->id)
                    ->where('student_id', $student->id)
                    ->where('academic_year', $request->academic_year)
                    ->where('term', $request->term)
                    ->first();

                if ($existing && $request->input('skip_existing', false)) {
                    $skipped++;
                    continue;
                }

                // Generate grade book for this student
                $enrollment = $student->currentEnrollment;
                if (!$enrollment) {
                    $skipped++;
                    continue;
                }

                // Get marks for this student
                $marksQuery = Mark::forTenant($tenant->id)
                    ->where('student_id', $student->id)
                    ->where('class_id', $request->class_id);

                if ($request->section_id) {
                    $marksQuery->where('section_id', $request->section_id);
                }

                // Filter by academic year
                if ($request->academic_year) {
                    $yearParts = explode('-', $request->academic_year);
                    $startYear = (int)$yearParts[0];
                    $endYear = (int)$yearParts[1] ?? $startYear + 1;
                    
                    $marksQuery->whereBetween('assessment_date', [
                        $startYear . '-04-01',
                        $endYear . '-03-31'
                    ]);
                }

                $marks = $marksQuery->get();

                if ($marks->isEmpty()) {
                    $skipped++;
                    continue;
                }

                // Calculate totals
                $totalMarks = $marks->sum('marks_obtained');
                $maxTotalMarks = $marks->sum('max_marks');
                $percentage = $maxTotalMarks > 0 ? round(($totalMarks / $maxTotalMarks) * 100, 2) : 0;

                // Group by subject
                $marksBySubject = $marks->groupBy('subject_id');
                $totalSubjects = $marksBySubject->count();
                $passedSubjects = $marksBySubject->filter(function($subjectMarks) {
                    return $subjectMarks->where('status', 'pass')->count() > 0;
                })->count();
                $failedSubjects = $totalSubjects - $passedSubjects;

                // Create or update grade book
                $gradeBookData = [
                    'tenant_id' => $tenant->id,
                    'student_id' => $student->id,
                    'class_id' => $enrollment->class_id,
                    'section_id' => $enrollment->section_id,
                    'academic_year' => $request->academic_year,
                    'term' => $request->term,
                    'total_marks' => $totalMarks,
                    'max_total_marks' => $maxTotalMarks,
                    'percentage' => $percentage,
                    'total_subjects' => $totalSubjects,
                    'passed_subjects' => $passedSubjects,
                    'failed_subjects' => $failedSubjects,
                    'generated_by' => auth()->id(),
                    'generated_at' => now(),
                ];

                if ($existing) {
                    $existing->update($gradeBookData);
                    $gradeBook = $existing;
                } else {
                    $gradeBook = GradeBook::create($gradeBookData);
                }

                // Calculate overall grade
                $gradeBook->calculateOverallGrade($tenant->id);
                $gradeBook->save();

                $generated++;
            }

            // Calculate ranks for all students
            $this->calculateRank($tenant->id, $request->academic_year, $request->term, $request->class_id, $request->section_id);

            DB::commit();

            $message = "Grade books generated successfully! Generated: {$generated}";
            if ($skipped > 0) {
                $message .= ", Skipped: {$skipped}";
            }

            return redirect(url('/admin/grades/grade-books'))->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to generate grade books: ' . $e->getMessage());
        }
    }

    /**
     * Print grade book
     */
    public function print(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $gradeBook = GradeBook::forTenant($tenant->id)
            ->with(['student', 'schoolClass', 'section', 'generatedBy'])
            ->findOrFail($id);

        // Get all marks for this grade book
        $marksQuery = Mark::forTenant($tenant->id)
            ->where('student_id', $gradeBook->student_id)
            ->where('class_id', $gradeBook->class_id);

        if ($gradeBook->section_id) {
            $marksQuery->where('section_id', $gradeBook->section_id);
        }

        // Filter by academic year
        if ($gradeBook->academic_year) {
            $yearParts = explode('-', $gradeBook->academic_year);
            $startYear = (int)$yearParts[0];
            $endYear = (int)$yearParts[1] ?? $startYear + 1;
            
            $marksQuery->whereBetween('assessment_date', [
                $startYear . '-04-01',
                $endYear . '-03-31'
            ]);
        }

        $marks = $marksQuery->with(['subject', 'exam'])
            ->orderBy('subject_id')
            ->orderBy('assessment_date')
            ->get();

        // Group marks by subject
        $marksBySubject = $marks->groupBy('subject_id');

        return view('tenant.admin.grades.grade-books.print', compact('gradeBook', 'marksBySubject', 'marks', 'tenant'));
    }

    /**
     * Calculate rank for students in a class/section
     */
    private function calculateRank($tenantId, $academicYear, $term, $classId, $sectionId = null)
    {
        $query = GradeBook::forTenant($tenantId)
            ->where('academic_year', $academicYear)
            ->where('term', $term)
            ->where('class_id', $classId);

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $gradeBooks = $query->orderByDesc('percentage')
            ->orderByDesc('total_marks')
            ->get();

        $rank = 1;
        $previousPercentage = null;
        $previousTotal = null;

        foreach ($gradeBooks as $gradeBook) {
            // If percentage and total are different from previous, increment rank
            if ($previousPercentage !== null && 
                ($gradeBook->percentage != $previousPercentage || $gradeBook->total_marks != $previousTotal)) {
                $rank = $gradeBooks->where('percentage', '>', $gradeBook->percentage)
                    ->count() + 1;
            }

            $gradeBook->rank = $rank;
            $gradeBook->save();

            $previousPercentage = $gradeBook->percentage;
            $previousTotal = $gradeBook->total_marks;
        }
    }
}
