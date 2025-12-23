<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\ExamShift;
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
        $shifts = ExamShift::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.examinations.exams.create', compact('classes', 'shifts', 'tenant'));
    }

    /**
     * Store a newly created exam
     */
    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'exam_name' => 'required|string|max:255',
            'exam_type' => 'required|in:unit_test,mid_term,final,quiz,assignment,preliminary,practical,oral',
            'academic_year' => 'nullable|string|max:50',
            'class_id' => 'nullable|exists:classes,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'status' => 'nullable|in:draft,scheduled,ongoing,completed,published,archived',
            'admit_card_enabled' => 'nullable|boolean',
            'result_enabled' => 'nullable|boolean',
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
                'max_exams_per_day' => $request->max_exams_per_day,
                'shift_selection_mode' => $request->shift_selection_mode,
                'default_shift_id' => $request->default_shift_id,
                'skip_weekends' => $request->has('skip_weekends') ? (bool)$request->skip_weekends : true,
                'default_max_marks' => $request->default_max_marks,
                'default_passing_marks' => $request->default_passing_marks,
                'default_duration_minutes' => $request->default_duration_minutes,
                'admit_card_enabled' => $request->has('admit_card_enabled') ? (bool)$request->admit_card_enabled : true,
                'result_enabled' => $request->has('result_enabled') ? (bool)$request->result_enabled : true,
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
                'examSchedules.supervisor',
                'examSchedules.shift'
            ])
            ->findOrFail($id);

        // Get all schedules for this exam with filtering and sorting
        // Query directly from ExamSchedule model to ensure we get all schedules
        $scheduleQuery = \App\Models\ExamSchedule::forTenant($tenant->id)
            ->where('exam_id', $exam->id);

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $scheduleQuery->where('exam_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $scheduleQuery->where('exam_date', '<=', $request->date_to);
        }

        // Get all schedules first with relationships
        $schedules = $scheduleQuery->with(['subject', 'schoolClass', 'section', 'supervisor', 'shift'])->get();

        // Sorting
        $sortBy = $request->get('sort_by', 'exam_date');
        $sortOrder = $request->get('sort_order', 'asc');

        if ($sortBy === 'exam_date') {
            $schedules = $schedules->sortBy(function($schedule) use ($sortOrder) {
                $dateValue = $schedule->exam_date ? $schedule->exam_date->format('U') : 9999999999;
                $timeValue = $schedule->start_time ? strtotime($schedule->start_time) : 0;
                return $dateValue . '_' . $timeValue;
            }, SORT_REGULAR, $sortOrder === 'desc');
        } elseif ($sortBy === 'subject') {
            $schedules = $schedules->sortBy(function($schedule) use ($sortOrder) {
                $subjectName = $schedule->subject ? $schedule->subject->subject_name : 'ZZZ';
                $dateValue = $schedule->exam_date ? $schedule->exam_date->format('U') : 9999999999;
                $timeValue = $schedule->start_time ? strtotime($schedule->start_time) : 0;
                return $subjectName . '_' . $dateValue . '_' . $timeValue;
            }, SORT_REGULAR, $sortOrder === 'desc');
        } else {
            $schedules = $schedules->sortBy(function($schedule) use ($sortOrder) {
                $dateValue = $schedule->exam_date ? $schedule->exam_date->format('U') : 9999999999;
                $timeValue = $schedule->start_time ? strtotime($schedule->start_time) : 0;
                return $dateValue . '_' . $timeValue;
            }, SORT_REGULAR, $sortOrder === 'desc');
        }

        $schedules = $schedules->values();

        // Get unique subjects from schedules
        $uniqueSubjects = $schedules->pluck('subject_id')->unique()->count();

        // Get students enrolled in classes that have schedules for this exam
        $classIds = $schedules->pluck('class_id')->unique();

        // Get all classes with their numeric values for sorting
        $classesMap = collect();
        if ($classIds->isNotEmpty()) {
            $classesMap = SchoolClass::forTenant($tenant->id)
                ->whereIn('id', $classIds)
                ->get()
                ->keyBy('class_name')
                ->map(function($class) {
                    return [
                        'id' => $class->id,
                        'class_name' => $class->class_name,
                        'class_numeric' => $class->class_numeric ?? 999999, // Use high number for null values (sort last)
                    ];
                });
        }
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

        // Get all shifts for this tenant, ordered by display_order
        $shifts = \App\Models\ExamShift::forTenant($tenant->id)
            ->active()
            ->ordered()
            ->get();

        // Create shift ID to index mapping (for color assignment)
        $shiftColorIndex = [];
        foreach ($shifts as $index => $shift) {
            $shiftColorIndex[$shift->id] = $index;
        }

        // Prepare data for different views
        $dateWiseData = collect();
        $dateClassData = collect();
        $calendarData = [];

        if ($schedules->count() > 0) {
            // Date-wise grouping with shift-based columns
            $dateGrouped = [];
            foreach ($schedules as $schedule) {
                if (!$schedule->exam_date) continue;
                $dateKey = $schedule->exam_date->format('Y-m-d');
                if (!isset($dateGrouped[$dateKey])) {
                    $dateGrouped[$dateKey] = [
                        'date' => $schedule->exam_date,
                        'count' => 0,
                        'shifts' => []
                    ];
                }
                $dateGrouped[$dateKey]['count']++;
                $shiftId = $schedule->shift ? $schedule->shift->id : null;
                $shiftName = $schedule->shift ? $schedule->shift->shift_name : 'No Shift';
                $shiftIndex = isset($shiftColorIndex[$shiftId]) ? $shiftColorIndex[$shiftId] : 999;

                if (!isset($dateGrouped[$dateKey]['shifts'][$shiftIndex])) {
                    $dateGrouped[$dateKey]['shifts'][$shiftIndex] = [
                        'shift_name' => $shiftName,
                        'shift_id' => $shiftId,
                        'schedules' => []
                    ];
                }

                $dateGrouped[$dateKey]['shifts'][$shiftIndex]['schedules'][] = [
                    'id' => $schedule->id,
                    'subject' => $schedule->subject ? $schedule->subject->subject_name : 'N/A',
                    'subject_code' => $schedule->subject ? $schedule->subject->subject_code : null,
                    'time' => $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') : 'N/A',
                    'end_time' => $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') : null,
                    'shift' => $shiftName,
                    'shift_id' => $shiftId,
                    'class' => $schedule->schoolClass ? $schedule->schoolClass->class_name : 'N/A',
                    'section' => $schedule->section ? $schedule->section->section_name : null,
                    'duration' => $schedule->duration_minutes ?? null,
                    'supervisor' => $schedule->supervisor ? $schedule->supervisor->full_name : null,
                ];
            }
            // Sort schedules within each shift by class_numeric
            $compareFn = [$this, 'compareClassesByNumeric'];
            foreach ($dateGrouped as $dateKey => &$dayData) {
                ksort($dayData['shifts']); // Sort shifts by index
                foreach ($dayData['shifts'] as &$shiftData) {
                    usort($shiftData['schedules'], function($a, $b) use ($classesMap, $compareFn) {
                        $aClass = $a['class'] ?? 'N/A';
                        $bClass = $b['class'] ?? 'N/A';
                        return call_user_func($compareFn, $aClass, $bClass, $classesMap);
                    });
                }
                unset($shiftData);
            }
            unset($dayData);

            $dateWiseData = collect($dateGrouped)->sortBy(function($item) {
                return $item['date'] ? $item['date']->format('U') : 0;
            })->values();

            // Date, Shift, and Class grouping - Table format (dates -> shifts -> classes as columns)
            $dateClassTable = [];
            $allClasses = [];
            $allDates = [];
            $allShifts = [];

            // First pass: collect all unique dates, classes, and shifts
            foreach ($schedules as $schedule) {
                if (!$schedule->exam_date) continue;
                $dateKey = $schedule->exam_date->format('Y-m-d');
                $classKey = $schedule->schoolClass->class_name ?? 'N/A';
                $shiftKey = $schedule->shift->shift_name ?? 'No Shift';

                if (!in_array($dateKey, $allDates)) {
                    $allDates[] = $dateKey;
                }
                if (!in_array($classKey, $allClasses)) {
                    $allClasses[] = $classKey;
                }
                if (!in_array($shiftKey, $allShifts)) {
                    $allShifts[] = $shiftKey;
                }
            }

            // Sort dates
            sort($allDates);

            // Sort classes by class_numeric (using database ordering)
            usort($allClasses, function($a, $b) use ($classesMap) {
                return $this->compareClassesByNumeric($a, $b, $classesMap);
            });

            // Sort shifts
            sort($allShifts);

            // Second pass: populate table structure (date -> shift -> class -> schedules)
            foreach ($schedules as $schedule) {
                // Skip if schedule is not an object or doesn't have exam_date
                if (!is_object($schedule) || !$schedule->exam_date) continue;

                $dateKey = $schedule->exam_date->format('Y-m-d');
                $classKey = ($schedule->schoolClass && $schedule->schoolClass->class_name) ? $schedule->schoolClass->class_name : 'N/A';
                $shiftKey = ($schedule->shift && $schedule->shift->shift_name) ? $schedule->shift->shift_name : 'No Shift';

                if (!isset($dateClassTable[$dateKey])) {
                    $dateClassTable[$dateKey] = [
                        'date' => $schedule->exam_date,
                        'shifts' => []
                    ];
                }

                if (!isset($dateClassTable[$dateKey]['shifts'][$shiftKey])) {
                    $dateClassTable[$dateKey]['shifts'][$shiftKey] = [
                        'shift_name' => $shiftKey,
                        'classes' => []
                    ];
                }

                if (!isset($dateClassTable[$dateKey]['shifts'][$shiftKey]['classes'][$classKey])) {
                    $dateClassTable[$dateKey]['shifts'][$shiftKey]['classes'][$classKey] = [];
                }

                $shiftId = $schedule->shift ? $schedule->shift->id : null;
                $dateClassTable[$dateKey]['shifts'][$shiftKey]['classes'][$classKey][] = [
                    'id' => $schedule->id,
                    'subject' => $schedule->subject ? $schedule->subject->subject_name : 'N/A',
                    'subject_code' => $schedule->subject ? $schedule->subject->subject_code : null,
                    'time' => $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') : 'N/A',
                    'end_time' => $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') : null,
                    'section' => $schedule->section ? $schedule->section->section_name : null,
                    'duration' => $schedule->duration_minutes ?? null,
                    'supervisor' => $schedule->supervisor ? $schedule->supervisor->full_name : null,
                    'shift' => $schedule->shift ? $schedule->shift->shift_name : null,
                    'shift_id' => $shiftId,
                ];
            }

            $dateClassData = [
                'dates' => $allDates,
                'classes' => $allClasses,
                'shifts' => $allShifts,
                'table' => $dateClassTable
            ];

            // Calendar data (for calendar view)
            foreach ($schedules as $schedule) {
                if (!$schedule->exam_date) continue;
                $dateKey = $schedule->exam_date->format('Y-m-d');
                if (!isset($calendarData[$dateKey])) {
                    $calendarData[$dateKey] = [];
                }
                $shiftId = $schedule->shift ? $schedule->shift->id : null;
                $calendarData[$dateKey][] = [
                    'id' => $schedule->id,
                    'subject' => $schedule->subject ? $schedule->subject->subject_name : 'N/A',
                    'time' => $schedule->start_time ? \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') : 'N/A',
                    'class' => $schedule->schoolClass ? $schedule->schoolClass->class_name : 'N/A',
                    'section' => $schedule->section ? $schedule->section->section_name : null,
                    'shift_id' => $shiftId,
                ];
            }

            // Sort calendar data by class_numeric within each date (using database ordering)
            $compareFn = [$this, 'compareClassesByNumeric'];
            foreach ($calendarData as $dateKey => &$dateSchedules) {
                usort($dateSchedules, function($a, $b) use ($classesMap, $compareFn) {
                    $aClass = $a['class'] ?? 'N/A';
                    $bClass = $b['class'] ?? 'N/A';
                    return call_user_func($compareFn, $aClass, $bClass, $classesMap);
                });
            }
            unset($dateSchedules);
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

        // Debug: Log schedule count
        \Log::info('Exam Show - Schedules Count', [
            'exam_id' => $exam->id,
            'total_schedules' => $schedules->count(),
            'has_date_filter' => $request->hasAny(['date_from', 'date_to']),
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
        ]);

        return view('tenant.admin.examinations.exams.show', compact('exam', 'stats', 'tenant', 'dateWiseData', 'dateClassData', 'calendarData', 'schedules', 'request', 'shiftColorIndex'));
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
                'admit_card_enabled' => $request->has('admit_card_enabled') ? (bool)$request->admit_card_enabled : ($exam->admit_card_enabled ?? true),
                'result_enabled' => $request->has('result_enabled') ? (bool)$request->result_enabled : ($exam->result_enabled ?? true),
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
     * Compare two classes by their class_numeric value from database
     * Falls back to alphabetical if class_numeric is not available
     *
     * @param string $a First class name
     * @param string $b Second class name
     * @param \Illuminate\Support\Collection $classesMap Map of class_name => class data
     * @return int Comparison result (-1, 0, or 1)
     */
    protected function compareClassesByNumeric($a, $b, $classesMap)
    {
        $aData = $classesMap->get($a);
        $bData = $classesMap->get($b);

        // If both classes have numeric values, compare by numeric
        if ($aData && $bData) {
            $aNumeric = $aData['class_numeric'] ?? 999999;
            $bNumeric = $bData['class_numeric'] ?? 999999;

            if ($aNumeric != $bNumeric) {
                return $aNumeric <=> $bNumeric;
            }
            // If numeric values are equal, compare by name
            return strcmp($a, $b);
        }

        // If only one has data, prioritize the one with data
        if ($aData && !$bData) {
            return -1;
        }
        if (!$aData && $bData) {
            return 1;
        }

        // If neither has data, compare alphabetically
        return strcmp($a, $b);
    }

}

