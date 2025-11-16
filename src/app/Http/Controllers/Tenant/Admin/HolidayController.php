<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Services\TenantService;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);
        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $year = (int) $request->get('year', now()->year);

        $holidays = Holiday::forTenant($tenant->id)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->get();

        return view('tenant.admin.attendance.holidays.index', compact(
            'tenant',
            'holidays',
            'year'
        ));
    }

    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);
        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $data = $request->validate([
            'date' => 'required|date',
            'title' => 'required|string|max:255',
            'type' => 'nullable|string|max:100',
            'is_full_day' => 'sometimes|boolean',
            'notes' => 'nullable|string|max:1000',
        ]);

        $data['tenant_id'] = $tenant->id;
        $data['is_full_day'] = $request->boolean('is_full_day', true);

        Holiday::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'date' => $data['date'],
            ],
            $data
        );

        return back()->with('success', 'Holiday saved successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);
        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $holiday = Holiday::forTenant($tenant->id)->findOrFail($id);
        $holiday->delete();

        return back()->with('success', 'Holiday deleted successfully.');
    }
}


