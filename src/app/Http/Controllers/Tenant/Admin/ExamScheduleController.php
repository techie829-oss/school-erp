<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
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

        $query = ExamSchedule::forTenant($tenant->id)
            ->with(['exam', 'subject', 'schoolClass', 'section', 'supervisor']);

        // Filter by exam
        if ($request->has('exam_id') && $request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }

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

        $schedules = $query->orderBy('exam_date')->orderBy('start_time')->paginate(20)->withQueryString();

        $exams = Exam::forTenant($tenant->id)->where('status', '!=', 'archived')->get();
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();

        return view('tenant.admin.examinations.schedules.index', compact('schedules', 'exams', 'classes', 'subjects', 'tenant'));
    }

    /**
     * Show the form for creating a new exam schedule
     */
    public function create(Request $request)
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

        return view('tenant.admin.examinations.schedules.create', compact('exam', 'classes', 'subjects', 'teachers', 'tenant'));
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

        return view('tenant.admin.examinations.schedules.edit', compact('schedule', 'classes', 'subjects', 'teachers', 'tenant'));
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
}

