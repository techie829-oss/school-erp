<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TenantService
{
    /**
     * Get the current tenant ID from the request.
     */
    public function getCurrentTenantId(): ?string
    {
        $host = request()->getHost();
        $primaryDomain = config('all.domains.primary');
        $adminDomain = config('all.domains.admin');

        // Check if it's the admin domain
        if ($host === $adminDomain) {
            return null;
        }

        // Check if it's the primary domain (landing page)
        if ($host === $primaryDomain) {
            return null;
        }

        // Check if it's a subdomain
        if (str_ends_with($host, '.' . $primaryDomain)) {
            $subdomain = str_replace('.' . $primaryDomain, '', $host);
            return $this->findTenantBySubdomain($subdomain);
        }

        // Check if it's a custom domain
        return $this->findTenantByCustomDomain($host);
    }

    /**
     * Find tenant by subdomain.
     */
    public function findTenantBySubdomain(string $subdomain): ?string
    {
        $tenant = Tenant::whereJsonContains('data->subdomain', $subdomain)->first();
        return $tenant ? $tenant->id : null;
    }

    /**
     * Find tenant by custom domain.
     */
    public function findTenantByCustomDomain(string $domain): ?string
    {
        $tenant = Tenant::whereJsonContains('data->custom_domain', $domain)->first();
        return $tenant ? $tenant->id : null;
    }

    /**
     * Get tenant domain information.
     */
    public function getTenantDomainInfo(string $tenantId): array
    {
        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            return [];
        }

        $data = $tenant->data;
        $domainInfo = [
            'tenant_id' => $tenantId,
            'domain_type' => $data['domain_type'] ?? 'subdomain',
            'full_domain' => $data['full_domain'] ?? $tenantId . '.' . config('all.domains.primary'),
        ];

        if ($data['domain_type'] === 'subdomain') {
            $domainInfo['subdomain'] = $data['subdomain'] ?? $tenantId;
            $domainInfo['base_domain'] = config('all.domains.primary');
        } else {
            $domainInfo['custom_domain'] = $data['custom_domain'] ?? null;
        }

        return $domainInfo;
    }

    /**
     * Validate domain availability.
     */
    public function validateDomainAvailability(string $domainType, string $domain): array
    {
        $errors = [];

        if ($domainType === 'subdomain') {
            // Check subdomain format
            if (!preg_match('/^[a-z0-9-]+$/', $domain)) {
                $errors[] = 'Subdomain can only contain lowercase letters, numbers, and hyphens.';
            }

            // Check if subdomain is reserved
            $reservedSubdomains = ['www', 'admin', 'api', 'app', 'mail', 'ftp', 'blog', 'shop', 'store'];
            if (in_array($domain, $reservedSubdomains)) {
                $errors[] = 'This subdomain is reserved and cannot be used.';
            }

            // Check if subdomain already exists
            if (Tenant::whereJsonContains('data->subdomain', $domain)->exists()) {
                $errors[] = 'This subdomain is already taken.';
            }
        } else {
            // Check custom domain format
            if (!preg_match('/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', $domain)) {
                $errors[] = 'Please enter a valid domain name.';
            }

            // Check if custom domain already exists
            if (Tenant::whereJsonContains('data->custom_domain', $domain)->exists()) {
                $errors[] = 'This custom domain is already taken.';
            }
        }

        return $errors;
    }

    /**
     * Generate domain suggestions based on tenant name.
     */
    public function generateDomainSuggestions(string $name, string $domainType = 'subdomain'): array
    {
        $suggestions = [];
        $baseSlug = Str::slug($name);

        if ($domainType === 'subdomain') {
            $suggestions[] = $baseSlug;
            
            // Add variations
            $variations = [
                $baseSlug . '-school',
                $baseSlug . '-academy',
                $baseSlug . '-institute',
                $baseSlug . '-college',
                $baseSlug . '-university',
            ];

            foreach ($variations as $variation) {
                if (!$this->isDomainTaken('subdomain', $variation)) {
                    $suggestions[] = $variation;
                }
            }
        } else {
            $suggestions[] = $baseSlug . '.com';
            $suggestions[] = $baseSlug . '.org';
            $suggestions[] = $baseSlug . '.edu';
            $suggestions[] = $baseSlug . '.net';
        }

        return array_slice($suggestions, 0, 5); // Return max 5 suggestions
    }

    /**
     * Check if domain is already taken.
     */
    public function isDomainTaken(string $domainType, string $domain): bool
    {
        if ($domainType === 'subdomain') {
            return Tenant::whereJsonContains('data->subdomain', $domain)->exists();
        } else {
            return Tenant::whereJsonContains('data->custom_domain', $domain)->exists();
        }
    }

    /**
     * Get all tenant domains for vhost configuration.
     */
    public function getAllTenantDomains(): array
    {
        $tenants = Tenant::where('data->active', true)->get();
        $domains = [];

        foreach ($tenants as $tenant) {
            $data = $tenant->data;
            if (isset($data['full_domain'])) {
                $domains[] = [
                    'tenant_id' => $tenant->id,
                    'domain' => $data['full_domain'],
                    'type' => $data['domain_type'] ?? 'subdomain',
                    'subdomain' => $data['subdomain'] ?? null,
                    'custom_domain' => $data['custom_domain'] ?? null,
                ];
            }
        }

        return $domains;
    }

    /**
     * Update tenant domain configuration.
     */
    public function updateTenantDomain(string $tenantId, array $domainData): bool
    {
        try {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                return false;
            }

            $data = $tenant->data;
            $data['domain_type'] = $domainData['domain_type'];
            
            if ($domainData['domain_type'] === 'subdomain') {
                $data['subdomain'] = $domainData['subdomain'];
                $data['full_domain'] = $domainData['subdomain'] . '.' . config('all.domains.primary');
                unset($data['custom_domain']);
            } else {
                $data['custom_domain'] = $domainData['custom_domain'];
                $data['full_domain'] = $domainData['custom_domain'];
                unset($data['subdomain']);
            }

            $data['updated_at'] = now()->toISOString();

            $tenant->update(['data' => $data]);

            Log::info('Tenant domain updated', [
                'tenant_id' => $tenantId,
                'domain_data' => $domainData
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to update tenant domain', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get domain statistics.
     */
    public function getDomainStatistics(): array
    {
        $totalTenants = Tenant::count();
        $subdomainTenants = Tenant::whereJsonContains('data->domain_type', 'subdomain')->count();
        $customDomainTenants = Tenant::whereJsonContains('data->domain_type', 'custom')->count();
        $activeTenants = Tenant::where('data->active', true)->count();

        return [
            'total_tenants' => $totalTenants,
            'subdomain_tenants' => $subdomainTenants,
            'custom_domain_tenants' => $customDomainTenants,
            'active_tenants' => $activeTenants,
            'inactive_tenants' => $totalTenants - $activeTenants,
        ];
    }
}