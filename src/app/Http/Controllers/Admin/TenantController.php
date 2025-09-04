<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\AdminUser;
use App\Services\VhostService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

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
            'subdomain' => 'required|string|max:50|regex:/^[a-z0-9-]+$/|unique:tenants,data->subdomain',
            'custom_domain' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:tenants,data->custom_domain',
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
            'subdomain' => $validated['subdomain'],
            'active' => $validated['active'] ?? true,
            'created_at' => now()->toISOString(),
        ];

        // Add subdomain (mandatory) and custom domain (optional)
        $tenantData['full_domain'] = $validated['subdomain'] . '.' . config('all.domains.primary');

        if (!empty($validated['custom_domain'])) {
            $tenantData['custom_domain'] = $validated['custom_domain'];
        }

        $tenant = Tenant::create([
            'id' => $tenantId,
            'data' => $tenantData
        ]);

        // Update Herd configuration if hosting type is Laravel Herd
        $this->updateHerdConfiguration($validated['subdomain']);

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
            'subdomain' => 'required|string|max:50|regex:/^[a-z0-9-]+$/|unique:tenants,data->subdomain,' . $tenant->id . ',id',
            'custom_domain' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/|unique:tenants,data->custom_domain,' . $tenant->id . ',id',
            'active' => 'boolean',
        ]);

        $tenantData = array_merge($tenant->data, [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'type' => $validated['type'],
            'database_strategy' => $validated['database_strategy'],
            'subdomain' => $validated['subdomain'],
            'active' => $validated['active'] ?? true,
            'updated_at' => now()->toISOString(),
        ]);

        // Update subdomain (mandatory) and custom domain (optional)
        $tenantData['full_domain'] = $validated['subdomain'] . '.' . config('all.domains.primary');

        if (!empty($validated['custom_domain'])) {
            $tenantData['custom_domain'] = $validated['custom_domain'];
        } else {
            // Remove custom domain if empty
            unset($tenantData['custom_domain']);
        }

        // Check if subdomain changed and update Herd configuration
        $oldSubdomain = $tenant->data['subdomain'] ?? null;
        $newSubdomain = $validated['subdomain'];

        if ($oldSubdomain !== $newSubdomain) {
            $this->updateHerdConfiguration($newSubdomain, $oldSubdomain);
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

    /**
     * Clean up Herd YAML file formatting.
     */
    public function cleanupHerdYml()
    {
        try {
            $this->performHerdYmlCleanup();

            return redirect()->route('admin.tenants.index')
                ->with('success', 'Herd YAML file cleaned up and formatted successfully!');

        } catch (\Exception $e) {
            return redirect()->route('admin.tenants.index')
                ->with('error', 'Failed to cleanup Herd YAML file: ' . $e->getMessage());
        }
    }

    /**
     * Check if subdomain already exists in database.
     */
    public function checkSubdomain(Request $request)
    {
        $subdomain = $request->input('subdomain');
        $excludeTenantId = $request->input('exclude_tenant_id');

        if (empty($subdomain)) {
            return response()->json([
                'available' => false,
                'message' => 'Subdomain is required'
            ]);
        }

        $query = Tenant::whereRaw("JSON_EXTRACT(data, '$.subdomain') = ?", [$subdomain]);

        if ($excludeTenantId) {
            $query->where('id', '!=', $excludeTenantId);
        }

        $exists = $query->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Subdomain already exists' : 'Subdomain is available'
        ]);
    }

    /**
     * Sync .herd.yml file with database tenant data.
     */
    public function syncHerdYmlWithDatabase()
    {
        try {
            $vhostService = app(VhostService::class);

            // Only sync if hosting type is Laravel Herd
            if ($vhostService->getHostingType() !== 'laravel-herd') {
                return redirect()->route('admin.tenants.index')
                    ->with('error', 'Herd YAML sync is only available for Laravel Herd hosting type.');
            }

            // Get all tenants with subdomains from database
            $tenants = Tenant::whereRaw("JSON_EXTRACT(data, '$.subdomain') IS NOT NULL")->get();
            $validSubdomains = [];

            foreach ($tenants as $tenant) {
                if (isset($tenant->data['subdomain']) && !empty($tenant->data['subdomain'])) {
                    $validSubdomains[] = $tenant->data['subdomain'];
                }
            }

            // Get current .herd.yml content
            $herdYmlPath = $vhostService->getHerdYmlPath();
            $content = file_exists($herdYmlPath) ? file_get_contents($herdYmlPath) : '';

            if (empty($content)) {
                Log::warning('Herd YAML file not found or empty', ['path' => $herdYmlPath]);
                return;
            }

            // Parse and update the content
            $lines = explode("\n", $content);
            $newLines = [];
            $inSubdomainsSection = false;
            $updated = false;

            foreach ($lines as $line) {
                $trimmedLine = trim($line);

                if ($trimmedLine === 'subdomains:') {
                    $inSubdomainsSection = true;
                    $newLines[] = $line;
                    continue;
                }

                if ($inSubdomainsSection && str_starts_with($trimmedLine, '- ')) {
                    $subdomainLine = trim(substr($trimmedLine, 2));
                    $existingSubdomain = trim(explode('#', $subdomainLine)[0]);

                    // Skip this line if subdomain is not in valid list
                    if (!in_array($existingSubdomain, $validSubdomains)) {
                        $updated = true;
                        continue;
                    }
                } else {
                    if ($inSubdomainsSection && !str_starts_with($trimmedLine, '- ') && !empty($trimmedLine)) {
                        $inSubdomainsSection = false;

                        // Add all valid subdomains with school names
                        foreach ($validSubdomains as $subdomain) {
                            $schoolName = $this->getSchoolNameForSubdomain($subdomain);
                            if ($schoolName) {
                                $newLines[] = "  - {$subdomain}  # {$schoolName}";
                            } else {
                                $newLines[] = "  - {$subdomain}";
                            }
                        }
                    }
                    $newLines[] = $line;
                }
            }

            // If we're still in subdomains section at the end
            if ($inSubdomainsSection) {
                foreach ($validSubdomains as $subdomain) {
                    $schoolName = $this->getSchoolNameForSubdomain($subdomain);
                    if ($schoolName) {
                        $newLines[] = "  - {$subdomain}  # {$schoolName}";
                    } else {
                        $newLines[] = "  - {$subdomain}";
                    }
                }
            }

            // Update the file if changes were made
            if ($updated) {
                $newContent = implode("\n", $newLines);
                $vhostService->updateHerdYmlContent($newContent);

                Log::info('Herd YAML file synced with database', [
                    'path' => $herdYmlPath,
                    'valid_subdomains' => $validSubdomains
                ]);

                return redirect()->route('admin.tenants.index')
                    ->with('success', 'Herd YAML file synced with database successfully!');
            } else {
                return redirect()->route('admin.tenants.index')
                    ->with('info', 'Herd YAML file is already in sync with database.');
            }

        } catch (\Exception $e) {
            Log::error('Failed to sync Herd YAML with database', [
                'error' => $e->getMessage()
            ]);

            return redirect()->route('admin.tenants.index')
                ->with('error', 'Failed to sync Herd YAML with database: ' . $e->getMessage());
        }
    }

    /**
     * Clean up and format .herd.yml file.
     */
    private function performHerdYmlCleanup(): void
    {
        try {
            $vhostService = app(VhostService::class);

            // Only update if hosting type is Laravel Herd
            if ($vhostService->getHostingType() !== 'laravel-herd') {
                return;
            }

            // Get current .herd.yml content
            $herdYmlPath = $vhostService->getHerdYmlPath();
            $content = file_exists($herdYmlPath) ? file_get_contents($herdYmlPath) : '';

            if (empty($content)) {
                return;
            }

            // Parse and clean up the content
            $lines = explode("\n", $content);
            $newLines = [];
            $subdomains = [];
            $inSubdomainsSection = false;

            foreach ($lines as $line) {
                $trimmedLine = trim($line);

                // Check if we're in the subdomains section
                if ($trimmedLine === 'subdomains:') {
                    $inSubdomainsSection = true;
                    $newLines[] = $line;
                    continue;
                }

                // If we're in subdomains section and find a subdomain
                if ($inSubdomainsSection && str_starts_with($trimmedLine, '- ')) {
                    $subdomainLine = trim(substr($trimmedLine, 2));

                    // Extract subdomain (remove comment if present)
                    $subdomain = trim(explode('#', $subdomainLine)[0]);

                    // Skip empty lines and invalid entries
                    if (!empty($subdomain)) {
                        $subdomains[] = $subdomain;
                    }
                } else {
                    // If we were in subdomains section and now we're not, add the cleaned subdomains
                    if ($inSubdomainsSection && !str_starts_with($trimmedLine, '- ') && !empty($trimmedLine)) {
                        $inSubdomainsSection = false;

                        // Add all subdomains with proper formatting and school names as comments
                        foreach ($subdomains as $subdomain) {
                            $schoolName = $this->getSchoolNameForSubdomain($subdomain);
                            if ($schoolName) {
                                $newLines[] = "  - {$subdomain}  # {$schoolName}";
                            } else {
                                $newLines[] = "  - {$subdomain}";
                            }
                        }
                    }
                    $newLines[] = $line;
                }
            }

            // If we're still in subdomains section at the end, add the cleaned subdomains
            if ($inSubdomainsSection) {
                        // Add all subdomains with proper formatting and school names as comments
                        foreach ($subdomains as $subdomain) {
                            $schoolName = $this->getSchoolNameForSubdomain($subdomain);
                            if ($schoolName) {
                                $newLines[] = "  - {$subdomain}  # {$schoolName}";
                            } else {
                                $newLines[] = "  - {$subdomain}";
                            }
                        }
            }

            // Update the file with cleaned content
            $newContent = implode("\n", $newLines);
            $vhostService->updateHerdYmlContent($newContent);

            Log::info('Herd YAML file cleaned up and formatted', [
                'path' => $herdYmlPath,
                'subdomains_count' => count($subdomains)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to cleanup Herd YAML file', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get school name for a given subdomain.
     */
    private function getSchoolNameForSubdomain(string $subdomain): ?string
    {
        try {
            $tenant = Tenant::whereRaw("JSON_EXTRACT(data, '$.subdomain') = ?", [$subdomain])->first();

            if ($tenant && isset($tenant->data['name'])) {
                return $tenant->data['name'];
            }

            return null;
        } catch (\Exception $e) {
            Log::warning('Failed to get school name for subdomain', [
                'subdomain' => $subdomain,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Update Herd configuration when subdomain changes.
     */
    private function updateHerdConfiguration(string $newSubdomain, ?string $oldSubdomain = null): void
    {
        try {
            $vhostService = app(VhostService::class);

            // Only update if hosting type is Laravel Herd
            if ($vhostService->getHostingType() !== 'laravel-herd') {
                return;
            }

            // Get current .herd.yml content
            $herdYmlPath = $vhostService->getHerdYmlPath();
            $content = file_exists($herdYmlPath) ? file_get_contents($herdYmlPath) : '';

            if (empty($content)) {
                Log::warning('Herd YAML file not found or empty', ['path' => $herdYmlPath]);
                return;
            }

            // Parse YAML content and clean up formatting
            $lines = explode("\n", $content);
            $subdomainsSection = false;
            $updated = false;
            $newLines = [];
            $subdomains = [];

            foreach ($lines as $line) {
                $trimmedLine = trim($line);

                // Check if we're in the subdomains section
                if ($trimmedLine === 'subdomains:') {
                    $subdomainsSection = true;
                    $newLines[] = $line;
                    continue;
                }

                // If we're in subdomains section and find a subdomain
                if ($subdomainsSection && str_starts_with($trimmedLine, '- ')) {
                    $subdomainLine = trim(substr($trimmedLine, 2));

                    // Extract subdomain (remove comment if present)
                    $existingSubdomain = trim(explode('#', $subdomainLine)[0]);

                    // Skip empty lines and invalid entries
                    if (empty($existingSubdomain)) {
                        continue;
                    }

                    // Remove old subdomain if it exists
                    if ($oldSubdomain && $existingSubdomain === $oldSubdomain) {
                        $updated = true;
                        continue; // Skip this line (remove old subdomain)
                    }

                    // Add to subdomains list if it doesn't exist
                    if (!in_array($existingSubdomain, $subdomains)) {
                        $subdomains[] = $existingSubdomain;
                    }
                } else {
                    // If we were in subdomains section and now we're not, add the new subdomain
                    if ($subdomainsSection && !str_starts_with($trimmedLine, '- ') && !empty($trimmedLine)) {
                        $subdomainsSection = false;

                        // Add new subdomain if it doesn't exist
                        if (!in_array($newSubdomain, $subdomains)) {
                            $subdomains[] = $newSubdomain;
                            $updated = true;
                        }

                        // Add all subdomains with proper formatting and school names as comments
                        foreach ($subdomains as $subdomain) {
                            $schoolName = $this->getSchoolNameForSubdomain($subdomain);
                            if ($schoolName) {
                                $newLines[] = "  - {$subdomain}  # {$schoolName}";
                            } else {
                                $newLines[] = "  - {$subdomain}";
                            }
                        }
                    }
                    $newLines[] = $line;
                }
            }

            // If we're still in subdomains section at the end, add the new subdomain
            if ($subdomainsSection) {
                // Add new subdomain if it doesn't exist
                if (!in_array($newSubdomain, $subdomains)) {
                    $subdomains[] = $newSubdomain;
                    $updated = true;
                }

                        // Add all subdomains with proper formatting and school names as comments
                        foreach ($subdomains as $subdomain) {
                            $schoolName = $this->getSchoolNameForSubdomain($subdomain);
                            if ($schoolName) {
                                $newLines[] = "  - {$subdomain}  # {$schoolName}";
                            } else {
                                $newLines[] = "  - {$subdomain}";
                            }
                        }
            }

            // Update the file if changes were made
            if ($updated) {
                $newContent = implode("\n", $newLines);
                $vhostService->updateHerdYmlContent($newContent);

                // Clean up the file to remove extra spaces and fix formatting
                $this->performHerdYmlCleanup();

                Log::info('Herd configuration updated for subdomain change', [
                    'old_subdomain' => $oldSubdomain,
                    'new_subdomain' => $newSubdomain,
                    'path' => $herdYmlPath
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to update Herd configuration for subdomain change', [
                'old_subdomain' => $oldSubdomain,
                'new_subdomain' => $newSubdomain,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display a listing of tenant users
     */
    public function usersIndex(Tenant $tenant)
    {
        $users = AdminUser::where('tenant_id', $tenant->id)->latest()->paginate(10);
        return view('admin.tenants.users.index', compact('tenant', 'users'));
    }

    /**
     * Display the specified tenant user
     */
    public function usersShow(Tenant $tenant, AdminUser $user)
    {
        // Ensure the user belongs to this tenant
        if ($user->tenant_id !== $tenant->id) {
            abort(404);
        }

        return view('admin.tenants.users.show', compact('tenant', 'user'));
    }

    /**
     * Show the form for editing the specified tenant user
     */
    public function usersEdit(Tenant $tenant, AdminUser $user)
    {
        // Ensure the user belongs to this tenant
        if ($user->tenant_id !== $tenant->id) {
            abort(404);
        }

        return view('admin.tenants.users.edit', compact('tenant', 'user'));
    }

    /**
     * Update the specified tenant user
     */
    public function usersUpdate(Request $request, Tenant $tenant, AdminUser $user)
    {
        // Ensure the user belongs to this tenant
        if ($user->tenant_id !== $tenant->id) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admin_users,email,' . $user->id,
            'admin_type' => 'required|in:super_admin,super_manager,school_admin',
            'is_active' => 'boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'admin_type' => $request->admin_type,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.tenants.users.show', [$tenant, $user])
            ->with('success', 'User updated successfully!');
    }

    /**
     * Show the form for changing tenant user password
     */
    public function usersChangePassword(Tenant $tenant, AdminUser $user)
    {
        // Ensure the user belongs to this tenant
        if ($user->tenant_id !== $tenant->id) {
            abort(404);
        }

        return view('admin.tenants.users.change-password', compact('tenant', 'user'));
    }

    /**
     * Update the tenant user's password
     */
    public function usersUpdatePassword(Request $request, Tenant $tenant, AdminUser $user)
    {
        // Ensure the user belongs to this tenant
        if ($user->tenant_id !== $tenant->id) {
            abort(404);
        }

        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'The current password is incorrect.'
            ]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.tenants.users.show', [$tenant, $user])
            ->with('success', 'Password updated successfully!');
    }
}
