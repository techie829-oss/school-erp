<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportAssignment;
use App\Models\Route;
use App\Models\Vehicle;
use App\Models\Student;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TransportAssignmentController extends Controller
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

        $query = TransportAssignment::forTenant($tenant->id)
            ->with(['student', 'route', 'vehicle', 'pickupStop', 'dropStop']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('booking_status') && $request->booking_status !== '') {
            $query->where('booking_status', $request->booking_status);
        }

        if ($request->has('route_id') && $request->route_id) {
            $query->where('route_id', $request->route_id);
        }

        if ($request->has('vehicle_id') && $request->vehicle_id) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->has('search') && $request->search) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('admission_number', 'like', '%' . $request->search . '%');
            });
        }

        $assignments = $query->latest('booking_date')->paginate(20)->withQueryString();
        $routes = Route::forTenant($tenant->id)->active()->orderBy('name')->get();
        $vehicles = Vehicle::forTenant($tenant->id)->active()->orderBy('vehicle_number')->get();

        return view('tenant.admin.transport.assignments.index', compact('assignments', 'routes', 'vehicles', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        $students = Student::forTenant($tenant->id)->active()->orderBy('full_name')->get();
        $routes = Route::forTenant($tenant->id)->active()->with('stops')->orderBy('name')->get();
        $vehicles = Vehicle::forTenant($tenant->id)->active()->orderBy('vehicle_number')->get();

        return view('tenant.admin.transport.assignments.create', compact('students', 'routes', 'vehicles', 'tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'route_id' => 'required|exists:routes,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'pickup_stop_id' => 'nullable|exists:route_stops,id',
            'drop_stop_id' => 'nullable|exists:route_stops,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'booking_date' => 'nullable|date',
            'monthly_fare' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,cancelled',
            'booking_status' => 'required|in:pending,confirmed,active,cancelled,completed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if student already has an active assignment
        $existingAssignment = TransportAssignment::forTenant($tenant->id)
            ->where('student_id', $request->student_id)
            ->where('status', 'active')
            ->where('booking_status', '!=', 'cancelled')
            ->first();

        if ($existingAssignment) {
            return back()->with('error', 'Student already has an active transport assignment.')->withInput();
        }

        // Check vehicle capacity if vehicle is assigned
        if ($request->vehicle_id) {
            $vehicle = Vehicle::forTenant($tenant->id)->findOrFail($request->vehicle_id);
            if ($vehicle->is_full) {
                return back()->with('error', 'Selected vehicle is at full capacity.')->withInput();
            }
        }

        DB::beginTransaction();
        try {
            $assignment = TransportAssignment::create([
                'tenant_id' => $tenant->id,
                'student_id' => $request->student_id,
                'route_id' => $request->route_id,
                'vehicle_id' => $request->vehicle_id,
                'pickup_stop_id' => $request->pickup_stop_id,
                'drop_stop_id' => $request->drop_stop_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'booking_date' => $request->booking_date ?? now(),
                'booking_status' => $request->booking_status,
                'monthly_fare' => $request->monthly_fare,
                'notes' => $request->notes,
                'status' => $request->status,
                'assigned_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect(url('/admin/transport/assignments'))->with('success', 'Transport assignment created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create assignment: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $assignment = TransportAssignment::forTenant($tenant->id)
            ->with(['student', 'route.stops', 'vehicle.driver', 'pickupStop', 'dropStop', 'assignedBy', 'bills.payments'])
            ->findOrFail($id);

        return view('tenant.admin.transport.assignments.show', compact('assignment', 'tenant'));
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $assignment = TransportAssignment::forTenant($tenant->id)->findOrFail($id);
        $students = Student::forTenant($tenant->id)->active()->orderBy('full_name')->get();
        $routes = Route::forTenant($tenant->id)->active()->with('stops')->orderBy('name')->get();
        $vehicles = Vehicle::forTenant($tenant->id)->active()->orderBy('vehicle_number')->get();

        return view('tenant.admin.transport.assignments.edit', compact('assignment', 'students', 'routes', 'vehicles', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $assignment = TransportAssignment::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'route_id' => 'required|exists:routes,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'pickup_stop_id' => 'nullable|exists:route_stops,id',
            'drop_stop_id' => 'nullable|exists:route_stops,id',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'booking_date' => 'nullable|date',
            'monthly_fare' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,cancelled',
            'booking_status' => 'required|in:pending,confirmed,active,cancelled,completed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if another student has active assignment for this student
        if ($request->student_id != $assignment->student_id) {
            $existingAssignment = TransportAssignment::forTenant($tenant->id)
                ->where('student_id', $request->student_id)
                ->where('id', '!=', $id)
                ->where('status', 'active')
                ->where('booking_status', '!=', 'cancelled')
                ->first();

            if ($existingAssignment) {
                return back()->with('error', 'Student already has an active transport assignment.')->withInput();
            }
        }

        $assignment->update($request->all());

        return redirect(url('/admin/transport/assignments'))->with('success', 'Transport assignment updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $assignment = TransportAssignment::forTenant($tenant->id)
            ->withCount('bills')
            ->findOrFail($id);

        if ($assignment->bills_count > 0) {
            return back()->with('error', 'Cannot delete assignment with existing bills.');
        }

        $assignment->delete();

        return redirect(url('/admin/transport/assignments'))->with('success', 'Transport assignment deleted successfully.');
    }
}
