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
        // Check if user is authenticated (using web guard with users table)
        // This includes school_admin, teachers, staff, students
        if (auth()->check()) {
            return $next($request);
        }

        // No authentication found, redirect to login
        return redirect('/login');
    }
}
