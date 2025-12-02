<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
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

        $query = Driver::forTenant($tenant->id)->withCount('vehicles');

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('license_number', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $drivers = $query->orderBy('name')->paginate(20)->withQueryString();

        return view('tenant.admin.transport.drivers.index', compact('drivers', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        return view('tenant.admin.transport.drivers.create', compact('tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => 'nullable|string|max:100',
            'license_type' => 'nullable|string|max:100',
            'license_issue_date' => 'nullable|date',
            'license_expiry_date' => 'nullable|date|after_or_equal:license_issue_date',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'salary' => 'nullable|numeric|min:0',
            'joining_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Driver::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'license_number' => $request->license_number,
            'license_type' => $request->license_type,
            'license_issue_date' => $request->license_issue_date,
            'license_expiry_date' => $request->license_expiry_date,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'salary' => $request->salary,
            'joining_date' => $request->joining_date,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        return redirect(url('/admin/transport/drivers'))->with('success', 'Driver added successfully.');
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $driver = Driver::forTenant($tenant->id)
            ->with(['vehicles.route'])
            ->findOrFail($id);

        return view('tenant.admin.transport.drivers.show', compact('driver', 'tenant'));
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $driver = Driver::forTenant($tenant->id)->findOrFail($id);
        return view('tenant.admin.transport.drivers.edit', compact('driver', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $driver = Driver::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'license_number' => 'nullable|string|max:100',
            'license_type' => 'nullable|string|max:100',
            'license_issue_date' => 'nullable|date',
            'license_expiry_date' => 'nullable|date|after_or_equal:license_issue_date',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'salary' => 'nullable|numeric|min:0',
            'joining_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $driver->update($request->all());

        return redirect(url('/admin/transport/drivers'))->with('success', 'Driver updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $driver = Driver::forTenant($tenant->id)->withCount('vehicles')->findOrFail($id);

        if ($driver->vehicles_count > 0) {
            return back()->with('error', 'Cannot delete driver with assigned vehicles.');
        }

        $driver->delete();

        return redirect(url('/admin/transport/drivers'))->with('success', 'Driver deleted successfully.');
    }
}
