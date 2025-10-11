<?php

namespace App\Services;

use App\Models\AdminUser;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TenantAuthenticationService
{
    protected $contextService;

    public function __construct(TenantContextService $contextService)
    {
        $this->contextService = $contextService;
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

        // Initialize tenant context (automatically handles env file loading and database setup)
        $this->contextService->initializeContext($tenant);

        try {
            // Unified authentication - context service handles the database connection
            $user = $this->authenticateUser($credentials, $tenant);

            if ($user) {
                // Log in the user
                Auth::guard('admin')->login($user, $remember);

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
     * Authenticate user (unified for both shared and separate databases)
     * TenantContextService has already set up the correct database connection
     */
    protected function authenticateUser(array $credentials, Tenant $tenant): ?object
    {
        // Get the correct database connection (already configured by contextService)
        $connection = TenantContextService::getDatabaseConnection($tenant);

        // Query admin_users table (works for both shared and separate databases)
        $query = $connection->table('admin_users')
            ->where('email', $credentials['email']);

        // For shared database, also filter by tenant_id
        if (!$tenant->usesSeparateDatabase()) {
            $query->where('tenant_id', $tenant->id);
        }

        $userData = $query->first();

        if (!$userData) {
            return null;
        }

        // Verify password
        if (!Hash::check($credentials['password'], $userData->password)) {
            return null;
        }

        // Check if user is active
        $isActive = $userData->is_active ?? $userData->active ?? false;
        if (!$isActive) {
            return null;
        }

        // For shared database, return AdminUser model
        if (!$tenant->usesSeparateDatabase()) {
            return AdminUser::find($userData->id);
        }

        // For separate database, create authenticatable user object
        return new class($userData, $tenant->id) implements \Illuminate\Contracts\Auth\Authenticatable {
            public $id;
            public $name;
            public $email;
            public $admin_type;
            public $is_active;
            public $tenant_id;
            private $userData;

            public function __construct($userData, $tenantId) {
                $this->userData = $userData;
                $this->tenantId = $tenantId;
                
                // Expose properties directly for easy access
                $this->id = $userData->id;
                $this->name = $userData->name;
                $this->email = $userData->email;
                $this->admin_type = $userData->admin_type ?? 'school_admin';
                $this->is_active = $userData->is_active ?? $userData->active ?? false;
                $this->tenant_id = $tenantId;
            }

            public function getAuthIdentifierName() { return 'id'; }
            public function getAuthIdentifier() { return $this->userData->id; }
            public function getAuthPassword() { return $this->userData->password; }
            public function getAuthPasswordName() { return 'password'; }
            public function getRememberToken() { return null; }
            public function setRememberToken($value) { }
            public function getRememberTokenName() { return 'remember_token'; }

            public function __get($key) {
                return $this->userData->$key ?? null;
            }

            public function __isset($key) {
                return isset($this->userData->$key);
            }
        };
    }

    /**
     * Validate domain access for user (unified approach)
     */
    public function validateDomainAccess(string $email, Tenant $tenant): bool
    {
        $this->contextService->initializeContext($tenant);

        try {
            // Get the correct database connection
            $connection = TenantContextService::getDatabaseConnection($tenant);

            // Query admin_users table
            $query = $connection->table('admin_users')->where('email', $email);

            // For shared database, also filter by tenant_id
            if (!$tenant->usesSeparateDatabase()) {
                $query->where('tenant_id', $tenant->id);
            }

            $user = $query->first();
            return $user !== null;
        } catch (\Exception $e) {
            Log::error('Domain access validation error', [
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
        $this->contextService->initializeContext($tenant);

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
