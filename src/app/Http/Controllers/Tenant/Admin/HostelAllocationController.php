<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelAllocation;
use App\Models\HostelRoom;
use App\Models\Student;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class HostelAllocationController extends Controller
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

        $query = HostelAllocation::forTenant($tenant->id)
            ->with(['student', 'hostel', 'room']);

        if ($request->has('hostel_id') && $request->hostel_id) {
            $query->where('hostel_id', $request->hostel_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        } else {
            $query->where('status', 'active');
        }

        if ($request->has('student_id') && $request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        $allocations = $query->latest('allocation_date')->paginate(20)->withQueryString();
        $hostels = Hostel::forTenant($tenant->id)->active()->orderBy('name')->get();

        return view('tenant.admin.hostel.allocations.index', compact('allocations', 'hostels', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        $hostels = Hostel::forTenant($tenant->id)->active()->orderBy('name')->get();
        $students = Student::forTenant($tenant->id)->active()->orderBy('full_name')->get();
        return view('tenant.admin.hostel.allocations.create', compact('hostels', 'students', 'tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'hostel_id' => 'required|exists:hostels,id',
            'room_id' => 'required|exists:hostel_rooms,id',
            'bed_number' => 'nullable|integer|min:1',
            'allocation_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if student already has active allocation
        $existingAllocation = HostelAllocation::forTenant($tenant->id)
            ->where('student_id', $request->student_id)
            ->where('status', 'active')
            ->first();

        if ($existingAllocation) {
            return back()->with('error', 'Student already has an active hostel allocation.')->withInput();
        }

        // Check room availability
        $room = HostelRoom::findOrFail($request->room_id);
        if ($room->available_beds <= 0) {
            return back()->with('error', 'Selected room has no available beds.')->withInput();
        }

        // Check bed number if provided
        if ($request->bed_number) {
            $bedOccupied = HostelAllocation::forTenant($tenant->id)
                ->where('room_id', $request->room_id)
                ->where('bed_number', $request->bed_number)
                ->where('status', 'active')
                ->exists();

            if ($bedOccupied) {
                return back()->with('error', 'Selected bed is already occupied.')->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $allocation = HostelAllocation::create([
                'tenant_id' => $tenant->id,
                'student_id' => $request->student_id,
                'hostel_id' => $request->hostel_id,
                'room_id' => $request->room_id,
                'bed_number' => $request->bed_number,
                'allocation_date' => $request->allocation_date,
                'notes' => $request->notes,
                'status' => 'active',
                'allocated_by' => auth()->id(),
            ]);

            // Update room and hostel available beds
            $room->decrement('available_beds');
            if ($room->available_beds == 0) {
                $room->update(['status' => 'occupied']);
            }

            $hostel = Hostel::find($request->hostel_id);
            $hostel->decrement('available_beds');

            DB::commit();

            return redirect(url('/admin/hostel/allocations'))->with('success', 'Student allocated to hostel successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to allocate student: ' . $e->getMessage())->withInput();
        }
    }

    public function release(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $allocation = HostelAllocation::forTenant($tenant->id)
            ->where('status', 'active')
            ->with(['room', 'hostel'])
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'release_date' => 'required|date|after_or_equal:' . $allocation->allocation_date,
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $allocation->update([
                'release_date' => $request->release_date,
                'status' => 'released',
                'notes' => $request->notes ?? $allocation->notes,
            ]);

            // Update room and hostel available beds
            $allocation->room->increment('available_beds');
            if ($allocation->room->status == 'occupied' && $allocation->room->available_beds > 0) {
                $allocation->room->update(['status' => 'available']);
            }

            $allocation->hostel->increment('available_beds');

            DB::commit();

            return redirect(url('/admin/hostel/allocations'))->with('success', 'Student released from hostel successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to release student: ' . $e->getMessage());
        }
    }
}

