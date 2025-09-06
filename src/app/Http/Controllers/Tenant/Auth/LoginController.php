<?php

namespace App\Http\Controllers\Tenant\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AdminUser;
use App\Models\Tenant;

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

        // Check if user exists in tenant database
        if ($tenant->usesSeparateDatabase()) {
            try {
                $databaseService = new \App\Services\TenantDatabaseService();
                $connection = $databaseService->getTenantConnection($tenant);
                $userData = $connection->table('admin_users')->where('email', $credentials['email'])->first();

                if ($userData && Hash::check($credentials['password'], $userData->password)) {
                    // Create a temporary user object for authentication
                    $user = (object) [
                        'id' => $userData->id,
                        'name' => $userData->name,
                        'email' => $userData->email,
                        'admin_type' => $userData->admin_type,
                        'is_active' => $userData->is_active ?? $userData->active ?? false,
                    ];

                    // Store user in session
                    session(['tenant_user' => $user]);
                    session(['tenant_id' => $tenant->id]);

                    if ($tenant && isset($tenant->data['subdomain'])) {
                        return redirect()->route('tenant.admin.dashboard', ['tenant' => $tenant->data['subdomain']]);
                    }
                    return redirect()->route('tenant.login', ['tenant' => request()->route('tenant')]);
                }
            } catch (\Exception $e) {
                \Log::error('Tenant login error: ' . $e->getMessage());
            }
        } else {
            // Use shared database
            $user = AdminUser::where('email', $credentials['email'])
                ->where('tenant_id', $tenant->id)
                ->first();

            if ($user && Hash::check($credentials['password'], $user->password)) {
                // Store user in session
                session(['tenant_user' => $user]);
                session(['tenant_id' => $tenant->id]);

                if ($tenant && isset($tenant->data['subdomain'])) {
                    return redirect()->route('tenant.admin.dashboard', ['tenant' => $tenant->data['subdomain']]);
                }
                return redirect()->route('tenant.login', ['tenant' => request()->route('tenant')]);
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        session()->forget(['tenant_user', 'tenant_id']);

        return redirect()->route('tenant.login', ['tenant' => request()->route('tenant')]);
    }
}
