<?php

namespace App\Services;

use App\Models\AdminUser;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TenantUserValidationService
{
    /**
     * Validate user credentials for tenant login
     */
    public function validateUserForTenant(string $email, string $password, Tenant $tenant): bool
    {
        Log::info('Validating user for tenant', [
            'email' => $email,
            'tenant_id' => $tenant->id,
            'tenant_subdomain' => $tenant->data['subdomain'] ?? 'unknown'
        ]);

        try {
            // Find user in shared database with tenant_id filter
            $user = AdminUser::where('email', $email)
                ->where('tenant_id', $tenant->id)
                ->first();

            if (!$user) {
                Log::warning('User not found for tenant', [
                    'email' => $email,
                    'tenant_id' => $tenant->id
                ]);
                return false;
            }

            // Verify password
            if (!Hash::check($password, $user->password)) {
                Log::warning('Invalid password for user', [
                    'email' => $email,
                    'tenant_id' => $tenant->id
                ]);
                return false;
            }

            // Check if user is active
            if (!$user->is_active) {
                Log::warning('Inactive user attempted login', [
                    'email' => $email,
                    'tenant_id' => $tenant->id
                ]);
                return false;
            }

            Log::info('User validation successful', [
                'email' => $email,
                'tenant_id' => $tenant->id,
                'user_id' => $user->id
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('User validation failed', [
                'email' => $email,
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get user data for tenant
     */
    public function getUserForTenant(string $email, Tenant $tenant): ?AdminUser
    {
        try {
            return AdminUser::where('email', $email)
                ->where('tenant_id', $tenant->id)
                ->first();
        } catch (\Exception $e) {
            Log::error('Failed to get user for tenant', [
                'email' => $email,
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Check if email exists in any tenant (for admin domain login redirect)
     */
    public function findTenantForEmail(string $email): ?array
    {
        try {
            // Check shared database tenants
            $user = AdminUser::where('email', $email)->first();

            if ($user && $user->admin_type === 'school_admin' && $user->tenant_id) {
                $tenant = Tenant::find($user->tenant_id);

                if ($tenant) {
                    return [
                        'tenant' => $tenant,
                        'user' => $user,
                        'tenant_domain' => $tenant->data['subdomain'] . '.' . config('all.domains.primary'),
                        'tenant_name' => $tenant->data['name'] ?? 'Your School'
                    ];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to find tenant for email', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
