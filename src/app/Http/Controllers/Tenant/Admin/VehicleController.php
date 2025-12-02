<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Route;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VehicleController extends Controller
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

        $query = Vehicle::forTenant($tenant->id)
            ->with(['driver', 'route'])
            ->withCount('activeAssignments');

        if ($request->has('search') && $request->search) {
            $query->where('vehicle_number', 'like', '%' . $request->search . '%')
                  ->orWhere('registration_number', 'like', '%' . $request->search . '%');
        }

        if ($request->has('vehicle_type') && $request->vehicle_type) {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $vehicles = $query->orderBy('vehicle_number')->paginate(20)->withQueryString();
        $drivers = Driver::forTenant($tenant->id)->active()->orderBy('name')->get();
        $routes = Route::forTenant($tenant->id)->active()->orderBy('name')->get();

        return view('tenant.admin.transport.vehicles.index', compact('vehicles', 'drivers', 'routes', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        $drivers = Driver::forTenant($tenant->id)->active()->orderBy('name')->get();
        $routes = Route::forTenant($tenant->id)->active()->orderBy('name')->get();
        return view('tenant.admin.transport.vehicles.create', compact('drivers', 'routes', 'tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'vehicle_number' => 'required|string|max:50|unique:vehicles,vehicle_number',
            'vehicle_type' => 'required|in:bus,van,car,auto,other',
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'manufacturing_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'capacity' => 'required|integer|min:1',
            'color' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'registration_date' => 'nullable|date',
            'insurance_expiry' => 'nullable|date',
            'permit_expiry' => 'nullable|date',
            'fitness_expiry' => 'nullable|date',
            'driver_id' => 'nullable|exists:drivers,id',
            'route_id' => 'nullable|exists:routes,id',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,maintenance,retired',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Vehicle::create([
            'tenant_id' => $tenant->id,
            'vehicle_number' => $request->vehicle_number,
            'vehicle_type' => $request->vehicle_type,
            'make' => $request->make,
            'model' => $request->model,
            'manufacturing_year' => $request->manufacturing_year,
            'capacity' => $request->capacity,
            'color' => $request->color,
            'registration_number' => $request->registration_number,
            'registration_date' => $request->registration_date,
            'insurance_expiry' => $request->insurance_expiry,
            'permit_expiry' => $request->permit_expiry,
            'fitness_expiry' => $request->fitness_expiry,
            'driver_id' => $request->driver_id,
            'route_id' => $request->route_id,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return redirect(url('/admin/transport/vehicles'))->with('success', 'Vehicle added successfully.');
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $vehicle = Vehicle::forTenant($tenant->id)
            ->with(['driver', 'route', 'activeAssignments.student'])
            ->findOrFail($id);

        return view('tenant.admin.transport.vehicles.show', compact('vehicle', 'tenant'));
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $vehicle = Vehicle::forTenant($tenant->id)->findOrFail($id);
        $drivers = Driver::forTenant($tenant->id)->active()->orderBy('name')->get();
        $routes = Route::forTenant($tenant->id)->active()->orderBy('name')->get();
        return view('tenant.admin.transport.vehicles.edit', compact('vehicle', 'drivers', 'routes', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $vehicle = Vehicle::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'vehicle_number' => 'required|string|max:50|unique:vehicles,vehicle_number,' . $id,
            'vehicle_type' => 'required|in:bus,van,car,auto,other',
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'manufacturing_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'capacity' => 'required|integer|min:1',
            'color' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'registration_date' => 'nullable|date',
            'insurance_expiry' => 'nullable|date',
            'permit_expiry' => 'nullable|date',
            'fitness_expiry' => 'nullable|date',
            'driver_id' => 'nullable|exists:drivers,id',
            'route_id' => 'nullable|exists:routes,id',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,maintenance,retired',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $vehicle->update($request->all());

        return redirect(url('/admin/transport/vehicles'))->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $vehicle = Vehicle::forTenant($tenant->id)
            ->withCount('activeAssignments')
            ->findOrFail($id);

        if ($vehicle->active_assignments_count > 0) {
            return back()->with('error', 'Cannot delete vehicle with active student assignments.');
        }

        $vehicle->delete();

        return redirect(url('/admin/transport/vehicles'))->with('success', 'Vehicle deleted successfully.');
    }
}
