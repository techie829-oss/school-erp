<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
        /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // Determine the correct login route based on the domain
        $host = $request->getHost();
        $adminDomain = config('all.domains.admin');

        // If we're on the admin domain, redirect to admin login
        if ($host === $adminDomain) {
            return route('admin.login');
        }

        // For tenant domains, extract tenant subdomain and redirect to tenant login
        $tenantSubdomain = str_replace('.myschool.test', '', $host);
        return route('tenant.login', ['tenant' => $tenantSubdomain]);
    }

    /**
     * Determine which guard to use based on the request.
     */
    protected function authenticate($request, array $guards)
    {
        // Determine the correct guard based on the domain
        $host = $request->getHost();
        $adminDomain = config('all.domains.admin');

        // If we're on the admin domain, use admin guard
        if ($host === $adminDomain) {
            $guards = ['admin'];
        } else {
            // For tenant domains, use admin guard (school admins are also admin users)
            $guards = ['admin'];
        }

        return parent::authenticate($request, $guards);
    }
}
