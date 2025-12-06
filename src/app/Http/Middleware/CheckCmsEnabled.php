<?php

namespace App\Http\Middleware;

use App\Models\TenantSetting;
use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCmsEnabled
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Handle an incoming request.
     * If CMS is disabled, redirect to login page instead of showing CMS pages.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        // CMS is an opt-in feature, defaults to false
        $cmsEnabled = TenantSetting::getSetting(
            $tenant->id,
            'feature_cms',
            false
        );

        if (!$cmsEnabled) {
            // Redirect to login page when CMS is disabled
            if (auth()->check()) {
                if (auth()->user()->user_type === 'school_admin') {
                    return redirect()->to(url('/admin/dashboard'));
                } else {
                    return redirect()->to(url('/login'));
                }
            } else {
                return redirect()->to(url('/login'));
            }
        }

        return $next($request);
    }
}
