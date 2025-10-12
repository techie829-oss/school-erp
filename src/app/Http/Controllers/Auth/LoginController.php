<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TenantUserValidationService;
use App\Services\TenantAuthenticationService;
use App\Policies\AdminAccessPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // Check if we're on a tenant domain
        if ($this->isTenantDomain()) {
            $tenant = $this->resolveTenantFromSubdomain();
            return view('tenant.auth.login', compact('tenant'));
        }

        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $this->ensureIsNotRateLimited($request);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Determine which guard to use based on the current domain
        $guard = $this->getGuardForCurrentDomain();

        // For tenant domains, use tenant authentication service
        if ($this->isTenantDomain()) {
            $tenant = $this->resolveTenantFromSubdomain();
            if (!$tenant) {
                throw ValidationException::withMessages([
                    'email' => 'Invalid tenant domain.',
                ]);
            }

            $authService = new TenantAuthenticationService();
            if ($authService->authenticateForTenant($credentials, $tenant)) {
                // Regenerate session for security
                $request->session()->regenerate();

                // Store user info in session for easy access
                $user = auth()->user();
                if ($user) {
                    session([
                        'tenant_user' => [
                            'id' => $user->id,
                            'name' => $user->name ?? 'Admin',
                            'email' => $user->email,
                            'admin_type' => $user->admin_type ?? 'school_admin',
                        ]
                    ]);
                }

                RateLimiter::clear($this->throttleKey($request));

                // Redirect to appropriate dashboard
                $redirectRoute = $this->getRedirectRoute();
                return redirect()->intended($redirectRoute);
            } else {
                RateLimiter::hit($this->throttleKey($request));
                throw ValidationException::withMessages([
                    'email' => 'These credentials do not match our records.',
                ]);
            }
        } else {
            // For admin domain, use admin guard (super admin with admin_users table)
            if (Auth::guard('admin')->attempt($credentials, $remember)) {
                $request->session()->regenerate();
                RateLimiter::clear($this->throttleKey($request));

                return redirect()->intended(route('admin.dashboard'));
            } else {
                RateLimiter::hit($this->throttleKey($request));
                throw ValidationException::withMessages([
                    'email' => 'These credentials do not match our records.',
                ]);
            }
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        // Logout from appropriate guard based on domain
        if ($this->isTenantDomain()) {
            Auth::guard('tadmin')->logout();
        } else {
            Auth::guard('admin')->logout();
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect based on domain
        if ($this->isTenantDomain()) {
            return redirect()->route('tenant.login');
        }

        return redirect()->route('admin.login');
    }

    /**
     * Check if current domain is a tenant domain
     */
    protected function isTenantDomain(): bool
    {
        $host = request()->getHost();
        $primaryDomain = config('all.domains.primary');
        $adminDomain = config('all.domains.admin');

        // Check if it's not the admin domain and has the primary domain pattern
        return $host !== $adminDomain && str_ends_with($host, '.' . $primaryDomain);
    }

    /**
     * Resolve tenant from subdomain
     */
    protected function resolveTenantFromSubdomain()
    {
        $host = request()->getHost();
        $primaryDomain = config('all.domains.primary');

        if (!str_ends_with($host, '.' . $primaryDomain)) {
            return null;
        }

        $subdomain = str_replace('.' . $primaryDomain, '', $host);

        return \App\Models\Tenant::where('data->subdomain', $subdomain)->first();
    }

    /**
     * Get the appropriate guard for current domain
     */
    protected function getGuardForCurrentDomain(): string
    {
        return $this->isTenantDomain() ? 'web' : 'web';
    }

    /**
     * Get redirect route after login
     */
    protected function getRedirectRoute(): string
    {
        if ($this->isTenantDomain()) {
            // For tenant domains, redirect to tenant admin dashboard (not app domain)
            return '/admin/dashboard';  // Relative path to stay on current domain
        }

        return route('admin.dashboard');
    }

    /**
     * Ensure the login request is not rate limited
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }
}
