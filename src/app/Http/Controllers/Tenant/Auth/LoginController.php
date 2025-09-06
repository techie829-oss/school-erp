<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUser;
use App\Models\Tenant;
use App\Services\TenantUserValidationService;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('tenant.auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // Get current tenant
        $tenant = tenant();

        if (!$tenant) {
            return back()->withErrors(['email' => 'Tenant not found.']);
        }

        // Use validation service to check user access
        $validationService = new TenantUserValidationService();
        $user = $validationService->validateUserForTenant(
            $credentials['email'],
            $credentials['password'],
            $tenant
        );

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid credentials or access denied.']);
        }

        // Store user in session
        session(['tenant_user' => $user]);
        session(['tenant_id' => $tenant->id]);

        // Update last login time
        $this->updateLastLoginTime($user, $tenant);

        if ($tenant && isset($tenant->data['subdomain'])) {
            return redirect()->route('tenant.admin.dashboard', ['tenant' => $tenant->data['subdomain']]);
        }

        return redirect()->route('tenant.login', ['tenant' => request()->route('tenant')]);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        // Log the logout attempt
        \Log::info('Tenant user logout', [
            'user_email' => session('tenant_user')->email ?? 'unknown',
            'tenant_id' => session('tenant_id'),
            'tenant_subdomain' => request()->route('tenant')
        ]);

        // Clear all tenant-related session data
        session()->forget([
            'tenant_user',
            'tenant_id',
            'tenant_database_switched'
        ]);

        // Also clear any Laravel auth session if it exists
        if (auth()->check()) {
            auth()->logout();
        }

        // Regenerate session ID for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to login with success message
        return redirect()->route('tenant.login', ['tenant' => request()->route('tenant')])
            ->with('success', 'You have been logged out successfully.');
    }

    /**
     * Update last login time for user
     */
    protected function updateLastLoginTime(object $user, Tenant $tenant): void
    {
        try {
            if ($tenant->usesSeparateDatabase()) {
                $databaseService = new \App\Services\TenantDatabaseService();
                $connection = $databaseService->getTenantConnection($tenant);

                $connection->table('admin_users')
                    ->where('id', $user->id)
                    ->update(['last_login_at' => now()]);
            } else {
                AdminUser::where('id', $user->id)
                    ->update(['last_login_at' => now()]);
            }
        } catch (\Exception $e) {
            \Log::error('Error updating last login time: ' . $e->getMessage());
        }
    }
}
