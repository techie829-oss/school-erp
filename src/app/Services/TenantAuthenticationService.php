<?php

namespace App\Services;

use App\Models\AdminUser;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TenantAuthenticationService
{

    public function __construct()
    {
        // No dependencies needed for simplified shared database approach
    }

    /**
     * Authenticate user for tenant (unified for both shared and separate databases)
     */
    public function authenticateForTenant(array $credentials, Tenant $tenant, bool $remember = false): bool
    {
        Log::info('Authenticating user for tenant', [
            'email' => $credentials['email'],
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->data['name'] ?? 'Unknown',
            'database_strategy' => $tenant->data['database_strategy'] ?? 'shared'
        ]);

        // Check if tenant is active
        if (!$tenant->isActive()) {
            Log::warning('Authentication failed - tenant is inactive', [
                'email' => $credentials['email'],
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->data['name'] ?? 'Unknown'
            ]);
            return false;
        }

        // Initialize tenant context (automatically handles env file loading and database setup)

        try {
            // Unified authentication - context service handles the database connection
            $user = $this->authenticateUser($credentials, $tenant);

            if ($user) {
                // Log in the user using web guard (all tenant users)
                Auth::login($user, $remember);

                Log::info('User authenticated successfully', [
                    'email' => $credentials['email'],
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->getAuthIdentifier(),
                    'database_strategy' => $tenant->data['database_strategy'] ?? 'shared'
                ]);

                return true;
            }

            Log::warning('Authentication failed', [
                'email' => $credentials['email'],
                'tenant_id' => $tenant->id,
                'database_strategy' => $tenant->data['database_strategy'] ?? 'shared'
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Authentication error', [
                'email' => $credentials['email'],
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return false;
        }
    }

    /**
     * Authenticate user (shared database only)
     */
    protected function authenticateUser(array $credentials, Tenant $tenant): ?\App\Models\User
    {
        // First, verify tenant is active (double-check)
        if (!$tenant->isActive()) {
            Log::warning('Tenant is inactive during user authentication', [
                'tenant_id' => $tenant->id,
                'email' => $credentials['email']
            ]);
            return null;
        }

        // Query users table with tenant_id filter (school admins, teachers, staff, students)
        $user = \App\Models\User::where('email', $credentials['email'])
            ->where('tenant_id', $tenant->id)
            ->first();

        if (!$user) {
            Log::info('User not found for tenant', [
                'email' => $credentials['email'],
                'tenant_id' => $tenant->id
            ]);
            return null;
        }

        // Check if user is active BEFORE verifying password (security best practice)
        if (!$user->is_active) {
            Log::warning('User account is inactive', [
                'email' => $credentials['email'],
                'user_id' => $user->id,
                'tenant_id' => $tenant->id
            ]);
            return null;
        }

        // Verify password
        if (!Hash::check($credentials['password'], $user->password)) {
            Log::info('Invalid password for user', [
                'email' => $credentials['email'],
                'tenant_id' => $tenant->id
            ]);
            return null;
        }

        return $user;
    }

    /**
     * Validate domain access for user (unified approach)
     */
    public function validateDomainAccess(string $email, Tenant $tenant): bool
    {
        try {
            // Query admin_users table in main database with tenant_id filter
            $userExists = AdminUser::where('email', $email)
                ->where('tenant_id', $tenant->id)
                ->exists();

            Log::info('Domain access validation', [
                'email' => $email,
                'tenant_id' => $tenant->id,
                'user_exists' => $userExists
            ]);

            return $userExists;
        } catch (\Exception $e) {
            Log::error('Domain access validation failed', [
                'email' => $email,
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get user for tenant (unified approach)
     */
    public function getUserForTenant(string $email, Tenant $tenant): ?object
    {

        try {
            // Get the correct database connection
            $connection = TenantContextService::getDatabaseConnection($tenant);

            // Query admin_users table
            $query = $connection->table('admin_users')->where('email', $email);

            // For shared database, also filter by tenant_id
            if (!$tenant->usesSeparateDatabase()) {
                $query->where('tenant_id', $tenant->id);
            }

            return $query->first();
        } catch (\Exception $e) {
            Log::error('Get user error', [
                'email' => $email,
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
