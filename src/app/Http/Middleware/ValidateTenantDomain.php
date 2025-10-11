<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\TenantUserValidationService;
use App\Models\Tenant;

class ValidateTenantDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to tenant domains
        if (!$this->isTenantDomain($request)) {
            return $next($request);
        }

        $subdomain = $this->extractSubdomain($request->getHost());

        if (!$subdomain) {
            abort(404, 'Invalid tenant domain');
        }

        // Check if tenant exists
        $tenant = Tenant::where('data->subdomain', $subdomain)->first();

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        // User already authenticated - they have access
        // The TenantAuthenticationService already validated they belong to this tenant
        // No need to check again here

        return $next($request);
    }

    /**
     * Check if current domain is a tenant domain
     */
    protected function isTenantDomain(Request $request): bool
    {
        $host = $request->getHost();
        $adminDomain = config('all.domains.admin');
        $primaryDomain = config('all.domains.primary');

        // Check if it's a tenant subdomain (e.g., school.myschool.test)
        return $host !== $adminDomain && str_ends_with($host, '.' . $primaryDomain);
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
