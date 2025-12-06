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
     * Switch language for the current session
     */
    public function switchLanguage(Request $request, string $language)
    {
        $languages = config('content.pages.languages', ['en' => 'English']);

        // Validate language
        if (!array_key_exists($language, $languages)) {
            return back()->with('error', 'Invalid language selected.');
        }

        // Store in session
        session(['website_language' => $language]);

        // Set app locale
        app()->setLocale($language);

        return back();
    }

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

        // Get current language
        $currentLanguage = $this->getCurrentLanguage($tenantInfo['id']);

        return view('school.home', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
            'currentLanguage' => $currentLanguage,
        ]);
    }

    /**
     * Get current language for tenant
     */
    private function getCurrentLanguage(?string $tenantId): string
    {
        // First check session
        $language = session('website_language');

        if (!$language && $tenantId) {
            try {
                $cmsSettings = \App\Models\CmsSettings::forTenant($tenantId)->first();
                if ($cmsSettings && $cmsSettings->default_language) {
                    $language = $cmsSettings->default_language;
                    session(['website_language' => $language]);
                }
            } catch (\Exception $e) {
                // Silently fail
            }
        }

        if (!$language) {
            $language = config('content.pages.default_language', 'en');
            session(['website_language' => $language]);
        }

        // Ensure language is valid
        $availableLanguages = array_keys(config('content.pages.languages', ['en' => 'English']));
        if (!in_array($language, $availableLanguages)) {
            $language = config('content.pages.default_language', 'en');
            session(['website_language' => $language]);
        }

        // Set app locale
        app()->setLocale($language);

        return $language;
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

        // Get current language
        $currentLanguage = $this->getCurrentLanguage($tenantInfo['id']);

        return view('school.about', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
            'currentLanguage' => $currentLanguage,
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

        // Get current language
        $currentLanguage = $this->getCurrentLanguage($tenantInfo['id']);

        return view('school.programs', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
            'currentLanguage' => $currentLanguage,
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

        // Get current language
        $currentLanguage = $this->getCurrentLanguage($tenantInfo['id']);

        return view('school.admission', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
            'currentLanguage' => $currentLanguage,
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

        // Get current language
        $currentLanguage = $this->getCurrentLanguage($tenantInfo['id']);

        return view('school.contact', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
            'currentLanguage' => $currentLanguage,
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

        // Get current language
        $currentLanguage = $this->getCurrentLanguage($tenantInfo['id']);

        return view('school.facilities', [
            'tenant' => $tenantInfo,
            'tenantSubdomain' => $tenant,
            'colorPalette' => $this->colorPaletteService->getActivePalette($request),
            'currentLanguage' => $currentLanguage,
        ]);
    }

}
