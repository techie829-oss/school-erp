<?php

namespace App\Http\View\Composers;

use App\Models\CmsSettings;
use App\Services\TenantService;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CmsSettingsComposer
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Bind CMS settings to the view.
     */
    public function compose(View $view)
    {
        $tenant = $this->tenantService->getCurrentTenant(request());

        if ($tenant) {
            $cmsSettings = CmsSettings::forTenant($tenant->id)->first();

            // Get tenant data for fallbacks
            $tenantData = $tenant->data ?? [];

            // Get subdomain for display
            $tenantSubdomain = $tenantData['subdomain'] ?? null;

            // Site name with fallback (use subdomain if no CMS site name)
            $siteName = $cmsSettings->site_name ?? ($tenantSubdomain ? ucfirst($tenantSubdomain) : ($tenantData['name'] ?? 'School ERP'));

            // Site tagline with fallback
            $siteTagline = $cmsSettings->site_tagline ?? $tenantData['description'] ?? 'Management System';

            // Logo with fallback check
            $logo = null;
            if ($cmsSettings && $cmsSettings->logo && Storage::disk('public')->exists($cmsSettings->logo)) {
                $logo = Storage::url($cmsSettings->logo);
            }

            // Favicon with fallback check
            $favicon = null;
            if ($cmsSettings && $cmsSettings->favicon && Storage::disk('public')->exists($cmsSettings->favicon)) {
                $favicon = Storage::url($cmsSettings->favicon);
            }

            // Footer text
            $footerText = $cmsSettings->footer_text ?? null;

            // Contact info
            $contactEmail = $cmsSettings->contact_email ?? null;
            $contactPhone = $cmsSettings->contact_phone ?? null;
            $contactAddress = $cmsSettings->contact_address ?? null;

            // Social media
            $socialMedia = [
                'facebook' => $cmsSettings->social_facebook ?? null,
                'twitter' => $cmsSettings->social_twitter ?? null,
                'instagram' => $cmsSettings->social_instagram ?? null,
                'linkedin' => $cmsSettings->social_linkedin ?? null,
                'youtube' => $cmsSettings->social_youtube ?? null,
            ];
        } else {
            // Defaults when no tenant
            $siteName = 'School ERP';
            $siteTagline = 'Management System';
            $logo = null;
            $favicon = null;
            $footerText = null;
            $contactEmail = null;
            $contactPhone = null;
            $contactAddress = null;
            $socialMedia = [];
        }

        $view->with([
            'cmsSiteName' => $siteName,
            'cmsSiteTagline' => $siteTagline,
            'cmsLogo' => $logo,
            'cmsFavicon' => $favicon,
            'cmsFooterText' => $footerText,
            'cmsContactEmail' => $contactEmail,
            'cmsContactPhone' => $contactPhone,
            'cmsContactAddress' => $contactAddress,
            'cmsSocialMedia' => $socialMedia,
            'tenantSubdomain' => $tenantSubdomain ?? null,
        ]);
    }
}

