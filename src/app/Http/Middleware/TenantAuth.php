<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via admin guard (for AdminUser model)
        if (auth('admin')->check()) {
            return $next($request);
        }

        // Fallback: Check if tenant user is authenticated via session (legacy)
        if (session('tenant_user')) {
            return $next($request);
        }

        // No authentication found, redirect to login
        return redirect()->route('tenant.login', ['tenant' => request()->route('tenant')]);
    }
}
