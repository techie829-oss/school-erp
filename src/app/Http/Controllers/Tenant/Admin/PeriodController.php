<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Period;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeriodController extends Controller
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

        $query = Period::forTenant($tenant->id);

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        } else {
            $query->active();
        }

        $periods = $query->ordered()->get();

        return view('tenant.admin.timetable.periods.index', compact('periods', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        return view('tenant.admin.timetable.periods.create', compact('tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'period_number' => 'required|integer|min:1|unique:periods,period_number,NULL,id,tenant_id,' . $tenant->id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'break_type' => 'required|in:none,short_break,lunch_break,assembly',
            'name' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        Period::create([
            'tenant_id' => $tenant->id,
            'period_number' => $request->period_number,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $request->duration_minutes,
            'break_type' => $request->break_type,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);

        return redirect(url('/admin/timetable/periods'))->with('success', 'Period created successfully.');
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $period = Period::forTenant($tenant->id)->findOrFail($id);
        return view('tenant.admin.timetable.periods.edit', compact('period', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $period = Period::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'period_number' => 'required|integer|min:1|unique:periods,period_number,' . $id . ',id,tenant_id,' . $tenant->id,
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'break_type' => 'required|in:none,short_break,lunch_break,assembly',
            'name' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $period->update([
            'period_number' => $request->period_number,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $request->duration_minutes,
            'break_type' => $request->break_type,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? $request->is_active : true,
        ]);

        return redirect(url('/admin/timetable/periods'))->with('success', 'Period updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $period = Period::forTenant($tenant->id)->findOrFail($id);

        // Check if period is used in any timetable
        $usedInTimetable = \App\Models\TimetablePeriod::whereHas('timetable', function($q) use ($tenant) {
            $q->where('tenant_id', $tenant->id);
        })->where('period_number', $period->period_number)->exists();

        if ($usedInTimetable) {
            return back()->with('error', 'Cannot delete period that is used in timetables.');
        }

        $period->delete();

        return redirect(url('/admin/timetable/periods'))->with('success', 'Period deleted successfully.');
    }
}

