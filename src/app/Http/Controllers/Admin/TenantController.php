<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants = Tenant::latest()->paginate(10);
        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tenants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'type' => 'required|in:internal,school,college,university',
            'database_strategy' => 'required|in:shared,separate',
            'domain_type' => 'required|in:subdomain,custom',
            'subdomain' => 'required_if:domain_type,subdomain|string|max:50|regex:/^[a-z0-9-]+$/|unique:tenants,data->subdomain',
            'custom_domain' => 'required_if:domain_type,custom|string|max:255|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:tenants,data->custom_domain',
            'active' => 'boolean',
        ]);

        // Generate unique tenant ID
        $tenantId = Str::slug($validated['name']);
        $counter = 1;
        $originalTenantId = $tenantId;

        while (Tenant::where('id', $tenantId)->exists()) {
            $tenantId = $originalTenantId . '-' . $counter;
            $counter++;
        }

        $tenantData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'type' => $validated['type'],
            'database_strategy' => $validated['database_strategy'],
            'domain_type' => $validated['domain_type'],
            'active' => $validated['active'] ?? true,
            'created_at' => now()->toISOString(),
        ];

        // Add domain-specific data
        if ($validated['domain_type'] === 'subdomain') {
            $tenantData['subdomain'] = $validated['subdomain'];
            $tenantData['full_domain'] = $validated['subdomain'] . '.' . config('all.domains.primary');
        } else {
            $tenantData['custom_domain'] = $validated['custom_domain'];
            $tenantData['full_domain'] = $validated['custom_domain'];
        }

        $tenant = Tenant::create([
            'id' => $tenantId,
            'data' => $tenantData
        ]);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant)
    {
        return view('admin.tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant)
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'type' => 'required|in:internal,school,college,university',
            'database_strategy' => 'required|in:shared,separate',
            'domain_type' => 'required|in:subdomain,custom',
            'subdomain' => 'required_if:domain_type,subdomain|string|max:50|regex:/^[a-z0-9-]+$/|unique:tenants,data->subdomain,' . $tenant->id . ',id',
            'custom_domain' => 'required_if:domain_type,custom|string|max:255|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:tenants,data->custom_domain,' . $tenant->id . ',id',
            'active' => 'boolean',
        ]);

        $tenantData = array_merge($tenant->data, [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'type' => $validated['type'],
            'database_strategy' => $validated['database_strategy'],
            'domain_type' => $validated['domain_type'],
            'active' => $validated['active'] ?? true,
            'updated_at' => now()->toISOString(),
        ]);

        // Update domain-specific data
        if ($validated['domain_type'] === 'subdomain') {
            $tenantData['subdomain'] = $validated['subdomain'];
            $tenantData['full_domain'] = $validated['subdomain'] . '.' . config('all.domains.primary');
            // Remove custom domain if switching to subdomain
            unset($tenantData['custom_domain']);
        } else {
            $tenantData['custom_domain'] = $validated['custom_domain'];
            $tenantData['full_domain'] = $validated['custom_domain'];
            // Remove subdomain if switching to custom domain
            unset($tenantData['subdomain']);
        }

        $tenant->update([
            'data' => $tenantData
        ]);

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant deleted successfully!');
    }
}
