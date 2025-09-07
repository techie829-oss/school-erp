<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TenantUserValidationService;
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

        if (!Auth::guard($guard)->attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // After successful authentication, validate tenant domain access if needed
        if ($this->isTenantDomain()) {
            $this->validateTenantDomainAccess($request->email, $request);
        }

        $request->session()->regenerate();
        RateLimiter::clear($this->throttleKey($request));

        // Redirect to appropriate dashboard
        $redirectRoute = $this->getRedirectRoute();
        return redirect()->intended($redirectRoute);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $isTenantDomain = $this->isTenantDomain();

        // Clear tenant session data if on tenant domain
        if ($isTenantDomain) {
            session()->forget(['tenant_user', 'tenant_id', 'tenant_database_switched']);
        }

        Auth::guard($this->getGuardForCurrentDomain())->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($isTenantDomain) {
            return redirect()->route('tenant.login', ['tenant' => request()->route('tenant')])
                ->with('success', 'You have been logged out successfully.');
        }

        return redirect()->route('admin.login');
    }

    /**
     * Get the appropriate guard based on the current domain
     */
    protected function getGuardForCurrentDomain(): string
    {
        $host = request()->getHost();
        $adminDomain = config('all.domains.admin');

        // If we're on the admin domain, use admin guard
        if ($host === $adminDomain) {
            return 'admin';
        }

        // For tenant domains, use admin guard (school admins are also admin users)
        return 'admin';
    }

    /**
     * Check if current domain is a tenant domain
     */
    protected function isTenantDomain(): bool
    {
        $host = request()->getHost();
        $adminDomain = config('all.domains.admin');
        $primaryDomain = config('all.domains.primary');

        // Check if it's a tenant subdomain (e.g., school.myschool.test)
        return $host !== $adminDomain && str_ends_with($host, '.' . $primaryDomain);
    }

    /**
     * Validate tenant domain access
     */
    protected function validateTenantDomainAccess(string $email, Request $request): void
    {
        $host = request()->getHost();
        $subdomain = $this->extractSubdomain($host);

        if (!$subdomain) {
            throw ValidationException::withMessages([
                'email' => 'Invalid tenant domain.',
            ]);
        }

        $validationService = new TenantUserValidationService();

        // Check if user has access to this tenant domain
        if (!$validationService->validateDomainAccess($email, $subdomain)) {
            $allowedDomains = $validationService->getAllowedDomainsForUser($email);

            $message = 'You do not have access to this tenant domain.';
            if (!empty($allowedDomains)) {
                $message .= ' You can access: ' . implode(', ', $allowedDomains);
            }

            throw ValidationException::withMessages([
                'email' => $message,
            ]);
        }
    }

    /**
     * Extract subdomain from host
     */
    protected function extractSubdomain(string $host): ?string
    {
        $primaryDomain = config('all.domains.primary');

        if (str_ends_with($host, '.' . $primaryDomain)) {
            return str_replace('.' . $primaryDomain, '', $host);
        }

        return null;
    }

    /**
     * Get the appropriate redirect route based on the current domain and user type
     */
    protected function getRedirectRoute(): string
    {
        $user = auth()->user();
        $policy = new AdminAccessPolicy();
        
        // Get the appropriate redirect URL based on user role and current domain
        $redirectUrl = $policy->getRedirectUrl($user);
        
        if ($redirectUrl) {
            return $redirectUrl;
        }

        // Default redirects based on current domain
        $host = request()->getHost();
        $adminDomain = config('all.domains.admin');

        if ($host === $adminDomain) {
            return route('admin.dashboard', absolute: false);
        }

        // For tenant domains, redirect to tenant admin dashboard
        $tenant = tenant();
        if ($tenant && isset($tenant->data['subdomain'])) {
            return route('tenant.admin.dashboard', ['tenant' => $tenant->data['subdomain']], absolute: false);
        }

        // Fallback to admin dashboard
        return route('admin.dashboard', absolute: false);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->email) . '|' . $request->ip());
    }
}
