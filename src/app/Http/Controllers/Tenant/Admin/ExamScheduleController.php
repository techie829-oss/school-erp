<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\StudentSubject;
use App\Models\TenantSetting;
use App\Models\Teacher;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExamScheduleController extends Controller
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
     * Display a listing of exam schedules
     */
    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);

        // Require exam_id - redirect to exams if not provided
        if (!$request->has('exam_id') || !$request->exam_id) {
            return redirect(url('/admin/examinations/exams'))
                ->with('info', 'Please select an exam to view schedules.');
        }

        $exam = Exam::forTenant($tenant->id)->findOrFail($request->exam_id);

        $query = ExamSchedule::forTenant($tenant->id)
            ->where('exam_id', $exam->id)
            ->with(['exam', 'subject', 'schoolClass', 'section.schoolClass', 'supervisor', 'shift']);

        // Filter by class
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by subject
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('exam_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('exam_date', '<=', $request->date_to);
        }

        // Get total count before pagination for debugging
        $totalCount = $query->count();

        // Sorting
        $sortBy = $request->get('sort_by', 'exam_date');
        $sortOrder = $request->get('sort_order', 'asc');

        if ($sortBy === 'exam_date') {
            // Order by exam_date (NULLs last), then by start_time
            $query->orderByRaw('exam_date IS NULL, exam_date ' . strtoupper($sortOrder))
                  ->orderBy('start_time', $sortOrder);
        } elseif ($sortBy === 'subject') {
            $query->join('subjects', 'exam_schedules.subject_id', '=', 'subjects.id')
                  ->orderBy('subjects.subject_name', $sortOrder)
                  ->orderByRaw('exam_date IS NULL, exam_date ASC')
                  ->orderBy('start_time', 'asc')
                  ->select('exam_schedules.*');
        } elseif ($sortBy === 'class') {
            $query->join('classes', 'exam_schedules.class_id', '=', 'classes.id')
                  ->orderBy('classes.class_numeric', $sortOrder)
                  ->orderBy('classes.class_name', $sortOrder)
                  ->orderByRaw('exam_date IS NULL, exam_date ASC')
                  ->orderBy('start_time', 'asc')
                  ->select('exam_schedules.*');
        } elseif ($sortBy === 'time') {
            $query->orderBy('start_time', $sortOrder)
                  ->orderByRaw('exam_date IS NULL, exam_date ASC');
        } else {
            // Default: order by exam_date (NULLs last), then by start_time
            $query->orderByRaw('exam_date IS NULL, exam_date ' . strtoupper($sortOrder))
                  ->orderBy('start_time', $sortOrder);
        }

        $schedules = $query->paginate(20)->withQueryString();

        // Log for debugging (can be removed later)
        \Log::info('Exam Schedules Query', [
            'exam_id' => $exam->id,
            'total_schedules' => $totalCount,
            'showing' => $schedules->count(),
            'has_filters' => $request->hasAny(['class_id', 'subject_id', 'date_from', 'date_to']),
        ]);

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();

        // Get all exams for the filter dropdown
        $exams = Exam::forTenant($tenant->id)
            ->orderBy('exam_name')
            ->get();

        return view('tenant.admin.examinations.schedules.index', compact('schedules', 'exam', 'classes', 'subjects', 'tenant', 'exams'));
    }

    /**
     * Show the form for creating a new exam schedule
     */
    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);

        $examId = $request->get('exam_id');

        // If no exam_id provided, show exam selection page
        if (!$examId) {
            $exams = Exam::forTenant($tenant->id)
                ->orderBy('exam_name')
                ->get();

            return view('tenant.admin.examinations.schedules.select-exam', compact('exams', 'tenant'));
        }

        $exam = Exam::forTenant($tenant->id)->findOrFail($examId);
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->with('sections')->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();
        $teachers = Teacher::forTenant($tenant->id)->active()->get();
        $shifts = \App\Models\ExamShift::forTenant($tenant->id)->active()->ordered()->get();

        // Get subject assignment settings
        $academicSettings = TenantSetting::getAllForTenant($tenant->id, 'academic');
        $classSubjectMode = $academicSettings['class_subject_assignment_mode'] ?? 'class_wise';
        $sectionSubjectMode = $academicSettings['section_subject_assignment_mode'] ?? 'section_wise';

        // Get class-subject mappings (common subjects for all sections)
        // Get section-subject mappings (section-specific subjects)
        $classSubjects = [];
        $sectionSubjects = [];

        foreach ($classes as $class) {
            // Get subjects based on assignment mode
            if ($classSubjectMode === 'student_wise') {
                // For student-wise: get all unique subjects from students in this class
                $enrollmentIds = \App\Models\ClassEnrollment::forTenant($tenant->id)
                    ->where('class_id', $class->id)
                    ->where('is_current', true)
                    ->pluck('student_id');

                $academicYear = $exam->academic_year ?? date('Y') . '-' . (date('Y') + 1);
                $studentSubjectIds = StudentSubject::forTenant($tenant->id)
                    ->whereIn('student_id', $enrollmentIds)
                    ->where('academic_year', $academicYear)
                    ->active()
                    ->distinct()
                    ->pluck('subject_id')
                    ->toArray();

                // If no student subjects found and student-wise is enabled, show ALL subjects
                // This allows admins to select any subject when students haven't been assigned yet
                if (empty($studentSubjectIds)) {
                    // Show all active subjects for flexibility
                    $classSubjects[$class->id] = Subject::forTenant($tenant->id)->active()->pluck('id')->toArray();
                } else {
                    $classSubjects[$class->id] = $studentSubjectIds;
                }
            } else {
                // Class-wise: get subjects from class
                $classSubjects[$class->id] = $class->subjects()->pluck('subjects.id')->toArray();
            }

            // For classes with sections, also get section-subject mappings
            if ($class->has_sections) {
                foreach ($class->sections as $section) {
                    if ($sectionSubjectMode === 'student_wise') {
                        // For student-wise: get all unique subjects from students in this section
                        $enrollmentIds = \App\Models\ClassEnrollment::forTenant($tenant->id)
                            ->where('section_id', $section->id)
                            ->where('is_current', true)
                            ->pluck('student_id');

                        $academicYear = $exam->academic_year ?? date('Y') . '-' . (date('Y') + 1);
                        $studentSubjectIds = StudentSubject::forTenant($tenant->id)
                            ->whereIn('student_id', $enrollmentIds)
                            ->where('academic_year', $academicYear)
                            ->active()
                            ->distinct()
                            ->pluck('subject_id')
                            ->toArray();

                        // If no student subjects found and student-wise is enabled, show ALL subjects
                        // This allows admins to select any subject when students haven't been assigned yet
                        if (empty($studentSubjectIds)) {
                            // Show all active subjects for flexibility
                            $sectionSubjects[$section->id] = Subject::forTenant($tenant->id)->active()->pluck('id')->toArray();
                        } else {
                            $sectionSubjects[$section->id] = $studentSubjectIds;
                        }
                    } else {
                        // Section-wise: get subjects from section
                        $sectionSubjects[$section->id] = $section->subjects()->pluck('subjects.id')->toArray();
                    }
                }
            }
        }

        return view('tenant.admin.examinations.schedules.create', compact('exam', 'classes', 'subjects', 'teachers', 'tenant', 'classSubjects', 'sectionSubjects', 'classSubjectMode', 'sectionSubjectMode'));
    }

    /**
     * Store a newly created exam schedule
     */
    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'exam_id' => [
                'required',
                Rule::exists('exams', 'id')->where('tenant_id', $tenant->id),
            ],
            'subject_id' => [
                'required',
                Rule::exists('subjects', 'id')->where('tenant_id', $tenant->id),
            ],
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')->where('tenant_id', $tenant->id),
            ],
            'section_id' => [
                'nullable',
                Rule::exists('sections', 'id')->where('tenant_id', $tenant->id),
            ],
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_minutes' => 'nullable|integer|min:1',
            'room_number' => 'nullable|string|max:50',
            'max_marks' => 'required|numeric|min:0',
            'passing_marks' => 'nullable|numeric|min:0|max:max_marks',
            'instructions' => 'nullable|string',
            'supervisor_id' => [
                'nullable',
                Rule::exists('teachers', 'id')->where('tenant_id', $tenant->id),
            ],
            'shift_id' => [
                'nullable',
                Rule::exists('exam_shifts', 'id')->where('tenant_id', $tenant->id),
            ],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            DB::beginTransaction();

            // Calculate duration if not provided
            $duration = $request->duration_minutes;
            if (!$duration) {
                $start = \Carbon\Carbon::parse($request->start_time);
                $end = \Carbon\Carbon::parse($request->end_time);
                $duration = $start->diffInMinutes($end);
            }

            $schedule = ExamSchedule::create([
                'tenant_id' => $tenant->id,
                'exam_id' => $request->exam_id,
                'shift_id' => $request->shift_id ?? null,
                'subject_id' => $request->subject_id,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'exam_date' => $request->exam_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration_minutes' => $duration,
                'room_number' => $request->room_number,
                'max_marks' => $request->max_marks,
                'passing_marks' => $request->passing_marks,
                'instructions' => $request->instructions,
                'supervisor_id' => $request->supervisor_id,
            ]);

            DB::commit();

            return redirect(url('/admin/examinations/schedules'))->with('success', 'Exam schedule created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create exam schedule: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified exam schedule
     */
    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $schedule = ExamSchedule::forTenant($tenant->id)
            ->with(['exam', 'subject', 'schoolClass', 'section'])
            ->findOrFail($id);

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();
        $teachers = Teacher::forTenant($tenant->id)->active()->get();
        $shifts = \App\Models\ExamShift::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.examinations.schedules.edit', compact('schedule', 'classes', 'subjects', 'teachers', 'shifts', 'tenant'));
    }

    /**
     * Update the specified exam schedule
     */
    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $schedule = ExamSchedule::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'subject_id' => [
                'required',
                Rule::exists('subjects', 'id')->where('tenant_id', $tenant->id),
            ],
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')->where('tenant_id', $tenant->id),
            ],
            'section_id' => [
                'nullable',
                Rule::exists('sections', 'id')->where('tenant_id', $tenant->id),
            ],
            'exam_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_minutes' => 'nullable|integer|min:1',
            'room_number' => 'nullable|string|max:50',
            'max_marks' => 'required|numeric|min:0',
            'passing_marks' => 'nullable|numeric|min:0|max:max_marks',
            'instructions' => 'nullable|string',
            'supervisor_id' => [
                'nullable',
                Rule::exists('teachers', 'id')->where('tenant_id', $tenant->id),
            ],
            'shift_id' => [
                'nullable',
                Rule::exists('exam_shifts', 'id')->where('tenant_id', $tenant->id),
            ],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        try {
            DB::beginTransaction();

            // Calculate duration if not provided
            $duration = $request->duration_minutes;
            if (!$duration) {
                $start = \Carbon\Carbon::parse($request->start_time);
                $end = \Carbon\Carbon::parse($request->end_time);
                $duration = $start->diffInMinutes($end);
            }

            $schedule->update([
                'shift_id' => $request->shift_id ?? null,
                'subject_id' => $request->subject_id,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'exam_date' => $request->exam_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'duration_minutes' => $duration,
                'room_number' => $request->room_number,
                'max_marks' => $request->max_marks,
                'passing_marks' => $request->passing_marks,
                'instructions' => $request->instructions,
                'supervisor_id' => $request->supervisor_id,
            ]);

            DB::commit();

            return redirect(url('/admin/examinations/schedules'))->with('success', 'Exam schedule updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update exam schedule: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified exam schedule
     */
    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $schedule = ExamSchedule::forTenant($tenant->id)->findOrFail($id);

        // Check if schedule has results
        if ($schedule->examResults()->count() > 0) {
            return back()->with('error', 'Cannot delete schedule with existing results. Please delete results first.');
        }

        try {
            $schedule->delete();
            return redirect(url('/admin/examinations/schedules'))->with('success', 'Exam schedule deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete exam schedule: ' . $e->getMessage());
        }
    }

    /**
     * Delete all schedules for a specific exam
     */
    public function bulkDestroy(Request $request)
    {
        $tenant = $this->getTenant($request);

        $examId = $request->input('exam_id');

        if (!$examId) {
            return back()->with('error', 'Exam ID is required.');
        }

        $exam = Exam::forTenant($tenant->id)->findOrFail($examId);

        // Get all schedules for this exam
        $schedules = ExamSchedule::forTenant($tenant->id)
            ->where('exam_id', $examId)
            ->get();

        // Check if any schedule has results
        $schedulesWithResults = $schedules->filter(function($schedule) {
            return $schedule->examResults()->count() > 0;
        });

        if ($schedulesWithResults->count() > 0) {
            return back()->with('error', 'Cannot delete schedules. ' . $schedulesWithResults->count() . ' schedule(s) have existing results. Please delete results first.');
        }

        try {
            $count = $schedules->count();

            ExamSchedule::forTenant($tenant->id)
                ->where('exam_id', $examId)
                ->delete();

            return back()->with('success', "Successfully deleted {$count} exam schedule(s). You can now regenerate them.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete exam schedules: ' . $e->getMessage());
        }
    }

    /**
     * Bulk create exam schedules
     */
    public function bulkCreate(Request $request)
    {
        $tenant = $this->getTenant($request);

        $examId = $request->get('exam_id');

        if (!$examId) {
            return redirect(url('/admin/examinations/exams'))
                ->with('error', 'Please select an exam first.');
        }

        $exam = Exam::forTenant($tenant->id)->findOrFail($examId);
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();
        $teachers = Teacher::forTenant($tenant->id)->active()->get();

        return view('tenant.admin.examinations.schedules.bulk-create', compact('exam', 'classes', 'subjects', 'teachers', 'tenant'));
    }

    /**
     * Store bulk exam schedules
     */
    public function bulkStore(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'exam_id' => [
                'required',
                Rule::exists('exams', 'id')->where('tenant_id', $tenant->id),
            ],
            'schedules' => 'required|array|min:1',
            'schedules.*.subject_id' => [
                'required',
                Rule::exists('subjects', 'id')->where('tenant_id', $tenant->id),
            ],
            'schedules.*.class_id' => [
                'required',
                Rule::exists('classes', 'id')->where('tenant_id', $tenant->id),
            ],
            'schedules.*.section_id' => [
                'nullable',
                Rule::exists('sections', 'id')->where('tenant_id', $tenant->id),
            ],
            'schedules.*.exam_date' => 'required|date',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i',
            'schedules.*.max_marks' => 'required|numeric|min:0',
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
            foreach ($request->schedules as $scheduleData) {
                $start = \Carbon\Carbon::parse($scheduleData['start_time']);
                $end = \Carbon\Carbon::parse($scheduleData['end_time']);
                $duration = $start->diffInMinutes($end);

                ExamSchedule::create([
                    'tenant_id' => $tenant->id,
                    'exam_id' => $request->exam_id,
                    'shift_id' => $scheduleData['shift_id'] ?? null,
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

            return redirect(url('/admin/examinations/schedules'))->with('success', "Successfully created {$created} exam schedule(s)!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create exam schedules: ' . $e->getMessage());
        }
    }

    /**
     * Smart bulk create exam schedules (4-step wizard)
     */
    public function smartBulkCreate(Request $request)
    {
        $tenant = $this->getTenant($request);

        $examId = $request->get('exam_id');

        if (!$examId) {
            return redirect(url('/admin/examinations/exams'))
                ->with('error', 'Please select an exam first.');
        }

        $exam = Exam::forTenant($tenant->id)->findOrFail($examId);

        // Get all classes with sections and student counts
        $classes = SchoolClass::forTenant($tenant->id)
            ->active()
            ->ordered()
            ->with(['sections' => function($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['enrollments as student_count' => function($q) {
                $q->where('is_current', true);
            }])
            ->get();

        // Add student count to each section
        foreach ($classes as $class) {
            foreach ($class->sections as $section) {
                $section->student_count = \App\Models\ClassEnrollment::forTenant($tenant->id)
                    ->where('section_id', $section->id)
                    ->where('is_current', true)
                    ->count();
            }
        }

        // Get all subjects
        $subjects = Subject::forTenant($tenant->id)->active()->orderBy('subject_name')->get();

        // Get subject assignment settings
        $academicSettings = TenantSetting::getAllForTenant($tenant->id, 'academic');
        $classSubjectMode = $academicSettings['class_subject_assignment_mode'] ?? 'class_wise';
        $sectionSubjectMode = $academicSettings['section_subject_assignment_mode'] ?? 'section_wise';

        // Get class-subject and section-subject mappings
        $classSubjects = [];
        $sectionSubjects = [];

        foreach ($classes as $class) {
            // Get subjects based on assignment mode
            if ($classSubjectMode === 'student_wise') {
                // For student-wise: get all unique subjects from students in this class
                $enrollmentIds = \App\Models\ClassEnrollment::forTenant($tenant->id)
                    ->where('class_id', $class->id)
                    ->where('is_current', true)
                    ->pluck('student_id');

                $academicYear = $exam->academic_year ?? date('Y') . '-' . (date('Y') + 1);
                $studentSubjectIds = StudentSubject::forTenant($tenant->id)
                    ->whereIn('student_id', $enrollmentIds)
                    ->where('academic_year', $academicYear)
                    ->active()
                    ->distinct()
                    ->pluck('subject_id')
                    ->toArray();

                // If no student subjects found and student-wise is enabled, show ALL subjects
                // This allows admins to select any subject when students haven't been assigned yet
                if (empty($studentSubjectIds)) {
                    // Show all active subjects for flexibility
                    $classSubjects[$class->id] = Subject::forTenant($tenant->id)->active()->pluck('id')->toArray();
                } else {
                    $classSubjects[$class->id] = $studentSubjectIds;
                }
            } else {
                // Class-wise: get subjects from class
                $classSubjects[$class->id] = $class->subjects()->pluck('subjects.id')->toArray();
            }

            // Section-specific subjects
            if ($class->has_sections) {
                foreach ($class->sections as $section) {
                    if ($sectionSubjectMode === 'student_wise') {
                        // For student-wise: get all unique subjects from students in this section
                        $enrollmentIds = \App\Models\ClassEnrollment::forTenant($tenant->id)
                            ->where('section_id', $section->id)
                            ->where('is_current', true)
                            ->pluck('student_id');

                        $academicYear = $exam->academic_year ?? date('Y') . '-' . (date('Y') + 1);
                        $studentSubjectIds = StudentSubject::forTenant($tenant->id)
                            ->whereIn('student_id', $enrollmentIds)
                            ->where('academic_year', $academicYear)
                            ->active()
                            ->distinct()
                            ->pluck('subject_id')
                            ->toArray();

                        // If no student subjects found and student-wise is enabled, show ALL subjects
                        // This allows admins to select any subject when students haven't been assigned yet
                        if (empty($studentSubjectIds)) {
                            // Show all active subjects for flexibility
                            $sectionSubjects[$section->id] = Subject::forTenant($tenant->id)->active()->pluck('id')->toArray();
                        } else {
                            $sectionSubjects[$section->id] = $studentSubjectIds;
                        }
                    } else {
                        // Section-wise: get subjects from section
                        $sectionSubjects[$section->id] = $section->subjects()->pluck('subjects.id')->toArray();
                    }
                }
            }
        }

        // Get teachers for supervisor assignment
        $teachers = Teacher::forTenant($tenant->id)
            ->where('is_active', true)
            ->where('status', 'active')
            ->orderBy('full_name')
            ->get();

        // Get active exam shifts and format for JavaScript
        $shiftsRaw = \App\Models\ExamShift::forTenant($tenant->id)->active()->ordered()->get();
        $shifts = $shiftsRaw->map(function($shift) {
            return [
                'id' => $shift->id,
                'shift_name' => $shift->shift_name,
                'start_time' => $shift->start_time ? $shift->start_time->format('H:i') : '09:00',
                'end_time' => $shift->end_time ? $shift->end_time->format('H:i') : '11:00',
                'duration_minutes' => $shift->duration_minutes,
                'class_ranges' => $shift->class_ranges,
            ];
        });

        return view('tenant.admin.examinations.schedules.smart-bulk-create', compact(
            'exam',
            'classes',
            'subjects',
            'teachers',
            'shifts',
            'tenant',
            'classSubjects',
            'sectionSubjects',
            'classSubjectMode',
            'sectionSubjectMode'
        ));
    }

    /**
     * Store smart bulk exam schedules
     */
    public function smartBulkStore(Request $request)
    {
        $tenant = $this->getTenant($request);

        // Get schedules directly from request
        $schedules = $request->input('schedules', []);

        // If no schedules, return error
        if (empty($schedules)) {
            return back()
                ->withInput()
                ->with('error', 'No schedules found. Please check your selections and try again.');
        }

        $validator = Validator::make($request->all(), [
            'exam_id' => [
                'required',
                Rule::exists('exams', 'id')->where('tenant_id', $tenant->id),
            ],
            'class_ids' => 'nullable|array', // Optional - schedules array contains class_id
            'class_ids.*' => [
                'nullable',
                Rule::exists('classes', 'id')->where('tenant_id', $tenant->id),
            ],
            'section_ids' => 'nullable|array',
            'section_ids.*' => [
                'nullable',
                Rule::exists('sections', 'id')->where('tenant_id', $tenant->id),
            ],
            'subject_ids' => 'nullable|array', // Optional - schedules array contains subject_id
            'subject_ids.*' => [
                'nullable',
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
            'schedules.*.shift_id' => [
                'nullable',
                Rule::exists('exam_shifts', 'id')->where('tenant_id', $tenant->id),
            ],
            'schedules.*.exam_date' => 'required|date',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.max_marks' => 'required|numeric|min:0',
            'schedules.*.passing_marks' => 'nullable|numeric|min:0',
            'schedules.*.room_number' => 'nullable|string|max:50',
            'schedules.*.supervisor_id' => [
                'nullable',
                Rule::exists('teachers', 'id')->where('tenant_id', $tenant->id),
            ],
        ]);


        try {
            DB::beginTransaction();


            $exam = Exam::forTenant($tenant->id)->findOrFail($request->exam_id);
            $created = 0;
            $conflicts = [];
            $skipped = 0;

            foreach ($request->schedules as $index => $scheduleData) {
                try {
                    // Check for conflicts (same room, same time, same date)
                    $roomNumber = $scheduleData['room_number'] ?? null;
                    $sectionId = $scheduleData['section_id'] ?? null;

                    $conflict = ExamSchedule::forTenant($tenant->id)
                        ->where('exam_date', $scheduleData['exam_date'])
                        ->where('start_time', $scheduleData['start_time'])
                        ->where(function($q) use ($scheduleData, $roomNumber, $sectionId) {
                            // Check room conflict if room_number is provided
                            if (!empty($roomNumber)) {
                                $q->where('room_number', $roomNumber);
                            }

                            // Check class/section conflict
                            $q->orWhere(function($q2) use ($scheduleData, $sectionId) {
                                $q2->where('class_id', $scheduleData['class_id'])
                                   ->where(function($q3) use ($sectionId) {
                                       if (!empty($sectionId)) {
                                           $q3->where('section_id', $sectionId);
                                       } else {
                                           $q3->whereNull('section_id');
                                       }
                                   });
                            });
                        })
                        ->exists();

                    if ($conflict) {
                        $conflicts[] = $scheduleData;
                        $skipped++;
                        continue;
                    }

                    $start = \Carbon\Carbon::parse($scheduleData['start_time']);
                    $end = \Carbon\Carbon::parse($scheduleData['end_time']);
                    $duration = $start->diffInMinutes($end);


                    // Ensure section_id is explicitly null if not provided (for common subjects)
                    $sectionId = null;
                    if (isset($scheduleData['section_id']) && !empty($scheduleData['section_id'])) {
                        $sectionId = (int) $scheduleData['section_id'];
                    }

                    ExamSchedule::create([
                        'tenant_id' => $tenant->id,
                        'exam_id' => (int) $request->exam_id,
                        'shift_id' => !empty($scheduleData['shift_id']) ? (int) $scheduleData['shift_id'] : null,
                        'subject_id' => (int) $scheduleData['subject_id'],
                        'class_id' => (int) $scheduleData['class_id'],
                        'section_id' => $sectionId, // Explicitly null for common subjects, or section ID for section-specific
                        'exam_date' => $scheduleData['exam_date'],
                        'start_time' => $scheduleData['start_time'],
                        'end_time' => $scheduleData['end_time'],
                        'duration_minutes' => $duration,
                        'room_number' => $scheduleData['room_number'] ?? null,
                        'max_marks' => (float) $scheduleData['max_marks'],
                        'passing_marks' => !empty($scheduleData['passing_marks']) ? (float) $scheduleData['passing_marks'] : null,
                        'instructions' => $scheduleData['instructions'] ?? null,
                        'supervisor_id' => !empty($scheduleData['supervisor_id']) ? (int) $scheduleData['supervisor_id'] : null,
                    ]);

                    $created++;
                } catch (\Exception $e) {
                    throw $e; // Re-throw to trigger rollback
                }
            }

            DB::commit();

            $message = "Successfully created {$created} exam schedule(s)!";
            if ($skipped > 0) {
                $message .= " {$skipped} schedule(s) skipped due to conflicts.";
            }

            return redirect(url('/admin/examinations/exams/' . $exam->id))
                ->with('success', $message)
                ->with('conflicts', $conflicts);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create exam schedules: ' . $e->getMessage());
        }
    }
}

