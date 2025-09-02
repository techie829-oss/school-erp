<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TenantService
{
    /**
     * Get the current tenant based on the request domain.
     */
    public function getCurrentTenant(Request $request): ?Tenant
    {
        $host = $request->getHost();

        // If we have a tenant subdomain parameter, use that for more precise detection
        if ($request->has('tenant_subdomain')) {
            $tenantSubdomain = $request->get('tenant_subdomain');
            return Cache::remember("tenant_subdomain_{$tenantSubdomain}", 3600, function () use ($tenantSubdomain) {
                return Tenant::where('data->domain', $tenantSubdomain . '.' . config('all.domains.primary'))->first();
            });
        }

        return Cache::remember("tenant_host_{$host}", 3600, function () use ($host) {
            return Tenant::where('data->domain', $host)->first();
        });
    }

    /**
     * Get the current tenant ID.
     */
    public function getCurrentTenantId(Request $request): ?string
    {
        $tenant = $this->getCurrentTenant($request);

        // For development, if no tenant found, return 'landing' as default
        if (!$tenant) {
            return 'landing';
        }

        return $tenant->id;
    }

    /**
     * Check if the current tenant uses shared database strategy.
     */
    public function isSharedDatabase(Request $request): bool
    {
        $tenant = $this->getCurrentTenant($request);
        return $tenant?->data['database_strategy'] === 'shared';
    }

    /**
     * Check if the current tenant uses separate database strategy.
     */
    public function isSeparateDatabase(Request $request): bool
    {
        $tenant = $this->getCurrentTenant($request);
        return $tenant?->data['database_strategy'] === 'separate';
    }

    /**
     * Get tenant type (internal, school, landing).
     */
    public function getTenantType(Request $request): ?string
    {
        $tenant = $this->getCurrentTenant($request);
        return $tenant?->data['type'];
    }

    /**
     * Check if current tenant is internal admin.
     */
    public function isInternalAdmin(Request $request): bool
    {
        return $this->getTenantType($request) === 'internal';
    }

    /**
     * Check if current tenant is a school.
     */
    public function isSchool(Request $request): bool
    {
        return $this->getTenantType($request) === 'school';
    }

    /**
     * Check if current tenant is landing page.
     */
    public function isLanding(Request $request): bool
    {
        return $this->getTenantType($request) === 'landing';
    }

    /**
     * Get all tenants with their database strategies.
     */
    public function getAllTenants(): array
    {
        return Cache::remember('all_tenants', 3600, function () {
            return Tenant::all()->map(function ($tenant) {
                return [
                    'id' => $tenant->id,
                    'name' => $tenant->data['name'] ?? 'Unknown',
                    'domain' => $tenant->data['domain'] ?? 'unknown.test',
                    'database_strategy' => $tenant->data['database_strategy'] ?? 'shared',
                    'type' => $tenant->data['type'] ?? 'unknown',
                    'status' => $tenant->data['status'] ?? 'inactive',
                    'description' => $tenant->data['description'] ?? '',
                    'student_count' => $tenant->data['student_count'] ?? null,
                    'location' => $tenant->data['location'] ?? null,
                ];
            })->toArray();
        });
    }

    /**
     * Get tenants by database strategy.
     */
    public function getTenantsByStrategy(string $strategy): array
    {
        $tenants = $this->getAllTenants();
        return array_filter($tenants, fn($tenant) => $tenant['database_strategy'] === $strategy);
    }

    /**
     * Get shared database tenants.
     */
    public function getSharedDatabaseTenants(): array
    {
        return $this->getTenantsByStrategy('shared');
    }

    /**
     * Get separate database tenants.
     */
    public function getSeparateDatabaseTenants(): array
    {
        return $this->getTenantsByStrategy('separate');
    }

    /**
     * Get tenant database connection details.
     */
    public function getTenantDatabase(Request $request): array
    {
        $tenant = $this->getCurrentTenant($request);

        if (!$tenant) {
            return config('database.connections.mysql');
        }

        if ($tenant->data['database_strategy'] === 'separate') {
            // For separate databases, return tenant-specific connection
            return [
                'driver' => 'mysql',
                'host' => config('database.connections.mysql.host'),
                'port' => config('database.connections.mysql.port'),
                'database' => $tenant->data['database'],
                'username' => config('database.connections.mysql.username'),
                'password' => config('database.connections.mysql.password'),
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ];
        }

        // For shared database, return default connection
        return config('database.connections.mysql');
    }

    /**
     * Get tenant information for display.
     */
    public function getTenantInfo(Request $request): array
    {
        $tenant = $this->getCurrentTenant($request);

        if (!$tenant) {
            return [
                'id' => null,
                'name' => 'Unknown Tenant',
                'domain' => $request->getHost(),
                'database_strategy' => 'unknown',
                'type' => 'unknown',
                'status' => 'inactive',
            ];
        }

        return [
            'id' => $tenant->id,
            'name' => $tenant->data['name'] ?? 'Unknown',
            'domain' => $tenant->data['domain'] ?? 'unknown.test',
            'database_strategy' => $tenant->data['database_strategy'] ?? 'shared',
            'type' => $tenant->data['type'] ?? 'unknown',
            'status' => $tenant->data['status'] ?? 'inactive',
            'description' => $tenant->data['description'] ?? '',
            'student_count' => $tenant->data['student_count'] ?? null,
            'location' => $tenant->data['location'] ?? null,
        ];
    }

    /**
     * Get available tenant subdomains for routing.
     */
    public function getAvailableTenantSubdomains(): array
    {
        return Cache::remember('available_tenant_subdomains', 3600, function () {
            $tenants = Tenant::where('data->type', 'school')->get();

            return $tenants->map(function ($tenant) {
                $domain = $tenant->data['domain'] ?? '';
                $subdomain = str_replace('.' . config('all.domains.primary'), '', $domain);
                return [
                    'subdomain' => $subdomain,
                    'name' => $tenant->data['name'] ?? 'Unknown',
                    'type' => $tenant->data['type'] ?? 'unknown',
                    'database_strategy' => $tenant->data['database_strategy'] ?? 'shared',
                ];
            })->filter(function ($tenant) {
                return !empty($tenant['subdomain']) && $tenant['subdomain'] !== $tenant['name'];
            })->values()->toArray();
        });
    }
}
