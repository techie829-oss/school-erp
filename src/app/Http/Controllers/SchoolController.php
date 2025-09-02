<?php

namespace App\Http\Controllers;

use App\Services\TenantService;
use App\Services\ColorPaletteService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function __construct(
        private TenantService $tenantService,
        private ColorPaletteService $colorPaletteService
    ) {}

    /**
     * Show the school's main landing page.
     */
    public function home(Request $request, string $tenant): View
    {
        // Set the tenant context for this request
        $request->merge(['tenant_subdomain' => $tenant]);

        $tenantInfo = $this->tenantService->getTenantInfo($request);

        return view('school.home', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
        ]);
    }

    /**
     * Show the school's about page.
     */
    public function about(Request $request, string $tenant): View
    {
        // Set the tenant context for this request
        $request->merge(['tenant_subdomain' => $tenant]);

        $tenantInfo = $this->tenantService->getTenantInfo($request);

        return view('school.about', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
        ]);
    }

    /**
     * Show the school's programs/curriculum page.
     */
    public function programs(Request $request, string $tenant): View
    {
        // Set the tenant context for this request
        $request->merge(['tenant_subdomain' => $tenant]);

        $tenantInfo = $this->tenantService->getTenantInfo($request);

        return view('school.programs', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
        ]);
    }

    /**
     * Show the school's admission page.
     */
    public function admission(Request $request, string $tenant): View
    {
        // Set the tenant context for this request
        $request->merge(['tenant_subdomain' => $tenant]);

        $tenantInfo = $this->tenantService->getTenantInfo($request);

        return view('school.admission', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
        ]);
    }

    /**
     * Show the school's contact page.
     */
    public function contact(Request $request, string $tenant): View
    {
        // Set the tenant context for this request
        $request->merge(['tenant_subdomain' => $tenant]);

        $tenantInfo = $this->tenantService->getTenantInfo($request);

        return view('school.contact', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
        ]);
    }

    /**
     * Show the school's facilities page.
     */
    public function facilities(Request $request, string $tenant): View
    {
        // Set the tenant context for this request
        $request->merge(['tenant_subdomain' => $tenant]);

        $tenantInfo = $this->tenantService->getTenantInfo($request);

        return view('school.facilities', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
        ]);
    }
}
