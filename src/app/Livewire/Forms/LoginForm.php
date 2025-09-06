<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Services\TenantUserValidationService;
use App\Models\Tenant;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;


    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Check if this is a tenant domain login
        if ($this->isTenantDomain()) {
            $this->validateTenantDomainAccess();
        }

        // Determine which guard to use based on the current domain
        $guard = $this->getGuardForCurrentDomain();

        if (! Auth::guard($guard)->attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
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
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
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
    protected function validateTenantDomainAccess(): void
    {
        $host = request()->getHost();
        $subdomain = $this->extractSubdomain($host);

        if (!$subdomain) {
            throw ValidationException::withMessages([
                'form.email' => 'Invalid tenant domain.',
            ]);
        }

        $validationService = new TenantUserValidationService();

        // Check if user has access to this tenant domain
        if (!$validationService->validateDomainAccess($this->email, $subdomain)) {
            $allowedDomains = $validationService->getAllowedDomainsForUser($this->email);

            $message = 'You do not have access to this tenant domain.';
            if (!empty($allowedDomains)) {
                $message .= ' You can access: ' . implode(', ', $allowedDomains);
            }

            throw ValidationException::withMessages([
                'form.email' => $message,
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
}
