<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\AdminUser;
use App\Services\VhostService;
use App\Services\TenantDatabaseService;
use App\Services\TenantEnvironmentService;
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
            // Database configuration (required only for separate strategy)
            'database_name' => 'required_if:database_strategy,separate|nullable|string|max:255',
            'database_host' => 'required_if:database_strategy,separate|nullable|string|max:255',
            'database_port' => 'nullable|integer|min:1|max:65535',
            'database_username' => 'required_if:database_strategy,separate|nullable|string|max:255',
            'database_password' => 'nullable|string|max:255',
            'database_charset' => 'nullable|string|max:255',
            'database_collation' => 'nullable|string|max:255',
        ]);

        // Generate unique tenant ID with better format
        $tenantId = $this->generateUniqueTenantId($validated['name']);

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

        // Prepare database configuration if separate strategy
        $databaseConfig = [];
        if ($validated['database_strategy'] === 'separate') {
            // Force 127.0.0.1 instead of localhost for TCP/IP connection
            $dbHost = $validated['database_host'];
            if ($dbHost === 'localhost') {
                $dbHost = '127.0.0.1';
            }

            $databaseConfig = [
                'database_name' => $validated['database_name'],
                'database_host' => $dbHost,
                'database_port' => $validated['database_port'] ?? 3306,
                'database_username' => $validated['database_username'],
                'database_password' => $validated['database_password'] ?? '',
                'database_charset' => $validated['database_charset'] ?? 'utf8mb4',
                'database_collation' => $validated['database_collation'] ?? 'utf8mb4_unicode_ci',
            ];
        }

        $tenant = Tenant::create([
            'id' => $tenantId,
            'data' => $tenantData,
            ...$databaseConfig
        ]);

        // Auto-create tenant environment file for separate database strategy
        if ($validated['database_strategy'] === 'separate') {
            $this->createTenantEnvironmentFile($tenant, $validated);
        }

        // Update Herd configuration if hosting type is Laravel Herd
        $this->updateHerdConfiguration($validated['subdomain']);

        return redirect()->route('admin.tenants.show', $tenant)
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
            'database_name' => 'required_if:database_strategy,separate|nullable|string|max:255',
            'database_host' => 'required_if:database_strategy,separate|nullable|string|max:255',
            'database_port' => 'nullable|integer|min:1|max:65535',
            'database_username' => 'required_if:database_strategy,separate|nullable|string|max:255',
            'database_password' => 'nullable|string|max:255',
            'database_charset' => 'nullable|string|max:255',
            'database_collation' => 'nullable|string|max:255',
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

        // Prepare database configuration
        $databaseConfig = [];
        if ($validated['database_strategy'] === 'separate') {
            // Force 127.0.0.1 instead of localhost for TCP/IP connection
            $dbHost = $validated['database_host'];
            if ($dbHost === 'localhost') {
                $dbHost = '127.0.0.1';
            }

            $databaseConfig = [
                'database_name' => $validated['database_name'],
                'database_host' => $dbHost,
                'database_port' => $validated['database_port'] ?? 3306,
                'database_username' => $validated['database_username'],
                'database_password' => $validated['database_password'] ?? '',
                'database_charset' => $validated['database_charset'] ?? 'utf8mb4',
                'database_collation' => $validated['database_collation'] ?? 'utf8mb4_unicode_ci',
            ];
        } else {
            // Clear database configuration for shared database strategy
            $databaseConfig = [
                'database_name' => null,
                'database_host' => null,
                'database_port' => null,
                'database_username' => null,
                'database_password' => null,
                'database_charset' => null,
                'database_collation' => null,
            ];
        }

        // Check if subdomain changed and update Herd configuration
        $oldSubdomain = $tenant->data['subdomain'] ?? null;
        $newSubdomain = $validated['subdomain'];

        if ($oldSubdomain !== $newSubdomain) {
            $this->updateHerdConfiguration($newSubdomain, $oldSubdomain);
        }

        $tenant->update(array_merge([
            'data' => $tenantData
        ], $databaseConfig));

        // Auto-create or update tenant environment file for separate database strategy
        if ($validated['database_strategy'] === 'separate') {
            $this->createTenantEnvironmentFile($tenant, $validated);
        } else {
            // Delete environment file if switching to shared database
            $this->deleteTenantEnvironmentFile($tenant);
        }

        return redirect()->route('admin.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant)
    {
        // Delete tenant environment file if it exists
        if ($tenant->usesSeparateDatabase()) {
            $this->deleteTenantEnvironmentFile($tenant);
        }

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
     * Get count of tenant users
     */
    public function usersCount(Tenant $tenant)
    {
        try {
            if ($tenant->usesSeparateDatabase()) {
                // Use separate database
                try {
                    $databaseService = new TenantDatabaseService();
                    $connection = $databaseService->getTenantConnection($tenant);
                    $count = $connection->table('admin_users')->count();
                } catch (\Exception $e) {
                    $count = 0;
                }
            } else {
                // Use shared database
                $count = AdminUser::where('tenant_id', $tenant->id)->count();
            }

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => 'Error counting users: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display a listing of tenant users
     */
    public function usersIndex(Tenant $tenant)
    {
        if ($tenant->usesSeparateDatabase()) {
            // Use separate database
            try {
                $databaseService = new TenantDatabaseService();
                $connection = $databaseService->getTenantConnection($tenant);

                // Get total count for pagination
                $total = $connection->table('admin_users')->count();

                $perPage = 10;
                $currentPage = request()->get('page', 1);
                $offset = ($currentPage - 1) * $perPage;

                // Get paginated users
                $usersData = $connection->table('admin_users')
                    ->orderBy('created_at', 'desc')
                    ->offset($offset)
                    ->limit($perPage)
                    ->get();

                // Convert stdClass objects to proper objects for the view
                $usersData = $usersData->map(function($user) {
                    return (object) [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'admin_type' => $user->admin_type,
                        'is_active' => $user->is_active ?? $user->active ?? false,
                        'last_login_at' => $user->last_login_at ? \Carbon\Carbon::parse($user->last_login_at) : null,
                        'created_at' => \Carbon\Carbon::parse($user->created_at),
                        'updated_at' => \Carbon\Carbon::parse($user->updated_at),
                    ];
                });

                // Create a simple paginator-like object
                $users = new \Illuminate\Pagination\LengthAwarePaginator(
                    $usersData,
                    $total,
                    $perPage,
                    $currentPage,
                    ['path' => request()->url(), 'pageName' => 'page']
                );
            } catch (\Exception $e) {
                // If separate database fails, return empty collection
                $users = new \Illuminate\Pagination\LengthAwarePaginator(
                    collect([]),
                    0,
                    10,
                    1,
                    ['path' => request()->url(), 'pageName' => 'page']
                );
            }
        } else {
            // Use shared database
            $users = AdminUser::where('tenant_id', $tenant->id)->latest()->paginate(10);
        }

        return view('admin.tenants.users.index', compact('tenant', 'users'));
    }


    /**
     * Show delete confirmation for tenant user
     */
    public function usersDelete(Tenant $tenant, $userId)
    {
        if ($tenant->usesSeparateDatabase()) {
            // Use separate database
            try {
                $databaseService = new TenantDatabaseService();
                $connection = $databaseService->getTenantConnection($tenant);
                $userData = $connection->table('admin_users')->where('id', $userId)->first();

                if (!$userData) {
                    abort(404, 'User not found');
                }

                // Convert to object for consistency
                $user = (object) [
                    'id' => $userData->id,
                    'name' => $userData->name,
                    'email' => $userData->email,
                    'admin_type' => $userData->admin_type,
                    'is_active' => $userData->is_active ?? $userData->active ?? false,
                    'last_login_at' => $userData->last_login_at ? \Carbon\Carbon::parse($userData->last_login_at) : null,
                    'created_at' => \Carbon\Carbon::parse($userData->created_at),
                    'updated_at' => \Carbon\Carbon::parse($userData->updated_at),
                ];
            } catch (\Exception $e) {
                abort(404, 'User not found');
            }
        } else {
            // Use shared database
            $user = AdminUser::where('id', $userId)->where('tenant_id', $tenant->id)->first();
            if (!$user) {
                abort(404, 'User not found');
            }
        }

        return view('admin.tenants.users.delete', compact('tenant', 'user'));
    }

    /**
     * Delete tenant user
     */
    public function usersDestroy(Tenant $tenant, $userId)
    {
        $request = request();

        // Validate confirmation input
        $request->validate([
            'confirmation_email' => 'required|string',
            'confirmation_text' => 'required|string|in:DELETE'
        ]);

        if ($tenant->usesSeparateDatabase()) {
            // Use separate database
            try {
                $databaseService = new TenantDatabaseService();
                $connection = $databaseService->getTenantConnection($tenant);
                $userData = $connection->table('admin_users')->where('id', $userId)->first();

                if (!$userData) {
                    return redirect()->back()->with('error', 'User not found');
                }

                // Verify email matches
                if ($userData->email !== $request->confirmation_email) {
                    return redirect()->back()->with('error', 'Email confirmation does not match. Please try again.');
                }

                // Delete user
                $connection->table('admin_users')->where('id', $userId)->delete();

                return redirect()->route('admin.tenants.users.index', $tenant)
                    ->with('success', 'User has been permanently deleted.');

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error deleting user: ' . $e->getMessage());
            }
        } else {
            // Use shared database
            $user = AdminUser::where('id', $userId)->where('tenant_id', $tenant->id)->first();

            if (!$user) {
                return redirect()->back()->with('error', 'User not found');
            }

            // Verify email matches
            if ($user->email !== $request->confirmation_email) {
                return redirect()->back()->with('error', 'Email confirmation does not match. Please try again.');
            }

            // Delete user
            $user->delete();

            return redirect()->route('admin.tenants.users.index', $tenant)
                ->with('success', 'User has been permanently deleted.');
        }
    }

    /**
     * Debug tenant database configuration
     */
    public function debugDatabase(Tenant $tenant)
    {
        $debug = [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->data['name'] ?? 'N/A',
            'database_strategy' => $tenant->data['database_strategy'] ?? 'not set',
            'uses_separate_database' => $tenant->usesSeparateDatabase(),
            'database_config' => $tenant->getDatabaseConfig(),
            'connection_name' => $tenant->getConnectionName(),
        ];

        // Try to get database connection info
        try {
            if ($tenant->usesSeparateDatabase()) {
                $databaseService = new TenantDatabaseService();
                $connection = $databaseService->getTenantConnection($tenant);
                $debug['connection_success'] = true;
                $debug['connection_name_used'] = $connection->getName();
                $debug['tables'] = $connection->select("SHOW TABLES");
                $debug['admin_users_count'] = $connection->table('admin_users')->count();
            } else {
                $debug['connection_success'] = true;
                $debug['connection_name_used'] = 'mysql (shared)';
                $debug['admin_users_count'] = AdminUser::where('tenant_id', $tenant->id)->count();
            }
        } catch (\Exception $e) {
            $debug['connection_success'] = false;
            $debug['connection_error'] = $e->getMessage();
        }

        return response()->json($debug, 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Show the form for creating a new tenant user
     */
    public function usersCreate(Tenant $tenant)
    {
        return view('admin.tenants.users.create', compact('tenant'));
    }

    /**
     * Store a newly created tenant user
     */
    public function usersStore(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'admin_type' => 'required|in:super_admin,super_manager,school_admin',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        // Check email uniqueness based on database strategy
        if ($tenant->usesSeparateDatabase()) {
            $databaseService = new TenantDatabaseService();
            $connection = $databaseService->getTenantConnection($tenant);
            $existingUser = $connection->table('admin_users')->where('email', $validated['email'])->first();
            if ($existingUser) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['email' => 'The email address is already taken.']);
            }
        } else {
            $existingUser = AdminUser::where('email', $validated['email'])->first();
            if ($existingUser) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['email' => 'The email address is already taken.']);
            }
        }

        try {
            if ($tenant->usesSeparateDatabase()) {
                // Create user in tenant database
                $databaseService = new TenantDatabaseService();
                $connection = $databaseService->getTenantConnection($tenant);

                $userId = $connection->table('admin_users')->insertGetId([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'admin_type' => $validated['admin_type'],
                    'password' => Hash::make($validated['password']),
                    'is_active' => $validated['is_active'] ?? true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Create user in shared database
                $user = AdminUser::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'admin_type' => $validated['admin_type'],
                    'password' => Hash::make($validated['password']),
                    'is_active' => $validated['is_active'] ?? true,
                    'tenant_id' => $tenant->id,
                ]);
            }

            return redirect()->route('admin.tenants.users.index', $tenant)
                ->with('success', 'User created successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified tenant user
     */
    public function usersShow(Tenant $tenant, $userId)
    {
        \Log::info("=== USER SHOW DEBUG START ===", [
            'tenant_id' => $tenant->id,
            'user_id' => $userId,
            'uses_separate_db' => $tenant->usesSeparateDatabase(),
            'database_strategy' => $tenant->data['database_strategy'] ?? 'unknown'
        ]);

        if ($tenant->usesSeparateDatabase()) {
            // Use separate database
            try {
                $databaseService = new TenantDatabaseService();
                $connection = $databaseService->getTenantConnection($tenant);
                $userData = $connection->table('admin_users')->where('id', $userId)->first();

                \Log::info("User show - tenant database lookup", [
                    'user_found' => $userData ? 'yes' : 'no',
                    'user_data' => $userData ? [
                        'id' => $userData->id,
                        'name' => $userData->name,
                        'email' => $userData->email
                    ] : null
                ]);

                if (!$userData) {
                    \Log::error("User not found in tenant database for show", [
                        'tenant_id' => $tenant->id,
                        'user_id' => $userId
                    ]);
                    abort(404, 'User not found in tenant database');
                }

                // Convert to object for consistency
                $user = (object) [
                    'id' => $userData->id,
                    'name' => $userData->name,
                    'email' => $userData->email,
                    'admin_type' => $userData->admin_type,
                    'is_active' => $userData->is_active ?? $userData->active ?? false,
                    'last_login_at' => $userData->last_login_at ? \Carbon\Carbon::parse($userData->last_login_at) : null,
                    'created_at' => \Carbon\Carbon::parse($userData->created_at),
                    'updated_at' => \Carbon\Carbon::parse($userData->updated_at),
                ];
            } catch (\Exception $e) {
                \Log::error("Error accessing tenant database for user show", [
                    'error' => $e->getMessage(),
                    'tenant_id' => $tenant->id,
                    'user_id' => $userId
                ]);
                abort(404, 'User not found');
            }
        } else {
            // Use shared database
            $user = AdminUser::where('id', $userId)->where('tenant_id', $tenant->id)->first();

            \Log::info("User show - shared database lookup", [
                'user_found' => $user ? 'yes' : 'no',
                'user_data' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ] : null
            ]);

            if (!$user) {
                \Log::error("User not found in shared database for show", [
                    'tenant_id' => $tenant->id,
                    'user_id' => $userId
                ]);
                abort(404, 'User not found in shared database');
            }
        }

        \Log::info("=== USER SHOW SUCCESS ===", [
            'tenant_id' => $tenant->id,
            'user_id' => $userId,
            'database_used' => $tenant->usesSeparateDatabase() ? 'separate' : 'shared'
        ]);

        return view('admin.tenants.users.show', compact('tenant', 'user'));
    }

    /**
     * Show the form for editing the specified tenant user
     */
    public function usersEdit(Tenant $tenant, $userId)
    {
        // Default to shared database
        $user = AdminUser::where('id', $userId)->where('tenant_id', $tenant->id)->first();

        // Only use separate database if explicitly configured and working
        if ($tenant->usesSeparateDatabase()) {
            try {
                $databaseService = new TenantDatabaseService();
                $connection = $databaseService->getTenantConnection($tenant);
                $userData = $connection->table('admin_users')->where('id', $userId)->first();

                if ($userData) {
                    // Convert to object for consistency
                    $user = (object) [
                        'id' => $userData->id,
                        'name' => $userData->name,
                        'email' => $userData->email,
                        'admin_type' => $userData->admin_type,
                        'is_active' => $userData->is_active ?? $userData->active ?? false,
                        'last_login_at' => $userData->last_login_at ? \Carbon\Carbon::parse($userData->last_login_at) : null,
                        'created_at' => \Carbon\Carbon::parse($userData->created_at),
                        'updated_at' => \Carbon\Carbon::parse($userData->updated_at),
                    ];
                }
            } catch (\Exception $e) {
                \Log::error('Error accessing tenant database for user edit, using shared: ' . $e->getMessage());
                // Keep the default shared database user
            }
        }

        if (!$user) {
            abort(404);
        }

        return view('admin.tenants.users.edit', compact('tenant', 'user'));
    }

    /**
     * Update the specified tenant user
     */
    public function usersUpdate(Request $request, Tenant $tenant, $userId)
    {
        \Log::info("=== USER UPDATE DEBUG START ===", [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->data['name'] ?? 'unknown',
            'user_id' => $userId,
            'database_strategy' => $tenant->data['database_strategy'] ?? 'unknown',
            'uses_separate_db' => $tenant->usesSeparateDatabase(),
            'request_data' => $request->all()
        ]);

        // Only check shared database if NOT using separate database
        $user = null;
        if (!$tenant->usesSeparateDatabase()) {
            $user = AdminUser::where('id', $userId)->where('tenant_id', $tenant->id)->first();

            \Log::info("Shared database user lookup", [
                'user_found' => $user ? 'yes' : 'no',
                'user_data' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'admin_type' => $user->admin_type
                ] : null
            ]);

            if (!$user) {
                \Log::error("User not found in shared database", [
                    'tenant_id' => $tenant->id,
                    'user_id' => $userId
                ]);
                abort(404, 'User not found in shared database');
            }
        } else {
            \Log::info("Skipping shared database lookup for separate database tenant");
        }

        // Validate request - different validation for separate vs shared database
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'admin_type' => 'required|in:super_admin,super_manager,school_admin',
            'is_active' => 'boolean',
        ];

        // Add email uniqueness validation based on database type
        if ($tenant->usesSeparateDatabase()) {
            // For separate database, we'll check uniqueness manually in the try-catch block
            $request->validate($validationRules);
        } else {
            // For shared database, use standard unique validation
            $validationRules['email'] = 'required|email|max:255|unique:admin_users,email,' . $userId;
            $request->validate($validationRules);
        }

        // Only use separate database if explicitly configured and working
        if ($tenant->usesSeparateDatabase()) {
            try {
                \Log::info("=== SEPARATE DATABASE UPDATE START ===", [
                    'tenant_id' => $tenant->id,
                    'user_id' => $userId,
                    'database_strategy' => $tenant->data['database_strategy'] ?? 'unknown',
                    'database_name' => $tenant->data['database_name'] ?? 'unknown',
                    'database_host' => $tenant->data['database_host'] ?? 'unknown'
                ]);

                $databaseService = new TenantDatabaseService();
                $connection = $databaseService->getTenantConnection($tenant);

                \Log::info("Database connection established", [
                    'connection_name' => $connection->getName(),
                    'database_name' => $connection->getDatabaseName()
                ]);

                // Check if user exists in tenant database
                $tenantUser = $connection->table('admin_users')->where('id', $userId)->first();

                \Log::info("Tenant user lookup result", [
                    'user_found' => $tenantUser ? 'yes' : 'no',
                    'user_data' => $tenantUser ? [
                        'id' => $tenantUser->id,
                        'name' => $tenantUser->name,
                        'email' => $tenantUser->email,
                        'admin_type' => $tenantUser->admin_type
                    ] : null,
                    'query_executed' => 'SELECT * FROM admin_users WHERE id = ' . $userId
                ]);

                if ($tenantUser) {
                    // Check email uniqueness in tenant database
                    $existingUser = $connection->table('admin_users')
                        ->where('email', $request->email)
                        ->where('id', '!=', $userId)
                        ->first();

                    if ($existingUser) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['email' => 'The email address is already taken.']);
                    }

                    // Update user in tenant database
                    $connection->table('admin_users')
                        ->where('id', $userId)
                        ->update([
                            'name' => $request->name,
                            'email' => $request->email,
                            'admin_type' => $request->admin_type,
                            'is_active' => $request->has('is_active'),
                            'updated_at' => now(),
                        ]);
                } else {
                    \Log::error("User not found in tenant database", [
                        'tenant_id' => $tenant->id,
                        'user_id' => $userId,
                        'database_name' => $connection->getDatabaseName()
                    ]);
                    abort(404, 'User not found in tenant database');
                }
            } catch (\Exception $e) {
                \Log::error('=== ERROR accessing tenant database for user update ===', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'tenant_id' => $tenant->id,
                    'user_id' => $userId,
                    'database_strategy' => $tenant->data['database_strategy'] ?? 'unknown'
                ]);
                abort(500, 'Error accessing tenant database: ' . $e->getMessage());
            }
        } else {
            // Use shared database
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'admin_type' => $request->admin_type,
                'is_active' => $request->has('is_active'),
            ]);
        }

        \Log::info("=== USER UPDATE SUCCESS ===", [
            'tenant_id' => $tenant->id,
            'user_id' => $userId,
            'database_used' => $tenant->usesSeparateDatabase() ? 'separate' : 'shared'
        ]);

        return redirect()->route('admin.tenants.users.show', [$tenant, $userId])
            ->with('success', 'User updated successfully!');
    }

    /**
     * Show the form for changing tenant user password
     */
    public function usersChangePassword(Tenant $tenant, $userId)
    {
        if ($tenant->usesSeparateDatabase()) {
            // Get user from tenant database
            $databaseService = new TenantDatabaseService();
            $connection = $databaseService->getTenantConnection($tenant);
            $user = $connection->table('admin_users')->where('id', $userId)->first();

            if (!$user) {
                abort(404);
            }
        } else {
            // Get user from shared database
            $user = AdminUser::where('id', $userId)->where('tenant_id', $tenant->id)->first();

            if (!$user) {
                abort(404);
            }
        }

        return view('admin.tenants.users.change-password', compact('tenant', 'user'));
    }

    /**
     * Update the tenant user's password
     */
    public function usersUpdatePassword(Request $request, Tenant $tenant, $userId)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if ($tenant->usesSeparateDatabase()) {
            // Get user from tenant database
            $databaseService = new TenantDatabaseService();
            $connection = $databaseService->getTenantConnection($tenant);
            $user = $connection->table('admin_users')->where('id', $userId)->first();

            if (!$user) {
                abort(404);
            }

            // Update password in tenant database
            $connection->table('admin_users')
                ->where('id', $userId)
                ->update([
                    'password' => Hash::make($request->password),
                    'updated_at' => now(),
                ]);
        } else {
            // Get user from shared database
            $user = AdminUser::where('id', $userId)->where('tenant_id', $tenant->id)->first();

            if (!$user) {
                abort(404);
            }

            // Update password
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('admin.tenants.users.show', [$tenant, $userId])
            ->with('success', 'Password updated successfully!');
    }

    /**
     * Generate a unique tenant ID with improved format
     */
    private function generateUniqueTenantId(string $name): string
    {
        // Create base ID from name (lowercase, alphanumeric, hyphens only)
        $baseId = Str::slug($name, '-');

        // Ensure it's not too long (max 50 chars for database)
        if (strlen($baseId) > 45) {
            $baseId = substr($baseId, 0, 45);
        }

        // Add timestamp suffix for better uniqueness
        $timestamp = now()->format('ymd');
        $tenantId = $baseId . '-' . $timestamp;

        // Check uniqueness and add counter if needed
        $counter = 1;
        $originalTenantId = $tenantId;

        while (Tenant::where('id', $tenantId)->exists()) {
            $tenantId = $originalTenantId . '-' . $counter;
            $counter++;
        }

        return $tenantId;
    }

    /**
     * Toggle tenant active status (deactivate/activate)
     */
    public function toggleStatus(Tenant $tenant)
    {
        $currentStatus = $tenant->data['active'] ?? true;
        $newStatus = !$currentStatus;

        $tenantData = $tenant->data;
        $tenantData['active'] = $newStatus;

        $tenant->update(['data' => $tenantData]);

        $status = $newStatus ? 'activated' : 'deactivated';

        return redirect()->route('admin.tenants.index')
            ->with('success', "Tenant '{$tenant->data['name']}' has been {$status} successfully!");
    }

    /**
     * Test tenant database connection
     */
    public function testDatabaseConnection(Tenant $tenant, TenantDatabaseService $databaseService)
    {
        $result = $databaseService->testTenantConnection($tenant);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'connection' => $result['connection'] ?? null,
                'database' => $result['database'] ?? null
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
                'error' => $result['error'] ?? null
            ], 400);
        }
    }

    /**
     * Create tenant database
     */
    public function createDatabase(Tenant $tenant, TenantDatabaseService $databaseService)
    {
        $result = $databaseService->createTenantDatabase($tenant);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'database' => $result['database'] ?? null
        ]);
    }

    /**
     * Run migrations for tenant database
     */
    public function runMigrations(Tenant $tenant, TenantDatabaseService $databaseService)
    {
        $result = $databaseService->runTenantMigrations($tenant);

        // If migration was successful, get the tables and create primary admin user
        if ($result['success']) {
            $tablesResult = $databaseService->getTenantTables($tenant);
            if ($tablesResult['success']) {
                $result['tables'] = $tablesResult['tables'];
                $result['message'] .= ' (' . count($tablesResult['tables']) . ' tables created)';
            }

            // Create primary admin user for separate database tenants
            if ($tenant->usesSeparateDatabase()) {
                $adminResult = $databaseService->createPrimaryAdminUser($tenant);
                if ($adminResult['success']) {
                    $result['admin_user'] = [
                        'email' => $adminResult['email'] ?? $tenant->data['email'],
                        'default_password' => $adminResult['default_password'] ?? 'admin123'
                    ];
                    $result['message'] .= ' Primary admin user created.';
                } else {
                    $result['admin_user_error'] = $adminResult['message'];
                }
            }
        }

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'tables' => $result['tables'] ?? null,
            'admin_user' => $result['admin_user'] ?? null,
            'admin_user_error' => $result['admin_user_error'] ?? null
        ]);
    }

    /**
     * Get tenant database tables
     */
    public function getDatabaseTables(Tenant $tenant, TenantDatabaseService $databaseService)
    {
        $result = $databaseService->getTenantTables($tenant);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'tables' => $result['tables']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);
        }
    }

    /**
     * Get comprehensive database information for a tenant
     */
    public function getDatabaseInfo(Tenant $tenant)
    {
        try {
            if (!$tenant->usesSeparateDatabase()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No separate database needed for shared strategy'
                ]);
            }

            $databaseService = new TenantDatabaseService();
            $connection = $databaseService->getTenantConnection($tenant);

            // Get database information
            $dbInfo = $connection->select('SELECT DATABASE() as current_db, VERSION() as version, NOW() as current_time');
            $tableCount = $connection->table('information_schema.tables')
                ->where('table_schema', $dbInfo[0]->current_db)
                ->count();

            $userCount = $connection->table('admin_users')->count();

            return response()->json([
                'success' => true,
                'database' => $dbInfo[0]->current_db,
                'version' => $dbInfo[0]->version,
                'current_time' => $dbInfo[0]->current_time,
                'table_count' => $tableCount,
                'user_count' => $userCount,
                'host' => $tenant->database_host ?? 'N/A',
                'connection_name' => $connection->getName()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting database info: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Automatically create tenant environment file for separate database
     */
    private function createTenantEnvironmentFile(Tenant $tenant, array $validated): void
    {
        try {
            $envService = new TenantEnvironmentService();
            $subdomain = $validated['subdomain'] ?? $tenant->data['subdomain'];

            if (!$subdomain) {
                Log::warning('Cannot create tenant env file without subdomain', [
                    'tenant_id' => $tenant->id
                ]);
                return;
            }

            // Build environment configuration from validated input (use standard DB_ prefix)
            // Force 127.0.0.1 instead of localhost to use TCP/IP connection
            $dbHost = $validated['database_host'] ?? '127.0.0.1';
            if ($dbHost === 'localhost') {
                $dbHost = '127.0.0.1';
            }

            $envConfig = [
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $dbHost,
                'DB_PORT' => $validated['database_port'] ?? '3306',
                'DB_DATABASE' => $validated['database_name'] ?? '',
                'DB_USERNAME' => $validated['database_username'] ?? 'root',
                'DB_PASSWORD' => $validated['database_password'] ?? '',
                'DB_CHARSET' => $validated['database_charset'] ?? 'utf8mb4',
                'DB_COLLATION' => $validated['database_collation'] ?? 'utf8mb4_unicode_ci',
            ];

            // Create the environment file
            $created = $envService->createTenantEnvironmentFile($tenant, $envConfig);

            if ($created) {
                Log::info('Tenant environment file auto-created', [
                    'tenant_id' => $tenant->id,
                    'subdomain' => $subdomain,
                    'file' => ".env.tenant.{$subdomain}",
                    'database' => $envConfig['TENANT_DB_DATABASE']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to auto-create tenant environment file', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception - this is a convenience feature
        }
    }

    /**
     * Delete tenant environment file when switching to shared database
     */
    private function deleteTenantEnvironmentFile(Tenant $tenant): void
    {
        try {
            $envService = new TenantEnvironmentService();

            if ($envService->hasTenantEnvironmentFile($tenant)) {
                $deleted = $envService->deleteTenantEnvironmentFile($tenant);

                if ($deleted) {
                    Log::info('Tenant environment file auto-deleted (switched to shared database)', [
                        'tenant_id' => $tenant->id,
                        'subdomain' => $tenant->data['subdomain'] ?? 'unknown'
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to auto-delete tenant environment file', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw exception - this is a convenience feature
        }
    }

    /**
     * Check if tenant environment file exists
     */
    public function getEnvFileStatus(Tenant $tenant)
    {
        try {
            $envService = new TenantEnvironmentService();
            $exists = $envService->hasTenantEnvironmentFile($tenant);

            $subdomain = $tenant->data['subdomain'] ?? null;
            $primaryDomain = config('all.domains.primary');
            $filename = $subdomain ? ".env.{$subdomain}.{$primaryDomain}" : null;

            return response()->json([
                'success' => true,
                'exists' => $exists,
                'filename' => $filename,
                'path' => $exists ? base_path($filename) : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'exists' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * View tenant environment file
     */
    public function viewEnvFile(Tenant $tenant)
    {
        try {
            $envService = new TenantEnvironmentService();

            if (!$envService->hasTenantEnvironmentFile($tenant)) {
                abort(404, 'Environment file not found');
            }

            $subdomain = $tenant->data['subdomain'] ?? null;
            if (!$subdomain) {
                abort(400, 'Tenant has no subdomain');
            }

            $primaryDomain = config('all.domains.primary');
            $filename = ".env.{$subdomain}.{$primaryDomain}";
            $filePath = base_path($filename);

            $content = \File::get($filePath);

            // Return as plain text view
            return response($content)
                ->header('Content-Type', 'text/plain')
                ->header('X-Filename', $filename);
        } catch (\Exception $e) {
            abort(500, 'Error reading environment file: ' . $e->getMessage());
        }
    }

    /**
     * Download tenant environment file
     */
    public function downloadEnvFile(Tenant $tenant)
    {
        try {
            $envService = new TenantEnvironmentService();

            if (!$envService->hasTenantEnvironmentFile($tenant)) {
                abort(404, 'Environment file not found');
            }

            $subdomain = $tenant->data['subdomain'] ?? null;
            if (!$subdomain) {
                abort(400, 'Tenant has no subdomain');
            }

            $primaryDomain = config('all.domains.primary');
            $filename = ".env.{$subdomain}.{$primaryDomain}";
            $filePath = base_path($filename);

            return response()->download($filePath, $filename);
        } catch (\Exception $e) {
            abort(500, 'Error downloading environment file: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate tenant environment file
     */
    public function regenerateEnvFile(Tenant $tenant)
    {
        try {
            if (!$tenant->usesSeparateDatabase()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This tenant does not use separate database'
                ]);
            }

            $envService = new TenantEnvironmentService();
            $subdomain = $tenant->data['subdomain'] ?? null;

            if (!$subdomain) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tenant has no subdomain defined'
                ]);
            }

            // Build environment configuration
            $envConfig = [
                'DB_CONNECTION' => 'mysql',
                'DB_HOST' => $tenant->database_host ?? '127.0.0.1',
                'DB_PORT' => $tenant->database_port ?? '3306',
                'DB_DATABASE' => $tenant->database_name ?? '',
                'DB_USERNAME' => $tenant->database_username ?? 'root',
                'DB_PASSWORD' => $tenant->database_password ?? '',
                'DB_CHARSET' => $tenant->database_charset ?? 'utf8mb4',
                'DB_COLLATION' => $tenant->database_collation ?? 'utf8mb4_unicode_ci',
            ];

            // Create/recreate the environment file
            $created = $envService->createTenantEnvironmentFile($tenant, $envConfig);

            if ($created) {
                $primaryDomain = config('all.domains.primary');
                $filename = ".env.{$subdomain}.{$primaryDomain}";

                return response()->json([
                    'success' => true,
                    'message' => "Environment file regenerated successfully: {$filename}"
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to regenerate environment file'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error regenerating environment file: ' . $e->getMessage()
            ]);
        }
    }
}
