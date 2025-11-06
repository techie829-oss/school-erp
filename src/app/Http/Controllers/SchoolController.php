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
    public function home(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        // Get tenant from route parameter
        $tenant = $request->route('tenant');

        // Set the tenant context for this request
        $request->merge(['tenant_subdomain' => $tenant]);

        $tenantInfo = $this->tenantService->getTenantInfo($request);

        // If no tenant found, redirect to landing page
        if (!$tenantInfo['id']) {
            return redirect()->away('http://' . config('all.domains.primary'));
        }

        return view('school.home', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
        ]);
    }

    /**
     * Show the school's about page.
     */
    public function about(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        // Get tenant from route parameter
        $tenant = $request->route('tenant');

        // Set the tenant context for this request
        $request->merge(['tenant_subdomain' => $tenant]);

        $tenantInfo = $this->tenantService->getTenantInfo($request);

        // If no tenant found, redirect to landing page
        if (!$tenantInfo['id']) {
            return redirect()->away('http://' . config('all.domains.primary'));
        }

        return view('school.about', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
        ]);
    }

    /**
     * Show the school's programs/curriculum page.
     */
    public function programs(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        // Get tenant from route parameter
        $tenant = $request->route('tenant');

        // Set the tenant context for this request
        $request->merge(['tenant_subdomain' => $tenant]);

        $tenantInfo = $this->tenantService->getTenantInfo($request);

        // If no tenant found, redirect to landing page
        if (!$tenantInfo['id']) {
            return redirect()->away('http://' . config('all.domains.primary'));
        }

        return view('school.programs', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
        ]);
    }

    /**
     * Show the school's admission page.
     */
    public function admission(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        // Get tenant from route parameter
        $tenant = $request->route('tenant');

        // Set the tenant context for this request
        $request->merge(['tenant_subdomain' => $tenant]);

        $tenantInfo = $this->tenantService->getTenantInfo($request);

        // If no tenant found, redirect to landing page
        if (!$tenantInfo['id']) {
            return redirect()->away('http://' . config('all.domains.primary'));
        }

        return view('school.admission', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
        ]);
    }

    /**
     * Show the school's contact page.
     */
    public function contact(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        // Get tenant from route parameter
        $tenant = $request->route('tenant');

        // Set the tenant context for this request
        $request->merge(['tenant_subdomain' => $tenant]);

        $tenantInfo = $this->tenantService->getTenantInfo($request);

        // If no tenant found, redirect to landing page
        if (!$tenantInfo['id']) {
            return redirect()->away('http://' . config('all.domains.primary'));
        }

        return view('school.contact', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
        ]);
    }

    /**
     * Show the school's facilities page.
     */
    public function facilities(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        // Get tenant from route parameter
        $tenant = $request->route('tenant');

        // Set the tenant context for this request
        $request->merge(['tenant_subdomain' => $tenant]);

        $tenantInfo = $this->tenantService->getTenantInfo($request);

        // If no tenant found, redirect to landing page
        if (!$tenantInfo['id']) {
            return redirect()->away('http://' . config('all.domains.primary'));
        }

        return view('school.facilities', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
        ]);
    }
}
