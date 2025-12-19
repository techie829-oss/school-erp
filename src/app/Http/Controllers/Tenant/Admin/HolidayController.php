<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use App\Models\HolidayScope;
use App\Models\SchoolClass;
use App\Models\Section;
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
            ->with('scopes')
            ->orderBy('date')
            ->get();

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $sections = Section::forTenant($tenant->id)
            ->active()
            ->with('schoolClass')
            ->get();

        return view('tenant.admin.holidays.index', compact(
            'tenant',
            'holidays',
            'year',
            'classes',
            'sections'
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
            'class_ids' => 'array',
            'class_ids.*' => 'integer|exists:classes,id',
            'section_ids' => 'array',
            'section_ids.*' => 'integer|exists:sections,id',
        ]);

        $data['tenant_id'] = $tenant->id;
        $data['is_full_day'] = $request->boolean('is_full_day', true);

        $holiday = Holiday::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'date' => $data['date'],
            ],
            $data
        );

        // Update specific class/section scopes (partial holidays)
        $classIds = $request->input('class_ids', []);
        $sectionIds = $request->input('section_ids', []);

        HolidayScope::where('holiday_id', $holiday->id)->delete();

        // Class-level scopes
        foreach ($classIds as $classId) {
            HolidayScope::create([
                'tenant_id' => $tenant->id,
                'holiday_id' => $holiday->id,
                'class_id' => $classId,
                'section_id' => null,
            ]);
        }

        // Section-level scopes (more specific)
        if (!empty($sectionIds)) {
            $sections = Section::whereIn('id', $sectionIds)->get();
            foreach ($sections as $section) {
                HolidayScope::create([
                    'tenant_id' => $tenant->id,
                    'holiday_id' => $holiday->id,
                    'class_id' => $section->class_id,
                    'section_id' => $section->id,
                ]);
            }
        }

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


