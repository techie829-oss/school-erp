<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TenantUserValidationService;
use App\Services\TenantAuthenticationService;
use App\Services\TenantContextService;
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

            if ($tenant) {
                $authService = new TenantAuthenticationService(app(TenantContextService::class));
                $authResult = $authService->authenticateForTenant($credentials, $tenant, $remember);

                if (!$authResult) {
                    RateLimiter::hit($this->throttleKey($request));
                    throw ValidationException::withMessages([
                        'email' => trans('auth.failed'),
                    ]);
                }
            } else {
                RateLimiter::hit($this->throttleKey($request));
                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }
        } else {
            // For admin domain, use normal authentication
            if (!Auth::guard($guard)->attempt($credentials, $remember)) {
                RateLimiter::hit($this->throttleKey($request));
                throw ValidationException::withMessages([
                    'email' => trans('auth.failed'),
                ]);
            }
        }

        // Regenerate session for security
        // The TenantContextService ensures sessions are handled in the main database
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
     * Authenticate against tenant database for separate database tenants
     */
    protected function authenticateAgainstTenantDatabase(array $credentials, $tenant): bool
    {
        try {
            $databaseService = new \App\Services\TenantDatabaseService();
            $connection = $databaseService->getTenantConnection($tenant);

            $userData = $connection->table('admin_users')
                ->where('email', $credentials['email'])
                ->first();

            if (!$userData) {
                return false;
            }

            // Verify password
            if (!\Illuminate\Support\Facades\Hash::check($credentials['password'], $userData->password)) {
                return false;
            }

            // Check if user is active
            $isActive = $userData->is_active ?? $userData->active ?? false;
            if (!$isActive) {
                return false;
            }

            // Create a proper user object that implements Authenticatable
            $user = new class($userData, $tenant->id) implements \Illuminate\Contracts\Auth\Authenticatable {
                private $userData;
                private $tenantId;

                public function __construct($userData, $tenantId) {
                    $this->userData = $userData;
                    $this->tenantId = $tenantId;
                }

                public function getAuthIdentifierName() {
                    return 'id';
                }

                public function getAuthIdentifier() {
                    return $this->userData->id;
                }

                public function getAuthPassword() {
                    return $this->userData->password;
                }

                public function getAuthPasswordName() {
                    return 'password';
                }

                public function getRememberToken() {
                    return null;
                }

                public function setRememberToken($value) {
                    // Not implemented for this use case
                }

                public function getRememberTokenName() {
                    return 'remember_token';
                }

                // Additional methods for accessing user data
                public function __get($key) {
                    if ($key === 'tenant_id') {
                        return $this->tenantId;
                    }
                    return $this->userData->$key ?? null;
                }

                public function __isset($key) {
                    if ($key === 'tenant_id') {
                        return true;
                    }
                    return isset($this->userData->$key);
                }
            };

            // Manually log in the user using the admin guard
            // Note: We need to ensure the session is established properly
            // even though we're in a switched database context
            \Illuminate\Support\Facades\Auth::guard('admin')->login($user, false);

            return true;

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error authenticating against tenant database', [
                'error' => $e->getMessage(),
                'tenant_id' => $tenant->id,
                'email' => $credentials['email']
            ]);
            return false;
        }
    }

    /**
     * Resolve tenant from subdomain manually
     */
    protected function resolveTenantFromSubdomain()
    {
        $host = request()->getHost();
        $subdomain = $this->extractSubdomain($host);

        if (!$subdomain) {
            return null;
        }

        // Find tenant by subdomain
        return \App\Models\Tenant::where('data->subdomain', $subdomain)->first();
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
        $host = request()->getHost();
        $adminDomain = config('all.domains.admin');

        // If we're on a tenant domain, always redirect to tenant admin dashboard
        if ($host !== $adminDomain) {
            $tenant = $this->resolveTenantFromSubdomain();
            if ($tenant && isset($tenant->data['subdomain'])) {
                return route('tenant.admin.dashboard', ['tenant' => $tenant->data['subdomain']], absolute: false);
            }
        }

        // For admin domain, use policy-based redirects
        $policy = new AdminAccessPolicy();
        $redirectUrl = $policy->getRedirectUrl($user);

        if ($redirectUrl) {
            return $redirectUrl;
        }

        // Default to admin dashboard for admin domain
        if ($host === $adminDomain) {
            return route('admin.dashboard', absolute: false);
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
