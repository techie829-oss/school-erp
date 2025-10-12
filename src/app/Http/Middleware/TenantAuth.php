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
        // Check if tenant admin is authenticated (using tadmin guard with admin_users table)
        if (auth('tadmin')->check()) {
            return $next($request);
        }

        // Fallback: Check regular users (staff, teachers, students) via web guard
        if (auth('web')->check()) {
            return $next($request);
        }

        // No authentication found, redirect to login
        return redirect()->route('tenant.login');
    }
}
