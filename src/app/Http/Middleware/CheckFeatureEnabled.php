<?php

namespace App\Http\Middleware;

use App\Models\TenantSetting;
use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureEnabled
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        // Get feature setting (default to true for backward compatibility)
        $enabled = TenantSetting::getSetting(
            $tenant->id,
            "feature_{$feature}",
            true // Default to enabled if not set
        );

        if (!$enabled) {
            abort(403, "The {$feature} feature is disabled for your institution.");
        }

        return $next($request);
    }
}

