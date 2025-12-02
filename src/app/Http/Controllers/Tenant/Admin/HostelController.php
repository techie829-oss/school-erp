<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\Teacher;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HostelController extends Controller
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

        $query = Hostel::forTenant($tenant->id)->with(['warden']);

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('gender') && $request->gender !== '') {
            $query->where('gender', $request->gender);
        }

        $hostels = $query->withCount(['rooms', 'activeAllocations'])->orderBy('name')->paginate(20)->withQueryString();

        return view('tenant.admin.hostel.hostels.index', compact('hostels', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        $teachers = Teacher::forTenant($tenant->id)->active()->orderBy('full_name')->get();
        return view('tenant.admin.hostel.hostels.create', compact('teachers', 'tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'capacity' => 'required|integer|min:1',
            'warden_id' => 'nullable|exists:teachers,id',
            'contact_number' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'gender' => 'required|in:male,female,mixed',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Hostel::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'address' => $request->address,
            'capacity' => $request->capacity,
            'available_beds' => $request->capacity,
            'warden_id' => $request->warden_id,
            'contact_number' => $request->contact_number,
            'description' => $request->description,
            'gender' => $request->gender,
            'status' => $request->status,
        ]);

        return redirect(url('/admin/hostel/hostels'))->with('success', 'Hostel created successfully.');
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $hostel = Hostel::forTenant($tenant->id)
            ->with(['warden', 'rooms', 'activeAllocations.student', 'fees'])
            ->withCount(['rooms', 'activeAllocations'])
            ->findOrFail($id);

        return view('tenant.admin.hostel.hostels.show', compact('hostel', 'tenant'));
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $hostel = Hostel::forTenant($tenant->id)->findOrFail($id);
        $teachers = Teacher::forTenant($tenant->id)->active()->orderBy('full_name')->get();
        return view('tenant.admin.hostel.hostels.edit', compact('hostel', 'teachers', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $hostel = Hostel::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'capacity' => 'required|integer|min:1',
            'warden_id' => 'nullable|exists:teachers,id',
            'contact_number' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'gender' => 'required|in:male,female,mixed',
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Calculate available beds difference
        $capacityDiff = $request->capacity - $hostel->capacity;
        $newAvailableBeds = max(0, $hostel->available_beds + $capacityDiff);

        $hostel->update([
            'name' => $request->name,
            'address' => $request->address,
            'capacity' => $request->capacity,
            'available_beds' => $newAvailableBeds,
            'warden_id' => $request->warden_id,
            'contact_number' => $request->contact_number,
            'description' => $request->description,
            'gender' => $request->gender,
            'status' => $request->status,
        ]);

        return redirect(url('/admin/hostel/hostels'))->with('success', 'Hostel updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $hostel = Hostel::forTenant($tenant->id)->withCount('activeAllocations')->findOrFail($id);

        if ($hostel->active_allocations_count > 0) {
            return back()->with('error', 'Cannot delete hostel with active student allocations.');
        }

        $hostel->delete();

        return redirect(url('/admin/hostel/hostels'))->with('success', 'Hostel deleted successfully.');
    }

    public function getRooms(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $hostel = Hostel::forTenant($tenant->id)->findOrFail($id);
        
        $rooms = $hostel->rooms()
            ->available()
            ->select('id', 'room_number', 'room_type', 'available_beds')
            ->get();

        return response()->json($rooms);
    }
}

