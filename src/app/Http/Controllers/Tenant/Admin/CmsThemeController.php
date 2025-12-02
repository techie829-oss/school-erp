<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsThemeSettings;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CmsThemeController extends Controller
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
        $theme = CmsThemeSettings::forTenant($tenant->id)->first();
        
        return view('tenant.admin.cms.settings.theme', compact('tenant', 'theme'));
    }

    public function update(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'primary_color_50' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_color_100' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_color_500' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_color_600' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_color_700' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_color_900' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color_50' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color_100' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color_500' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color_600' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color_700' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color_900' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color_50' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color_100' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color_500' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color_600' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color_700' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'accent_color_900' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'success_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'warning_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'error_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'info_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'custom_css' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $theme = CmsThemeSettings::forTenant($tenant->id)->firstOrNew(['tenant_id' => $tenant->id]);
        $theme->fill($request->all());
        $theme->save();

        // Clear cache if using caching
        cache()->forget("cms_theme_tenant_{$tenant->id}");

        return redirect(url('/admin/cms/settings/theme'))->with('success', 'Theme settings updated successfully.');
    }
}

