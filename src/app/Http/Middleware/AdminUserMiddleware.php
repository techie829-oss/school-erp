<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            $host = $request->getHost();
            $adminDomain = config('all.domains.admin');

            if ($host === $adminDomain) {
                return redirect()->route('admin.login');
            } else {
                return redirect()->route('tenant.login', ['tenant' => request()->route('tenant')]);
            }
        }

        return $next($request);
    }
}
