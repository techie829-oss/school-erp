<?php

namespace App\Http\Middleware;

use App\Services\TenantContextService;
use App\Services\TenantService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InitializeTenantContext
{
    protected $tenantService;
    protected $contextService;

    public function __construct(TenantService $tenantService, TenantContextService $contextService)
    {
        $this->tenantService = $tenantService;
        $this->contextService = $contextService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Get current tenant from request
            $tenant = $this->tenantService->getCurrentTenant($request);

            if ($tenant) {
                // Check if tenant is active
                if (!$tenant->isActive()) {
                    // If user is logged in, log them out
                    if (auth()->check()) {
                        auth()->logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                    }

                    // Show error page
                    return response()->view('errors.tenant-inactive', [
                        'tenant' => $tenant
                    ], 403);
                }

                // Initialize tenant context
                $this->contextService->initializeContext($tenant);

                // Store tenant in request for later use
                $request->attributes->set('current_tenant', $tenant);
            }

            $response = $next($request);

            return $response;

        } catch (\Exception $e) {
            // Log error but don't break the request
            \Log::warning('Failed to initialize tenant context: ' . $e->getMessage(), [
                'url' => $request->url(),
                'host' => $request->getHost(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $next($request);
        }
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate($request, $response)
    {
        // Reset tenant context after request is complete
        if (TenantContextService::isContextInitialized()) {
            $this->contextService->resetContext();
        }
    }
}
