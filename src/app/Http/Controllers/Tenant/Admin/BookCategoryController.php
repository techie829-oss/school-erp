<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookCategory;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookCategoryController extends Controller
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

        $query = BookCategory::forTenant($tenant->id);

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $categories = $query->withCount('books')->orderBy('name')->paginate(20)->withQueryString();

        return view('tenant.admin.library.categories.index', compact('categories', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        return view('tenant.admin.library.categories.create', compact('tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:book_categories,name,NULL,id,tenant_id,' . $tenant->id,
            'code' => 'nullable|string|max:20|unique:book_categories,code,NULL,id,tenant_id,' . $tenant->id,
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        BookCategory::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect(url('/admin/library/categories'))->with('success', 'Category created successfully.');
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $category = BookCategory::forTenant($tenant->id)->findOrFail($id);
        return view('tenant.admin.library.categories.edit', compact('category', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $category = BookCategory::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:book_categories,name,' . $id . ',id,tenant_id,' . $tenant->id,
            'code' => 'nullable|string|max:20|unique:book_categories,code,' . $id . ',id,tenant_id,' . $tenant->id,
            'description' => 'nullable|string|max:500',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $category->update([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect(url('/admin/library/categories'))->with('success', 'Category updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $category = BookCategory::forTenant($tenant->id)->withCount('books')->findOrFail($id);

        if ($category->books_count > 0) {
            return back()->with('error', 'Cannot delete category with existing books.');
        }

        $category->delete();

        return redirect(url('/admin/library/categories'))->with('success', 'Category deleted successfully.');
    }
}
