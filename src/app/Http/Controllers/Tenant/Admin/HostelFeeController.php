<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hostel;
use App\Models\HostelFee;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HostelFeeController extends Controller
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

        $query = HostelFee::forTenant($tenant->id)->with(['hostel']);

        if ($request->has('hostel_id') && $request->hostel_id) {
            $query->where('hostel_id', $request->hostel_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        } else {
            $query->active();
        }

        $fees = $query->orderBy('hostel_id')->orderBy('name')->paginate(20)->withQueryString();
        $hostels = Hostel::forTenant($tenant->id)->active()->orderBy('name')->get();

        return view('tenant.admin.hostel.fees.index', compact('fees', 'hostels', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        $hostels = Hostel::forTenant($tenant->id)->active()->orderBy('name')->get();
        return view('tenant.admin.hostel.fees.create', compact('hostels', 'tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'hostel_id' => 'required|exists:hostels,id',
            'fee_type' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:one_time,monthly,quarterly,semester,annual',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        HostelFee::create([
            'tenant_id' => $tenant->id,
            'hostel_id' => $request->hostel_id,
            'fee_type' => $request->fee_type,
            'name' => $request->name,
            'amount' => $request->amount,
            'frequency' => $request->frequency,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect(url('/admin/hostel/fees'))->with('success', 'Hostel fee created successfully.');
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $fee = HostelFee::forTenant($tenant->id)->findOrFail($id);
        $hostels = Hostel::forTenant($tenant->id)->active()->orderBy('name')->get();
        return view('tenant.admin.hostel.fees.edit', compact('fee', 'hostels', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $fee = HostelFee::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'hostel_id' => 'required|exists:hostels,id',
            'fee_type' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:one_time,monthly,quarterly,semester,annual',
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $fee->update([
            'hostel_id' => $request->hostel_id,
            'fee_type' => $request->fee_type,
            'name' => $request->name,
            'amount' => $request->amount,
            'frequency' => $request->frequency,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect(url('/admin/hostel/fees'))->with('success', 'Hostel fee updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $fee = HostelFee::forTenant($tenant->id)->findOrFail($id);
        $fee->delete();

        return redirect(url('/admin/hostel/fees'))->with('success', 'Hostel fee deleted successfully.');
    }
}

