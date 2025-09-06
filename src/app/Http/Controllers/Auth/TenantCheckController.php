<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use Illuminate\Http\Request;

class TenantCheckController extends Controller
{
    /**
     * Check if email belongs to a tenant user
     */
    public function checkTenantUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $host = request()->getHost();
        $adminDomain = config('all.domains.admin');

        // Only check if we're on admin domain
        if ($host !== $adminDomain) {
            return response()->json(['redirect' => false]);
        }

        // Check if user exists in shared database tenants
        $user = AdminUser::where('email', $email)->first();

        if ($user && $user->admin_type === 'school_admin') {
            // Get the tenant for this user
            $tenant = Tenant::find($user->tenant_id);

            if ($tenant) {
                // Build the tenant login URL
                $tenantDomain = $tenant->data['subdomain'] . '.' . config('all.domains.primary');
                $tenantLoginUrl = 'http://' . $tenantDomain . '/login';

                return response()->json([
                    'redirect' => true,
                    'message' => 'This is a school administrator account.',
                    'tenant_name' => $tenant->data['name'] ?? 'Your School',
                    'login_url' => $tenantLoginUrl,
                    'tenant_domain' => $tenantDomain
                ]);
            }
        } else {
            // Check if user exists in separate database tenants
            $tenants = Tenant::where('data->database_strategy', 'separate')->get();

            foreach ($tenants as $tenant) {
                try {
                    $databaseService = new TenantDatabaseService();
                    $connection = $databaseService->getTenantConnection($tenant);
                    $userData = $connection->table('admin_users')->where('email', $email)->first();

                    if ($userData) {
                        // Build the tenant login URL
                        $tenantDomain = $tenant->data['subdomain'] . '.' . config('all.domains.primary');
                        $tenantLoginUrl = 'http://' . $tenantDomain . '/login';

                        return response()->json([
                            'redirect' => true,
                            'message' => 'This is a school administrator account.',
                            'tenant_name' => $tenant->data['name'] ?? 'Your School',
                            'login_url' => $tenantLoginUrl,
                            'tenant_domain' => $tenantDomain
                        ]);
                    }
                } catch (\Exception $e) {
                    // Continue checking other tenants
                    continue;
                }
            }
        }

        return response()->json(['redirect' => false]);
    }
}
