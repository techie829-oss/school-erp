<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\AdminUser;
use App\Models\User;
use App\Services\VhostService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::paginate(10);
        return view('admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('admin.tenants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'type' => 'required|in:internal,school,college,university',
            'subdomain' => 'required|string|max:50|regex:/^[a-z0-9-]+$/|unique:tenants,data->subdomain',
            'custom_domain' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:tenants,data->custom_domain',
            'active' => 'boolean',
        ]);

        // Generate unique tenant ID
        $tenantId = $this->generateUniqueTenantId($validated['name']);

        $tenantData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'type' => $validated['type'],
            'subdomain' => $validated['subdomain'],
            'is_active' => $validated['active'] ?? true,
            'created_at' => now()->toISOString(),
        ];

        // Add domain information
        $tenantData['full_domain'] = $validated['subdomain'] . '.' . config('all.domains.primary');
        if ($validated['custom_domain']) {
            $tenantData['custom_domain'] = $validated['custom_domain'];
        }

        $tenant = Tenant::create([
            'id' => $tenantId,
            'data' => $tenantData,
        ]);

        // Update Herd configuration
        $this->updateHerdConfiguration($validated['subdomain']);

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', 'Tenant created successfully!');
    }

    public function show(Tenant $tenant)
    {
        return view('admin.tenants.show', compact('tenant'));
    }

    public function edit(Tenant $tenant)
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'type' => 'required|in:internal,school,college,university',
            'subdomain' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('tenants', 'data->subdomain')->ignore($tenant->id)
            ],
            'custom_domain' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('tenants', 'data->custom_domain')->ignore($tenant->id)
            ],
            'active' => 'boolean',
        ]);

        $tenantData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'type' => $validated['type'],
            'subdomain' => $validated['subdomain'],
            'is_active' => $validated['active'] ?? true,
            'updated_at' => now()->toISOString(),
        ];

        // Add domain information
        $tenantData['full_domain'] = $validated['subdomain'] . '.' . config('all.domains.primary');
        if ($validated['custom_domain']) {
            $tenantData['custom_domain'] = $validated['custom_domain'];
        }

        $tenant->update([
            'data' => array_merge($tenant->data, $tenantData),
        ]);

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully!');
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant deleted successfully!');
    }

    // User Management Methods
    public function usersIndex(Tenant $tenant)
    {
        // Get school users from users table (not admin_users)
        $users = User::where('tenant_id', $tenant->id)->paginate(10);
        return view('admin.tenants.users.index', compact('tenant', 'users'));
    }

    public function usersCount(Tenant $tenant)
    {
        // Count school users from users table (not admin_users)
        $count = User::where('tenant_id', $tenant->id)->count();
        return response()->json([
            'success' => true,
                'count' => $count
            ]);
    }

    public function usersCreate(Tenant $tenant)
    {
        return view('admin.tenants.users.create', compact('tenant'));
    }

    public function usersStore(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'user_type' => 'required|in:school_admin,teacher,staff,student',
            'is_active' => 'sometimes|boolean',
        ]);

                // Create school admin in users table (not admin_users)
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'tenant_id' => $tenant->id,
                    'user_type' => 'school_admin',
                    'is_active' => $validated['is_active'] ?? true,
                ]);

        return redirect()->route('admin.tenants.users.show', [$tenant, $user->id])
            ->with('success', 'User created successfully!');
    }

    public function usersShow(Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);
        return view('admin.tenants.users.show', compact('tenant', 'user'));
    }

    public function usersEdit(Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);
        return view('admin.tenants.users.edit', compact('tenant', 'user'));
    }

    public function usersUpdate(Request $request, Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id)
            ],
            'password' => 'nullable|confirmed|min:8',
            'user_type' => 'required|in:school_admin,teacher,staff,student',
            'is_active' => 'sometimes|boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'user_type' => $validated['user_type'],
            'is_active' => $validated['is_active'] ?? true,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.tenants.users.show', [$tenant, $userId])
            ->with('success', 'User updated successfully!');
    }

    public function usersChangePassword(Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);
        return view('admin.tenants.users.change-password', compact('tenant', 'user'));
    }

    public function usersUpdatePassword(Request $request, Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

            $user->update([
            'password' => Hash::make($validated['password'])
            ]);

        return redirect()->route('admin.tenants.users.show', [$tenant, $userId])
            ->with('success', 'Password updated successfully!');
    }

    public function usersDelete(Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);
        return view('admin.tenants.users.delete', compact('tenant', 'user'));
    }

    public function usersDestroy(Tenant $tenant, $userId)
    {
        $user = User::where('tenant_id', $tenant->id)->findOrFail($userId);
        $user->delete();

        return redirect()->route('admin.tenants.users.index', $tenant)
            ->with('success', 'User deleted successfully!');
    }

    private function generateUniqueTenantId(string $name): string
    {
        $baseId = Str::slug($name);
        $counter = 1;
        $tenantId = $baseId;

        while (Tenant::where('id', $tenantId)->exists()) {
            $tenantId = $baseId . '-' . $counter;
            $counter++;
        }

        return $tenantId;
    }

    private function updateHerdConfiguration(string $subdomain): void
    {
        try {
            $vhostService = new VhostService();
            $vhostService->updateHerdConfiguration($subdomain);
        } catch (\Exception $e) {
            Log::error('Failed to update Herd configuration', [
                'subdomain' => $subdomain,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function checkSubdomain(Request $request)
    {
        $subdomain = $request->input('subdomain');
        $exists = Tenant::where('data->subdomain', $subdomain)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'This subdomain is already taken' : 'Subdomain is available'
        ]);
    }

    public function cleanupHerdYml()
    {
        try {
            $vhostService = new VhostService();
            $result = $vhostService->cleanupHerdYml();

            return redirect()->route('admin.tenants.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return redirect()->route('admin.tenants.index')
                ->with('error', 'Failed to cleanup Herd configuration: ' . $e->getMessage());
        }
    }

    public function syncHerdYmlWithDatabase()
    {
        try {
            $vhostService = new VhostService();
            $result = $vhostService->syncHerdYmlWithDatabase();

            return redirect()->route('admin.tenants.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return redirect()->route('admin.tenants.index')
                ->with('error', 'Failed to sync Herd configuration: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Tenant $tenant)
    {
        $tenantData = $tenant->data;
        // Use 'is_active' to match the isActive() method in Tenant model
        $tenantData['is_active'] = !($tenantData['is_active'] ?? true);
        $tenant->update(['data' => $tenantData]);

        $status = $tenantData['is_active'] ? 'activated' : 'deactivated';

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', "Tenant {$status} successfully!");
    }
}
