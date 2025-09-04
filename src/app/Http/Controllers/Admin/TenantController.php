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

        $tenant = Tenant::create([
            'id' => $tenantId,
            'data' => [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'type' => $validated['type'],
                'database_strategy' => $validated['database_strategy'],
                'active' => $validated['active'] ?? true,
                'created_at' => now()->toISOString(),
            ]
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
            'active' => 'boolean',
        ]);

        $tenant->update([
            'data' => array_merge($tenant->data, [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'type' => $validated['type'],
                'database_strategy' => $validated['database_strategy'],
                'active' => $validated['active'] ?? true,
                'updated_at' => now()->toISOString(),
            ])
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
