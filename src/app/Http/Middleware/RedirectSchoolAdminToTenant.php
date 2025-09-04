<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectSchoolAdminToTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply this middleware on admin domain
        $host = $request->getHost();
        $adminDomain = config('all.domains.admin');
        
        if ($host === $adminDomain && auth()->check()) {
            $user = auth()->user();
            
            // If user is school_admin, redirect to their tenant domain
            if ($user && $user->admin_type === 'school_admin') {
                $tenantUrl = $user->getTenantUrl();
                if ($tenantUrl) {
                    return redirect($tenantUrl);
                }
            }
        }
        
        return $next($request);
    }
}
