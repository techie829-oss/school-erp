<?php

namespace App\Services;

use App\Models\AdminUser;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TenantUserValidationService
{
    /**
     * Validate if a user can login to a specific tenant domain
     */
    public function validateUserForTenant(string $email, string $password, Tenant $tenant): ?object
    {
        Log::info("=== VALIDATE USER FOR TENANT START ===", [
            'email' => $email,
            'tenant_id' => $tenant->id,
            'tenant_subdomain' => $tenant->data['subdomain'] ?? 'unknown',
            'database_strategy' => $tenant->data['database_strategy'] ?? 'unknown'
        ]);

        // Check if user exists and belongs to this tenant
        $user = $this->findUserInTenant($email, $tenant);

        Log::info("User lookup result", [
            'user_found' => $user ? 'yes' : 'no',
            'user_data' => $user ? [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name ?? 'unknown',
                'admin_type' => $user->admin_type ?? 'unknown',
                'is_active' => $user->is_active ?? $user->active ?? false
            ] : null
        ]);

        if (!$user) {
            Log::warning("User not found in tenant", [
                'email' => $email,
                'tenant_id' => $tenant->id
            ]);
            return null;
        }

        // Verify password
        $passwordValid = Hash::check($password, $user->password);
        Log::info("Password validation", [
            'password_valid' => $passwordValid,
            'password_hash' => substr($user->password, 0, 20) . '...'
        ]);

        if (!$passwordValid) {
            Log::warning("Invalid password for user in tenant", [
                'email' => $email,
                'tenant_id' => $tenant->id
            ]);
            return null;
        }

        // Check if user is active
        $isActive = $this->isUserActive($user);
        Log::info("User active status", [
            'is_active' => $isActive,
            'active_field' => $user->is_active ?? $user->active ?? false
        ]);

        if (!$isActive) {
            Log::warning("Inactive user attempted login", [
                'email' => $email,
                'tenant_id' => $tenant->id,
                'is_active' => $user->is_active ?? $user->active ?? false
            ]);
            return null;
        }

        Log::info("=== USER VALIDATION SUCCESSFUL ===", [
            'email' => $email,
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'admin_type' => $user->admin_type ?? 'unknown'
        ]);

        return $user;
    }

    /**
     * Find user in tenant (either shared or separate database)
     */
    protected function findUserInTenant(string $email, Tenant $tenant): ?object
    {
        if ($tenant->usesSeparateDatabase()) {
            return $this->findUserInSeparateDatabase($email, $tenant);
        } else {
            return $this->findUserInSharedDatabase($email, $tenant);
        }
    }

    /**
     * Find user in shared database
     */
    protected function findUserInSharedDatabase(string $email, Tenant $tenant): ?object
    {
        $user = AdminUser::where('email', $email)
            ->where('tenant_id', $tenant->id)
            ->first();

        return $user;
    }

    /**
     * Find user in separate database
     */
    protected function findUserInSeparateDatabase(string $email, Tenant $tenant): ?object
    {
        try {
            Log::info("=== SEPARATE DATABASE USER LOOKUP START ===", [
                'email' => $email,
                'tenant_id' => $tenant->id,
                'database_name' => $tenant->data['database_name'] ?? 'unknown',
                'database_host' => $tenant->data['database_host'] ?? 'unknown'
            ]);

            $databaseService = new TenantDatabaseService();
            $connection = $databaseService->getTenantConnection($tenant);

            Log::info("Database connection established", [
                'connection_name' => $connection->getName(),
                'database_name' => $connection->getDatabaseName()
            ]);

            $userData = $connection->table('admin_users')
                ->where('email', $email)
                ->first();

            Log::info("Database query result", [
                'user_found' => $userData ? 'yes' : 'no',
                'user_data' => $userData ? [
                    'id' => $userData->id,
                    'email' => $userData->email,
                    'name' => $userData->name,
                    'admin_type' => $userData->admin_type,
                    'is_active' => $userData->is_active ?? $userData->active ?? false,
                    'password_length' => strlen($userData->password)
                ] : null
            ]);

            if (!$userData) {
                Log::warning("User not found in separate database", [
                    'email' => $email,
                    'tenant_id' => $tenant->id,
                    'database_name' => $connection->getDatabaseName()
                ]);
                return null;
            }

            // Convert to object for consistency
            $user = (object) [
                'id' => $userData->id,
                'name' => $userData->name,
                'email' => $userData->email,
                'password' => $userData->password,
                'admin_type' => $userData->admin_type,
                'is_active' => $userData->is_active ?? $userData->active ?? false,
                'last_login_at' => $userData->last_login_at ? \Carbon\Carbon::parse($userData->last_login_at) : null,
                'created_at' => \Carbon\Carbon::parse($userData->created_at),
                'updated_at' => \Carbon\Carbon::parse($userData->updated_at),
            ];

            Log::info("User object created successfully", [
                'user_id' => $user->id,
                'email' => $user->email,
                'admin_type' => $user->admin_type
            ]);

            return $user;
        } catch (\Exception $e) {
            Log::error('=== ERROR accessing tenant database for user validation ===', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'tenant_id' => $tenant->id,
                'email' => $email
            ]);
            return null;
        }
    }

    /**
     * Check if user is active
     */
    protected function isUserActive(object $user): bool
    {
        return $user->is_active ?? $user->active ?? false;
    }

    /**
     * Get tenant by subdomain
     */
    public function getTenantBySubdomain(string $subdomain): ?Tenant
    {
        return Tenant::where('data->subdomain', $subdomain)->first();
    }

    /**
     * Validate domain access for user
     */
    public function validateDomainAccess(string $email, string $subdomain): bool
    {
        $tenant = $this->getTenantBySubdomain($subdomain);

        if (!$tenant) {
            Log::warning("Tenant not found for subdomain", [
                'subdomain' => $subdomain,
                'email' => $email
            ]);
            return false;
        }

        // Check if user exists in this tenant
        $user = $this->findUserInTenant($email, $tenant);

        if (!$user) {
            Log::warning("User not found in tenant for domain access", [
                'email' => $email,
                'subdomain' => $subdomain,
                'tenant_id' => $tenant->id
            ]);
            return false;
        }

        return true;
    }

    /**
     * Get allowed domains for a user
     */
    public function getAllowedDomainsForUser(string $email): array
    {
        $allowedDomains = [];

        // Check shared database tenants
        $sharedUsers = AdminUser::where('email', $email)->get();
        foreach ($sharedUsers as $user) {
            $tenant = Tenant::find($user->tenant_id);
            if ($tenant && isset($tenant->data['subdomain'])) {
                $allowedDomains[] = $tenant->data['subdomain'] . '.' . config('all.domains.primary');
            }
        }

        // Check separate database tenants
        $tenants = Tenant::where('data->database_strategy', 'separate')->get();
        foreach ($tenants as $tenant) {
            if ($this->findUserInTenant($email, $tenant)) {
                if (isset($tenant->data['subdomain'])) {
                    $allowedDomains[] = $tenant->data['subdomain'] . '.' . config('all.domains.primary');
                }
            }
        }

        return $allowedDomains;
    }
}
