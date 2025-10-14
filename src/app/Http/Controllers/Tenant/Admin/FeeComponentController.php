<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeComponent;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeeComponentController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of fee components
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        $components = FeeComponent::forTenant($tenant->id)
            ->orderBy('type', 'asc')
            ->orderBy('name', 'asc')
            ->paginate(20);

        return view('tenant.admin.fees.components.index', compact('components'));
    }

    /**
     * Show the form for creating a new component
     */
    public function create()
    {
        return view('tenant.admin.fees.components.create');
    }

    /**
     * Store a newly created component
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:fee_components,code,NULL,id,tenant_id,' . $tenant->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:recurring,one_time',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            FeeComponent::create([
                'tenant_id' => $tenant->id,
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            return redirect('/admin/fees/components')
                ->with('success', 'Fee component created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create fee component: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the component
     */
    public function edit(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        $component = FeeComponent::forTenant($tenant->id)->findOrFail($id);

        return view('tenant.admin.fees.components.edit', compact('component'));
    }

    /**
     * Update the specified component
     */
    public function update(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        $component = FeeComponent::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:fee_components,code,' . $id . ',id,tenant_id,' . $tenant->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:recurring,one_time',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $component->update([
                'code' => strtoupper($request->code),
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? 1 : 0,
            ]);

            return redirect('/admin/fees/components')
                ->with('success', 'Fee component updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update fee component: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified component
     */
    public function destroy(Request $request, $id)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        $component = FeeComponent::forTenant($tenant->id)->findOrFail($id);

        // Check if component is used in any plans
        if ($component->feePlanItems()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete component that is used in fee plans!');
        }

        try {
            $component->delete();
            return redirect('/admin/fees/components')
                ->with('success', 'Fee component deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete fee component: ' . $e->getMessage());
        }
    }
}
