<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventParticipant;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventController extends Controller
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

        $view = $request->get('view', 'month'); // month, week, day, list
        $date = $request->get('date', now()->format('Y-m-d'));

        $query = Event::forTenant($tenant->id)
            ->with(['category', 'organizer', 'participants']);

        // Filter by search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by type
        if ($request->has('event_type') && $request->event_type) {
            $query->byType($request->event_type);
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->byCategory($request->category_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        } else {
            $query->whereIn('status', ['draft', 'published']);
        }

        // Date range filtering for calendar views
        if ($view === 'month') {
            $startDate = Carbon::parse($date)->startOfMonth();
            $endDate = Carbon::parse($date)->endOfMonth();
            $query->byDateRange($startDate, $endDate);
        } elseif ($view === 'week') {
            $startDate = Carbon::parse($date)->startOfWeek();
            $endDate = Carbon::parse($date)->endOfWeek();
            $query->byDateRange($startDate, $endDate);
        } elseif ($view === 'day') {
            $query->where(function($q) use ($date) {
                $q->where('start_date', $date)
                  ->orWhere(function($q2) use ($date) {
                      $q2->where('start_date', '<=', $date)
                         ->where(function($q3) use ($date) {
                             $q3->whereNull('end_date')
                                ->orWhere('end_date', '>=', $date);
                         });
                  });
            });
        }

        if ($view === 'list') {
            $events = $query->orderBy('start_date')->orderBy('start_time')->paginate(20)->withQueryString();
        } else {
            $events = $query->orderBy('start_date')->orderBy('start_time')->get();
        }

        $categories = EventCategory::forTenant($tenant->id)->active()->get();

        return view('tenant.admin.events.index', compact('events', 'categories', 'tenant', 'view', 'date'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        $categories = EventCategory::forTenant($tenant->id)->active()->get();

        return view('tenant.admin.events.create', compact('tenant', 'categories'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:event_categories,id',
            'event_type' => 'required|in:general,academic,sports,cultural,meeting,holiday',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published,cancelled,completed',
            'is_all_day' => 'boolean',
            'participants' => 'nullable|array',
            'participants.*.type' => 'required_with:participants|in:student,teacher,class,section,department,all',
            'participants.*.id' => 'required_if:participants.*.type,student,teacher,class,section,department',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $event = Event::create([
                'tenant_id' => $tenant->id,
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'category_id' => $request->input('category_id'),
                'event_type' => $request->input('event_type'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'location' => $request->input('location'),
                'organizer_id' => auth()->id(),
                'status' => $request->input('status'),
                'is_all_day' => $request->has('is_all_day') ? true : false,
            ]);

            // Handle participants
            if ($request->has('participants') && is_array($request->participants)) {
                foreach ($request->participants as $participant) {
                    if ($participant['type'] === 'all') {
                        EventParticipant::create([
                            'event_id' => $event->id,
                            'participant_type' => 'all',
                            'participant_id' => null,
                            'status' => 'invited',
                        ]);
                    } else {
                        EventParticipant::create([
                            'event_id' => $event->id,
                            'participant_type' => $participant['type'],
                            'participant_id' => $participant['id'] ?? null,
                            'status' => 'invited',
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect(url('/admin/events'))->with('success', 'Event created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create event: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $event = Event::forTenant($tenant->id)
            ->with(['category', 'organizer', 'participants.participant'])
            ->findOrFail($id);

        return view('tenant.admin.events.show', compact('event', 'tenant'));
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $event = Event::forTenant($tenant->id)
            ->with(['participants'])
            ->findOrFail($id);
        $categories = EventCategory::forTenant($tenant->id)->active()->get();

        return view('tenant.admin.events.edit', compact('event', 'categories', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $event = Event::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:event_categories,id',
            'event_type' => 'required|in:general,academic,sports,cultural,meeting,holiday',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published,cancelled,completed',
            'is_all_day' => 'boolean',
            'participants' => 'nullable|array',
            'participants.*.type' => 'required_with:participants|in:student,teacher,class,section,department,all',
            'participants.*.id' => 'required_if:participants.*.type,student,teacher,class,section,department',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $event->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'category_id' => $request->input('category_id'),
                'event_type' => $request->input('event_type'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'start_time' => $request->input('start_time'),
                'end_time' => $request->input('end_time'),
                'location' => $request->input('location'),
                'status' => $request->input('status'),
                'is_all_day' => $request->has('is_all_day') ? true : false,
            ]);

            // Handle participants - delete existing and create new
            if ($request->has('participants')) {
                $event->participants()->delete();

                if (is_array($request->participants)) {
                    foreach ($request->participants as $participant) {
                        if ($participant['type'] === 'all') {
                            EventParticipant::create([
                                'event_id' => $event->id,
                                'participant_type' => 'all',
                                'participant_id' => null,
                                'status' => 'invited',
                            ]);
                        } else {
                            EventParticipant::create([
                                'event_id' => $event->id,
                                'participant_type' => $participant['type'],
                                'participant_id' => $participant['id'] ?? null,
                                'status' => 'invited',
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return redirect(url('/admin/events'))->with('success', 'Event updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update event: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $event = Event::forTenant($tenant->id)->findOrFail($id);

        DB::beginTransaction();
        try {
            $event->participants()->delete();
            $event->delete();

            DB::commit();

            return redirect(url('/admin/events'))->with('success', 'Event deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete event: ' . $e->getMessage());
        }
    }
}

