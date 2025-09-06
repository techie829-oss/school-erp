<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Services\TenantDatabaseService;
use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SwitchTenantDatabase
{
    protected $tenantService;
    protected $databaseService;

    public function __construct(TenantService $tenantService, TenantDatabaseService $databaseService)
    {
        $this->tenantService = $tenantService;
        $this->databaseService = $databaseService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Get current tenant from request
            $tenant = $this->tenantService->getCurrentTenant($request);

            if ($tenant) {
                // Switch to tenant's database connection
                $this->databaseService->switchToTenantDatabase($tenant);

                // Store tenant in request for later use
                $request->attributes->set('current_tenant', $tenant);
            }
        } catch (\Exception $e) {
            // Log error but don't break the request
            \Log::warning('Failed to switch tenant database: ' . $e->getMessage(), [
                'url' => $request->url(),
                'host' => $request->getHost(),
                'error' => $e->getMessage()
            ]);
        }

        $response = $next($request);

        // Reset to default connection after request
        $this->databaseService->resetToDefaultConnection();

        return $response;
    }
}
