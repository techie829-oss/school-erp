<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Illuminate\Http\Request;

class CmsController extends Controller
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

        // Get statistics for CMS dashboard
        $stats = [
            'pages' => 0, // Will be implemented with pages
            'posts' => 0, // Will be implemented with blog
            'media' => 0, // Will be implemented with media library
            'menus' => 0, // Will be implemented with menus
        ];

        return view('tenant.admin.cms.index', compact('tenant', 'stats'));
    }
}

