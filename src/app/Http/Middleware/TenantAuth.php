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
        // Check if tenant user is authenticated
        if (!session('tenant_user')) {
            return redirect()->route('tenant.login', ['tenant' => request()->route('tenant')]);
        }

        return $next($request);
    }
}
