<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelRoom;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HostelRoomController extends Controller
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

        $query = HostelRoom::with(['hostel'])->whereHas('hostel', function($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        });

        if ($request->has('hostel_id') && $request->hostel_id) {
            $query->where('hostel_id', $request->hostel_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('room_type') && $request->room_type !== '') {
            $query->where('room_type', $request->room_type);
        }

        $rooms = $query->withCount('activeAllocations')->orderBy('hostel_id')->orderBy('room_number')->paginate(20)->withQueryString();
        $hostels = Hostel::forTenant($tenant->id)->active()->orderBy('name')->get();

        return view('tenant.admin.hostel.rooms.index', compact('rooms', 'hostels', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        $hostels = Hostel::forTenant($tenant->id)->active()->orderBy('name')->get();
        return view('tenant.admin.hostel.rooms.create', compact('hostels', 'tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'hostel_id' => 'required|exists:hostels,id',
            'room_number' => 'required|string|max:50|unique:hostel_rooms,room_number,NULL,id,hostel_id,' . $request->hostel_id,
            'room_type' => 'required|in:single,double,triple,dormitory',
            'capacity' => 'required|integer|min:1',
            'floor' => 'nullable|string|max:50',
            'facilities' => 'nullable|string|max:500',
            'status' => 'required|in:available,occupied,maintenance,reserved',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        HostelRoom::create([
            'hostel_id' => $request->hostel_id,
            'room_number' => $request->room_number,
            'room_type' => $request->room_type,
            'capacity' => $request->capacity,
            'available_beds' => $request->capacity,
            'floor' => $request->floor,
            'facilities' => $request->facilities,
            'status' => $request->status,
        ]);

        // Update hostel capacity
        $hostel = Hostel::find($request->hostel_id);
        $hostel->increment('capacity', $request->capacity);
        $hostel->increment('available_beds', $request->capacity);

        return redirect(url('/admin/hostel/rooms'))->with('success', 'Room created successfully.');
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $room = HostelRoom::with(['hostel', 'activeAllocations.student'])
            ->whereHas('hostel', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->id);
            })
            ->withCount('activeAllocations')
            ->findOrFail($id);

        return view('tenant.admin.hostel.rooms.show', compact('room', 'tenant'));
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $room = HostelRoom::with('hostel')
            ->whereHas('hostel', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->id);
            })
            ->findOrFail($id);
        $hostels = Hostel::forTenant($tenant->id)->active()->orderBy('name')->get();
        return view('tenant.admin.hostel.rooms.edit', compact('room', 'hostels', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $room = HostelRoom::with('hostel')
            ->whereHas('hostel', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->id);
            })
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'hostel_id' => 'required|exists:hostels,id',
            'room_number' => 'required|string|max:50|unique:hostel_rooms,room_number,' . $id . ',id,hostel_id,' . $request->hostel_id,
            'room_type' => 'required|in:single,double,triple,dormitory',
            'capacity' => 'required|integer|min:1',
            'floor' => 'nullable|string|max:50',
            'facilities' => 'nullable|string|max:500',
            'status' => 'required|in:available,occupied,maintenance,reserved',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $oldCapacity = $room->capacity;
        $capacityDiff = $request->capacity - $oldCapacity;
        $newAvailableBeds = max(0, $room->available_beds + $capacityDiff);

        $room->update([
            'hostel_id' => $request->hostel_id,
            'room_number' => $request->room_number,
            'room_type' => $request->room_type,
            'capacity' => $request->capacity,
            'available_beds' => $newAvailableBeds,
            'floor' => $request->floor,
            'facilities' => $request->facilities,
            'status' => $request->status,
        ]);

        // Update hostel capacity if changed
        if ($capacityDiff != 0) {
            $hostel = Hostel::find($request->hostel_id);
            $hostel->increment('capacity', $capacityDiff);
            $hostel->increment('available_beds', $capacityDiff);
        }

        return redirect(url('/admin/hostel/rooms'))->with('success', 'Room updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $room = HostelRoom::with(['hostel'])
            ->whereHas('hostel', function($q) use ($tenant) {
                $q->where('tenant_id', $tenant->id);
            })
            ->withCount('activeAllocations')
            ->findOrFail($id);

        if ($room->active_allocations_count > 0) {
            return back()->with('error', 'Cannot delete room with active student allocations.');
        }

        // Update hostel capacity
        $hostel = $room->hostel;
        $hostel->decrement('capacity', $room->capacity);
        $hostel->decrement('available_beds', $room->capacity);

        $room->delete();

        return redirect(url('/admin/hostel/rooms'))->with('success', 'Room deleted successfully.');
    }
}

