<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Section;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
            ->with(['schoolClass'])
            ->withCount(['examSchedules', 'examResults', 'admitCards', 'reportCards']);

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

        // Get all class IDs from all exams' schedules for batch query
        $allExamIds = $exams->pluck('id');
        $allSchedules = ExamSchedule::forTenant($tenant->id)
            ->whereIn('exam_id', $allExamIds)
            ->get()
            ->groupBy('exam_id');

        $allClassIds = $allSchedules->flatten()->pluck('class_id')->unique();

        // Batch query for student counts per class
        $studentCountsByClass = \App\Models\ClassEnrollment::forTenant($tenant->id)
            ->whereIn('class_id', $allClassIds)
            ->where('is_current', true)
            ->selectRaw('class_id, COUNT(DISTINCT student_id) as student_count')
            ->groupBy('class_id')
            ->pluck('student_count', 'class_id');

        // Batch query for results per exam
        $resultsByExam = \App\Models\ExamResult::forTenant($tenant->id)
            ->whereIn('exam_id', $allExamIds)
            ->selectRaw('exam_id, COUNT(DISTINCT student_id) as students_with_results')
            ->groupBy('exam_id')
            ->pluck('students_with_results', 'exam_id');

        // Calculate progress stats for each exam
        foreach ($exams as $exam) {
            $schedules = $allSchedules->get($exam->id, collect());
            $classIds = $schedules->pluck('class_id')->unique();

            $totalStudents = $classIds->sum(function($classId) use ($studentCountsByClass) {
                return $studentCountsByClass->get($classId, 0);
            });

            $studentsWithResults = $resultsByExam->get($exam->id, 0);

            $exam->progress_stats = [
                'total_schedules' => $exam->exam_schedules_count,
                'total_students' => $totalStudents,
                'students_with_results' => $studentsWithResults,
                'results_progress' => $totalStudents > 0 ? round(($studentsWithResults / $totalStudents) * 100, 1) : 0,
                'admit_cards_generated' => $exam->admit_cards_count,
                'report_cards_generated' => $exam->report_cards_count,
                'has_schedules' => $exam->exam_schedules_count > 0,
                'has_results' => $studentsWithResults > 0,
            ];
        }

        // Get unique academic years for filter
        $academicYears = Exam::forTenant($tenant->id)
            ->whereNotNull('academic_year')
            ->distinct()
            ->pluck('academic_year')
            ->sort()
            ->reverse();

        return view('tenant.admin.examinations.exams.index', compact('exams', 'classes', 'academicYears', 'tenant'));
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
            ->with([
                'schoolClass',
                'examSchedules.subject',
                'examSchedules.schoolClass',
                'examSchedules.section',
                'examSchedules.supervisor'
            ])
            ->findOrFail($id);

        // Get all schedules for this exam
        $schedules = $exam->examSchedules()->with(['subject', 'schoolClass', 'section'])->get();

        // Get unique subjects from schedules
        $uniqueSubjects = $schedules->pluck('subject_id')->unique()->count();

        // Get students enrolled in classes that have schedules for this exam
        $classIds = $schedules->pluck('class_id')->unique();
        $totalStudents = \App\Models\ClassEnrollment::forTenant($tenant->id)
            ->whereIn('class_id', $classIds)
            ->where('is_current', true)
            ->distinct('student_id')
            ->count('student_id');

        // Get results statistics
        $results = $exam->examResults()->get();
        $resultsEntered = $results->count();
        $studentsWithResults = $results->pluck('student_id')->unique()->count();
        $averageScore = $results->where('marks_obtained', '!=', null)->avg('marks_obtained');

        // Get admit cards statistics
        $admitCards = $exam->admitCards()->get();
        $admitCardsGenerated = $admitCards->count();
        $studentsWithAdmitCards = $admitCards->pluck('student_id')->unique()->count();

        // Get report cards statistics
        $reportCards = $exam->reportCards()->get();
        $reportCardsGenerated = $reportCards->count();
        $studentsWithReportCards = $reportCards->pluck('student_id')->unique()->count();

        // Calculate progress percentages
        $scheduleProgress = $uniqueSubjects > 0 ? 100 : 0; // If schedules exist, consider it complete
        $resultsProgress = $totalStudents > 0 ? round(($studentsWithResults / $totalStudents) * 100, 1) : 0;
        $admitCardsProgress = $totalStudents > 0 ? round(($studentsWithAdmitCards / $totalStudents) * 100, 1) : 0;
        $reportCardsProgress = $totalStudents > 0 ? round(($studentsWithReportCards / $totalStudents) * 100, 1) : 0;

        // Get timeline data (exam dates from schedules)
        $timelineDates = collect();
        if ($schedules->count() > 0) {
            $grouped = [];
            foreach ($schedules as $schedule) {
                if (!$schedule->exam_date) continue;
                $dateKey = $schedule->exam_date->format('Y-m-d');
                if (!isset($grouped[$dateKey])) {
                    $grouped[$dateKey] = [
                        'date' => $schedule->exam_date,
                        'count' => 0,
                        'schedules' => []
                    ];
                }
                $grouped[$dateKey]['count']++;
                $grouped[$dateKey]['schedules'][] = [
                    'subject' => $schedule->subject->subject_name ?? 'N/A',
                    'time' => $schedule->start_time ? $schedule->start_time->format('H:i') : 'N/A',
                    'class' => $schedule->schoolClass->class_name ?? 'N/A',
                    'section' => $schedule->section->section_name ?? null,
                ];
            }
            $timelineDates = collect($grouped)->sortBy(function($item) {
                return $item['date']->timestamp ?? 0;
            })->values();
        }

        // Get statistics
        $stats = [
            'total_schedules' => $schedules->count(),
            'unique_subjects' => $uniqueSubjects,
            'total_results' => $resultsEntered,
            'total_students' => $totalStudents,
            'students_with_results' => $studentsWithResults,
            'results_pending' => max(0, $totalStudents - $studentsWithResults),
            'average_score' => $averageScore ? round($averageScore, 2) : null,
            'admit_cards_generated' => $admitCardsGenerated,
            'students_with_admit_cards' => $studentsWithAdmitCards,
            'report_cards_generated' => $reportCardsGenerated,
            'students_with_report_cards' => $studentsWithReportCards,
            'schedule_progress' => $scheduleProgress,
            'results_progress' => $resultsProgress,
            'admit_cards_progress' => $admitCardsProgress,
            'report_cards_progress' => $reportCardsProgress,
        ];

        return view('tenant.admin.examinations.exams.show', compact('exam', 'stats', 'tenant', 'timelineDates', 'schedules'));
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

    /**
     * Show exam setup wizard
     */
    public function createWizard(Request $request)
    {
        $tenant = $this->getTenant($request);

        $classes = SchoolClass::forTenant($tenant->id)
            ->active()
            ->ordered()
            ->with(['sections' => function($q) {
                $q->where('is_active', true);
            }])
            ->get();

        $subjects = Subject::forTenant($tenant->id)->active()->orderBy('subject_name')->get();

        // Get class-subject and section-subject mappings
        $classSubjects = [];
        $sectionSubjects = [];

        foreach ($classes as $class) {
            $classSubjects[$class->id] = $class->subjects()->pluck('subjects.id')->toArray();

            if ($class->has_sections) {
                foreach ($class->sections as $section) {
                    $sectionSubjects[$section->id] = $section->subjects()->pluck('subjects.id')->toArray();
                }
            }
        }

        return view('tenant.admin.examinations.exams.create-wizard', compact(
            'classes',
            'subjects',
            'classSubjects',
            'sectionSubjects',
            'tenant'
        ));
    }

    /**
     * Store exam and schedules from wizard
     */
    public function storeWizard(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'exam_name' => 'required|string|max:255',
            'exam_type' => 'required|in:unit_test,mid_term,final,quiz,assignment,preliminary',
            'academic_year' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'status' => 'nullable|in:draft,scheduled,ongoing,completed,published,archived',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => [
                'required',
                Rule::exists('classes', 'id')->where('tenant_id', $tenant->id),
            ],
            'section_ids' => 'nullable|array',
            'section_ids.*' => [
                'nullable',
                Rule::exists('sections', 'id')->where('tenant_id', $tenant->id),
            ],
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => [
                'required',
                Rule::exists('subjects', 'id')->where('tenant_id', $tenant->id),
            ],
            'default_date' => 'nullable|date',
            'default_start_time' => 'nullable|date_format:H:i',
            'default_end_time' => 'nullable|date_format:H:i',
            'default_duration' => 'nullable|integer|min:1',
            'default_max_marks' => 'nullable|numeric|min:0',
            'default_passing_marks' => 'nullable|numeric|min:0',
            'schedules' => 'required|array|min:1',
            'schedules.*.class_id' => [
                'required',
                Rule::exists('classes', 'id')->where('tenant_id', $tenant->id),
            ],
            'schedules.*.section_id' => [
                'nullable',
                Rule::exists('sections', 'id')->where('tenant_id', $tenant->id),
            ],
            'schedules.*.subject_id' => [
                'required',
                Rule::exists('subjects', 'id')->where('tenant_id', $tenant->id),
            ],
            'schedules.*.exam_date' => 'required|date',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.max_marks' => 'required|numeric|min:0',
            'schedules.*.passing_marks' => 'nullable|numeric|min:0',
            'schedules.*.room_number' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            DB::beginTransaction();

            // Create exam
            $exam = Exam::create([
                'tenant_id' => $tenant->id,
                'exam_name' => $request->exam_name,
                'exam_type' => $request->exam_type,
                'academic_year' => $request->academic_year,
                'class_id' => null, // Can be set if single class
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'description' => $request->description,
                'status' => $request->status ?? 'draft',
                'created_by' => auth()->id(),
            ]);

            // Create schedules
            $created = 0;
            $conflicts = [];

            foreach ($request->schedules as $scheduleData) {
                // Check for conflicts
                $conflict = ExamSchedule::forTenant($tenant->id)
                    ->where('exam_date', $scheduleData['exam_date'])
                    ->where('start_time', $scheduleData['start_time'])
                    ->where(function($q) use ($scheduleData) {
                        $q->where('room_number', $scheduleData['room_number'] ?? '')
                          ->orWhere(function($q2) use ($scheduleData) {
                              $q2->where('class_id', $scheduleData['class_id'])
                                 ->where(function($q3) use ($scheduleData) {
                                     if ($scheduleData['section_id']) {
                                         $q3->where('section_id', $scheduleData['section_id']);
                                     } else {
                                         $q3->whereNull('section_id');
                                     }
                                 });
                          });
                    })
                    ->exists();

                if ($conflict) {
                    $conflicts[] = $scheduleData;
                    continue;
                }

                $start = \Carbon\Carbon::parse($scheduleData['start_time']);
                $end = \Carbon\Carbon::parse($scheduleData['end_time']);
                $duration = $start->diffInMinutes($end);

                ExamSchedule::create([
                    'tenant_id' => $tenant->id,
                    'exam_id' => $exam->id,
                    'subject_id' => $scheduleData['subject_id'],
                    'class_id' => $scheduleData['class_id'],
                    'section_id' => $scheduleData['section_id'] ?? null,
                    'exam_date' => $scheduleData['exam_date'],
                    'start_time' => $scheduleData['start_time'],
                    'end_time' => $scheduleData['end_time'],
                    'duration_minutes' => $duration,
                    'room_number' => $scheduleData['room_number'] ?? null,
                    'max_marks' => $scheduleData['max_marks'],
                    'passing_marks' => $scheduleData['passing_marks'] ?? null,
                    'instructions' => $scheduleData['instructions'] ?? null,
                    'supervisor_id' => $scheduleData['supervisor_id'] ?? null,
                ]);

                $created++;
            }

            DB::commit();

            $message = "Exam '{$exam->exam_name}' created successfully with {$created} schedule(s)!";
            if (count($conflicts) > 0) {
                $message .= " " . count($conflicts) . " schedule(s) skipped due to conflicts.";
            }

            return redirect(url('/admin/examinations/exams/' . $exam->id))
                ->with('success', $message)
                ->with('conflicts', $conflicts);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create exam: ' . $e->getMessage());
        }
    }
}

