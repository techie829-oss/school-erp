<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Timetable;
use App\Models\TimetablePeriod;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Period;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

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

    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);

        $query = Timetable::forTenant($tenant->id)
            ->with(['schoolClass', 'section']);

        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('section_id') && $request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->has('academic_year') && $request->academic_year) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $timetables = $query->latest()->paginate(20)->withQueryString();
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.timetable.classes.index', compact('timetables', 'classes', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();
        $teachers = Teacher::forTenant($tenant->id)->active()->get();
        $periods = Period::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.timetable.classes.create', compact('classes', 'subjects', 'teachers', 'periods', 'tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'academic_year' => 'required|string|max:50',
            'term' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,draft',
            'notes' => 'nullable|string|max:1000',
            'periods' => 'required|array|min:1',
            'periods.*.day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'periods.*.period_number' => 'required|integer|min:1',
            'periods.*.start_time' => 'required|date_format:H:i',
            'periods.*.end_time' => 'required|date_format:H:i|after:periods.*.start_time',
            'periods.*.subject_id' => 'required|exists:subjects,id',
            'periods.*.teacher_id' => 'nullable|exists:teachers,id',
            'periods.*.room' => 'nullable|string|max:50',
            'periods.*.notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $timetable = Timetable::create([
                'tenant_id' => $tenant->id,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'academic_year' => $request->academic_year,
                'term' => $request->term,
                'status' => $request->status,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->periods as $periodData) {
                TimetablePeriod::create([
                    'timetable_id' => $timetable->id,
                    'day' => $periodData['day'],
                    'period_number' => $periodData['period_number'],
                    'start_time' => $periodData['start_time'],
                    'end_time' => $periodData['end_time'],
                    'subject_id' => $periodData['subject_id'],
                    'teacher_id' => $periodData['teacher_id'] ?? null,
                    'room' => $periodData['room'] ?? null,
                    'notes' => $periodData['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect(url('/admin/timetable/classes'))->with('success', 'Timetable created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create timetable: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $timetable = Timetable::forTenant($tenant->id)
            ->with(['schoolClass', 'section', 'periods.subject', 'periods.teacher', 'creator'])
            ->findOrFail($id);

        // Group periods by day
        $periodsByDay = [
            'monday' => $timetable->periodsByDay('monday')->get(),
            'tuesday' => $timetable->periodsByDay('tuesday')->get(),
            'wednesday' => $timetable->periodsByDay('wednesday')->get(),
            'thursday' => $timetable->periodsByDay('thursday')->get(),
            'friday' => $timetable->periodsByDay('friday')->get(),
            'saturday' => $timetable->periodsByDay('saturday')->get(),
            'sunday' => $timetable->periodsByDay('sunday')->get(),
        ];

        return view('tenant.admin.timetable.classes.show', compact('timetable', 'periodsByDay', 'tenant'));
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $timetable = Timetable::forTenant($tenant->id)
            ->with(['periods'])
            ->findOrFail($id);

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $sections = Section::forTenant($tenant->id)->where('class_id', $timetable->class_id)->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();
        $teachers = Teacher::forTenant($tenant->id)->active()->get();
        $periods = Period::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.timetable.classes.edit', compact('timetable', 'classes', 'sections', 'subjects', 'teachers', 'periods', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $timetable = Timetable::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'academic_year' => 'required|string|max:50',
            'term' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,draft',
            'notes' => 'nullable|string|max:1000',
            'periods' => 'required|array|min:1',
            'periods.*.day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'periods.*.period_number' => 'required|integer|min:1',
            'periods.*.start_time' => 'required|date_format:H:i',
            'periods.*.end_time' => 'required|date_format:H:i|after:periods.*.start_time',
            'periods.*.subject_id' => 'required|exists:subjects,id',
            'periods.*.teacher_id' => 'nullable|exists:teachers,id',
            'periods.*.room' => 'nullable|string|max:50',
            'periods.*.notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $timetable->update([
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'academic_year' => $request->academic_year,
                'term' => $request->term,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            // Delete existing periods
            $timetable->periods()->delete();

            // Create new periods
            foreach ($request->periods as $periodData) {
                TimetablePeriod::create([
                    'timetable_id' => $timetable->id,
                    'day' => $periodData['day'],
                    'period_number' => $periodData['period_number'],
                    'start_time' => $periodData['start_time'],
                    'end_time' => $periodData['end_time'],
                    'subject_id' => $periodData['subject_id'],
                    'teacher_id' => $periodData['teacher_id'] ?? null,
                    'room' => $periodData['room'] ?? null,
                    'notes' => $periodData['notes'] ?? null,
                ]);
            }

            DB::commit();

            return redirect(url('/admin/timetable/classes'))->with('success', 'Timetable updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update timetable: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $timetable = Timetable::forTenant($tenant->id)->findOrFail($id);

        DB::beginTransaction();
        try {
            $timetable->periods()->delete();
            $timetable->delete();

            DB::commit();

            return redirect(url('/admin/timetable/classes'))->with('success', 'Timetable deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete timetable: ' . $e->getMessage());
        }
    }

    public function view(Request $request)
    {
        $tenant = $this->getTenant($request);
        $viewType = $request->get('type', 'class'); // class, teacher, room

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $teachers = Teacher::forTenant($tenant->id)->active()->get();
        $timetable = null;
        $teacherTimetable = null;
        $roomTimetable = null;

        if ($viewType === 'class' && $request->has('class_id') && $request->has('section_id')) {
            $timetable = Timetable::forTenant($tenant->id)
                ->where('class_id', $request->class_id)
                ->where(function($q) use ($request) {
                    $q->where('section_id', $request->section_id)
                      ->orWhereNull('section_id');
                })
                ->where('status', 'active')
                ->with(['periods.subject', 'periods.teacher', 'schoolClass', 'section'])
                ->first();
        } elseif ($viewType === 'teacher' && $request->has('teacher_id')) {
            // Get all timetables for this teacher
            $timetables = Timetable::forTenant($tenant->id)
                ->whereHas('periods', function($q) use ($request) {
                    $q->where('teacher_id', $request->teacher_id);
                })
                ->where('status', 'active')
                ->with(['schoolClass', 'section', 'periods' => function($q) use ($request) {
                    $q->where('teacher_id', $request->teacher_id)->with(['subject']);
                }])
                ->get();

            // Group by day
            $teacherTimetable = [];
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            foreach ($days as $day) {
                $teacherTimetable[$day] = [];
                foreach ($timetables as $tt) {
                    foreach ($tt->periods->where('day', $day) as $period) {
                        $teacherTimetable[$day][] = [
                            'period_number' => $period->period_number,
                            'start_time' => $period->start_time,
                            'end_time' => $period->end_time,
                            'subject' => $period->subject,
                            'class' => $tt->schoolClass,
                            'section' => $tt->section,
                            'room' => $period->room,
                        ];
                    }
                }
                usort($teacherTimetable[$day], function($a, $b) {
                    return $a['period_number'] <=> $b['period_number'];
                });
            }
        } elseif ($viewType === 'room' && $request->has('room')) {
            // Get all timetables for this room
            $timetables = Timetable::forTenant($tenant->id)
                ->whereHas('periods', function($q) use ($request) {
                    $q->where('room', $request->room);
                })
                ->where('status', 'active')
                ->with(['schoolClass', 'section', 'periods' => function($q) use ($request) {
                    $q->where('room', $request->room)->with(['subject', 'teacher']);
                }])
                ->get();

            // Group by day
            $roomTimetable = [];
            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            foreach ($days as $day) {
                $roomTimetable[$day] = [];
                foreach ($timetables as $tt) {
                    foreach ($tt->periods->where('day', $day) as $period) {
                        $roomTimetable[$day][] = [
                            'period_number' => $period->period_number,
                            'start_time' => $period->start_time,
                            'end_time' => $period->end_time,
                            'subject' => $period->subject,
                            'teacher' => $period->teacher,
                            'class' => $tt->schoolClass,
                            'section' => $tt->section,
                        ];
                    }
                }
                usort($roomTimetable[$day], function($a, $b) {
                    return $a['period_number'] <=> $b['period_number'];
                });
            }
        }

        return view('tenant.admin.timetable.view', compact('tenant', 'classes', 'teachers', 'timetable', 'teacherTimetable', 'roomTimetable', 'viewType'));
    }
}

