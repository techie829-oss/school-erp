<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventCategoryController extends Controller
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
        $categories = EventCategory::forTenant($tenant->id)
            ->withCount('events')
            ->orderBy('name')
            ->get();

        return view('tenant.admin.events.categories.index', compact('categories', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        return view('tenant.admin.events.categories.create', compact('tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_hex' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $color = $request->input('color_hex') ?: $request->input('color');

        EventCategory::create([
            'tenant_id' => $tenant->id,
            'name' => $request->input('name'),
            'color' => $color,
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return redirect(url('/admin/events/categories'))->with('success', 'Category created successfully.');
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $category = EventCategory::forTenant($tenant->id)->findOrFail($id);

        return view('tenant.admin.events.categories.edit', compact('category', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $category = EventCategory::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'color_hex' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $color = $request->input('color_hex') ?: $request->input('color');

        $category->update([
            'name' => $request->input('name'),
            'color' => $color,
            'description' => $request->input('description'),
            'status' => $request->input('status'),
        ]);

        return redirect(url('/admin/events/categories'))->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $category = EventCategory::forTenant($tenant->id)->findOrFail($id);

        if ($category->events()->count() > 0) {
            return back()->with('error', 'Cannot delete category with associated events.');
        }

        $category->delete();

        return redirect(url('/admin/events/categories'))->with('success', 'Category deleted successfully.');
    }
}

