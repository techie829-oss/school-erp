<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsSettings;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CmsSettingsController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    protected function getTenant(Request $request)
    {
        $tenant = $request->attributes->get('current_tenant');
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }
        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        return $tenant;
    }

    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);
        $settings = CmsSettings::forTenant($tenant->id)->first();

        return view('tenant.admin.cms.settings.index', compact('tenant', 'settings'));
    }

    public function general(Request $request)
    {
        $tenant = $this->getTenant($request);
        $settings = CmsSettings::forTenant($tenant->id)->first();

        return view('tenant.admin.cms.settings.general', compact('tenant', 'settings'));
    }

    public function updateGeneral(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'site_name' => 'nullable|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:512',
            'footer_text' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_address' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $settings = CmsSettings::forTenant($tenant->id)->firstOrNew(['tenant_id' => $tenant->id]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($settings->logo) {
                Storage::disk('public')->delete($settings->logo);
            }
            $settings->logo = $request->file('logo')->store('cms/logos/' . $tenant->id, 'public');
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            if ($settings->favicon) {
                Storage::disk('public')->delete($settings->favicon);
            }
            $settings->favicon = $request->file('favicon')->store('cms/favicons/' . $tenant->id, 'public');
        }

        $settings->fill($request->only([
            'site_name',
            'site_tagline',
            'footer_text',
            'contact_email',
            'contact_phone',
            'contact_address',
        ]));

        $settings->save();

        return redirect(url('/admin/cms/settings/general'))->with('success', 'General settings updated successfully.');
    }

    public function social(Request $request)
    {
        $tenant = $this->getTenant($request);
        $settings = CmsSettings::forTenant($tenant->id)->first();

        return view('tenant.admin.cms.settings.social', compact('tenant', 'settings'));
    }

    public function updateSocial(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'social_facebook' => 'nullable|url|max:255',
            'social_twitter' => 'nullable|url|max:255',
            'social_instagram' => 'nullable|url|max:255',
            'social_linkedin' => 'nullable|url|max:255',
            'social_youtube' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $settings = CmsSettings::forTenant($tenant->id)->firstOrNew(['tenant_id' => $tenant->id]);
        $settings->fill($request->only([
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_linkedin',
            'social_youtube',
        ]));
        $settings->save();

        return redirect(url('/admin/cms/settings/social'))->with('success', 'Social media settings updated successfully.');
    }
}

