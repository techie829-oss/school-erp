<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\RouteStop;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
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

        $query = Route::forTenant($tenant->id)->withCount(['stops', 'activeAssignments']);

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('route_number', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $routes = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('tenant.admin.transport.routes.index', compact('routes', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        return view('tenant.admin.transport.routes.create', compact('tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'route_number' => 'nullable|string|max:50',
            'start_location' => 'required|string|max:500',
            'end_location' => 'required|string|max:500',
            'distance' => 'nullable|numeric|min:0',
            'base_fare' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
            'stops' => 'nullable|array',
            'stops.*.stop_name' => 'required_with:stops|string|max:255',
            'stops.*.stop_address' => 'nullable|string|max:500',
            'stops.*.stop_order' => 'required_with:stops|integer|min:1',
            'stops.*.fare_from_start' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $route = Route::create([
                'tenant_id' => $tenant->id,
                'name' => $request->name,
                'route_number' => $request->route_number,
                'start_location' => $request->start_location,
                'end_location' => $request->end_location,
                'distance' => $request->distance,
                'base_fare' => $request->base_fare ?? 0,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            // Create route stops if provided
            if ($request->has('stops') && is_array($request->stops)) {
                foreach ($request->stops as $stop) {
                    RouteStop::create([
                        'route_id' => $route->id,
                        'stop_name' => $stop['stop_name'],
                        'stop_address' => $stop['stop_address'] ?? null,
                        'stop_order' => $stop['stop_order'],
                        'fare_from_start' => $stop['fare_from_start'] ?? 0,
                    ]);
                }
            }

            DB::commit();

            return redirect(url('/admin/transport/routes'))->with('success', 'Route created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create route: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $route = Route::forTenant($tenant->id)
            ->with(['stops', 'vehicles.driver', 'activeAssignments.student'])
            ->findOrFail($id);

        return view('tenant.admin.transport.routes.show', compact('route', 'tenant'));
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $route = Route::forTenant($tenant->id)->with('stops')->findOrFail($id);
        return view('tenant.admin.transport.routes.edit', compact('route', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $route = Route::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'route_number' => 'nullable|string|max:50',
            'start_location' => 'required|string|max:500',
            'end_location' => 'required|string|max:500',
            'distance' => 'nullable|numeric|min:0',
            'base_fare' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
            'stops' => 'nullable|array',
            'stops.*.stop_name' => 'required_with:stops|string|max:255',
            'stops.*.stop_address' => 'nullable|string|max:500',
            'stops.*.stop_order' => 'required_with:stops|integer|min:1',
            'stops.*.fare_from_start' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $route->update([
                'name' => $request->name,
                'route_number' => $request->route_number,
                'start_location' => $request->start_location,
                'end_location' => $request->end_location,
                'distance' => $request->distance,
                'base_fare' => $request->base_fare ?? 0,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            // Update route stops
            if ($request->has('stops') && is_array($request->stops)) {
                // Delete existing stops
                $route->stops()->delete();

                // Create new stops
                foreach ($request->stops as $stop) {
                    RouteStop::create([
                        'route_id' => $route->id,
                        'stop_name' => $stop['stop_name'],
                        'stop_address' => $stop['stop_address'] ?? null,
                        'stop_order' => $stop['stop_order'],
                        'fare_from_start' => $stop['fare_from_start'] ?? 0,
                    ]);
                }
            }

            DB::commit();

            return redirect(url('/admin/transport/routes'))->with('success', 'Route updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update route: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $route = Route::forTenant($tenant->id)
            ->withCount(['vehicles', 'activeAssignments'])
            ->findOrFail($id);

        if ($route->vehicles_count > 0 || $route->active_assignments_count > 0) {
            return back()->with('error', 'Cannot delete route with assigned vehicles or active student assignments.');
        }

        $route->delete();

        return redirect(url('/admin/transport/routes'))->with('success', 'Route deleted successfully.');
    }
}
