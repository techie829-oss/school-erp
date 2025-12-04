<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Services\TenantService;
use Illuminate\Http\Request;

class CmsPageController extends Controller
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
        
        // Static pages list for now
        $pages = [
            ['id' => 1, 'title' => 'Home', 'slug' => 'home', 'template' => 'home', 'status' => 'published', 'updated_at' => now()],
            ['id' => 2, 'title' => 'About Us', 'slug' => 'about', 'template' => 'about', 'status' => 'published', 'updated_at' => now()],
            ['id' => 3, 'title' => 'Programs', 'slug' => 'programs', 'template' => 'programs', 'status' => 'published', 'updated_at' => now()],
            ['id' => 4, 'title' => 'Facilities', 'slug' => 'facilities', 'template' => 'facilities', 'status' => 'published', 'updated_at' => now()],
            ['id' => 5, 'title' => 'Admission', 'slug' => 'admission', 'template' => 'admission', 'status' => 'published', 'updated_at' => now()],
            ['id' => 6, 'title' => 'Contact', 'slug' => 'contact', 'template' => 'contact', 'status' => 'published', 'updated_at' => now()],
        ];

        return view('tenant.admin.cms.pages.index', compact('tenant', 'pages'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        return view('tenant.admin.cms.pages.create', compact('tenant'));
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        
        // Static page data for now
        $page = [
            'id' => $id,
            'title' => 'Home',
            'slug' => 'home',
            'template' => 'home',
            'content' => '',
            'excerpt' => '',
            'status' => 'published',
            'meta_title' => '',
            'meta_description' => '',
            'meta_keywords' => '',
        ];

        return view('tenant.admin.cms.pages.edit', compact('tenant', 'page'));
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        
        // Static page data for now
        $page = [
            'id' => $id,
            'title' => 'Home',
            'slug' => 'home',
            'template' => 'home',
            'content' => '',
            'status' => 'published',
        ];

        return view('tenant.admin.cms.pages.show', compact('tenant', 'page'));
    }
}

