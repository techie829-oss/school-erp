<?php

namespace App\Policies;

use App\Models\AdminUser;
use App\Models\User;
use Illuminate\Http\Request;

class AdminAccessPolicy
{
    /**
     * Check if user can access admin domain
     */
    public function canAccessAdminDomain($user): bool
    {
        // Check if user is AdminUser (admin domain users)
        if ($user instanceof AdminUser) {
            return $user->isSuperAdmin() || $user->isSuperManager();
        }

        // Check if user is regular User with super admin role
        if ($user instanceof User) {
            return $user->isSuperAdmin() || $user->hasRole('super_manager');
        }

        return false;
    }

    /**
     * Check if user can access tenant domain
     */
    public function canAccessTenantDomain($user, $tenant = null): bool
    {
        // AdminUser with school_admin type can access their tenant
        if ($user instanceof AdminUser) {
            if ($user->isSchoolAdmin()) {
                // If tenant is specified, check if user belongs to that tenant
                if ($tenant) {
                    return $user->tenant_id === $tenant->id;
                }
                return true; // Can access tenant domains in general
            }
            return false; // Super admin/manager should not access tenant domains
        }

        // Regular User can access tenant domains if they belong to a tenant
        if ($user instanceof User) {
            return $user->tenant_id !== null;
        }

        return false;
    }

    /**
     * Get the appropriate redirect URL for user
     */
    public function getRedirectUrl($user): ?string
    {
        // AdminUser logic
        if ($user instanceof AdminUser) {
            if ($user->isSuperAdmin() || $user->isSuperManager()) {
                return null; // Can stay on admin domain
            }
            
            if ($user->isSchoolAdmin()) {
                return $user->getTenantUrl('/admin');
            }
        }

        // Regular User logic
        if ($user instanceof User) {
            if ($user->isSuperAdmin() || $user->hasRole('super_manager')) {
                return null; // Can stay on admin domain
            }
            
            if ($user->tenant_id) {
                $tenant = $user->tenant;
                if ($tenant && isset($tenant->data['full_domain'])) {
                    $protocol = request()->secure() ? 'https' : 'http';
                    return $protocol . '://' . $tenant->data['full_domain'] . '/admin';
                }
            }
        }

        return null;
    }

    /**
     * Check if user should be redirected from current domain
     */
    public function shouldRedirectFromAdmin($user): bool
    {
        // AdminUser logic
        if ($user instanceof AdminUser) {
            return $user->isSchoolAdmin(); // School admin should be redirected to tenant
        }

        // Regular User logic
        if ($user instanceof User) {
            return !$user->isSuperAdmin() && !$user->hasRole('super_manager');
        }

        return false;
    }

    /**
     * Check if user should be redirected from tenant domain
     */
    public function shouldRedirectFromTenant($user, $currentTenant): bool
    {
        // AdminUser logic
        if ($user instanceof AdminUser) {
            if ($user->isSuperAdmin() || $user->isSuperManager()) {
                return true; // Should be redirected to admin domain
            }
            
            if ($user->isSchoolAdmin()) {
                return $user->tenant_id !== $currentTenant->id; // Wrong tenant
            }
        }

        // Regular User logic
        if ($user instanceof User) {
            if ($user->isSuperAdmin() || $user->hasRole('super_manager')) {
                return true; // Should be redirected to admin domain
            }
            
            return $user->tenant_id !== $currentTenant->id; // Wrong tenant
        }

        return false;
    }
}
