<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of subjects
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $query = Subject::forTenant($tenant->id);

        // Search
        if ($request->has('search') && $request->search) {
            $query->where('subject_name', 'like', '%' . $request->search . '%')
                  ->orWhere('subject_code', 'like', '%' . $request->search . '%');
        }

        // Filter by type
        if ($request->has('subject_type') && $request->subject_type) {
            $query->where('subject_type', $request->subject_type);
        }

        // Filter by status
        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        $subjects = $query->orderBy('subject_name')->paginate(20)->withQueryString();

        return view('tenant.admin.subjects.index', compact('subjects', 'tenant'));
    }

    /**
     * Show the form for creating a new subject
     */
    public function create(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        return view('tenant.admin.subjects.create', compact('tenant'));
    }

    /**
     * Store a newly created subject in storage
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'subject_name' => 'required|string|max:100|unique:subjects,subject_name,NULL,id,tenant_id,' . $tenant->id,
            'subject_code' => 'nullable|string|max:20|unique:subjects,subject_code,NULL,id,tenant_id,' . $tenant->id,
            'subject_type' => 'required|in:core,elective,optional,extra_curricular',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            Subject::create([
                'tenant_id' => $tenant->id,
                'subject_name' => $request->subject_name,
                'subject_code' => $request->subject_code,
                'subject_type' => $request->subject_type,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? $request->is_active : true,
            ]);

            return redirect()
                ->route('tenant.admin.subjects.index', ['subdomain' => $tenant->id])
                ->with('success', 'Subject created successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to create subject: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified subject
     */
    public function show(Request $request, $subjectId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $subject = Subject::with(['teachers'])->findOrFail($subjectId);

        // Ensure subject belongs to tenant
        if ($subject->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        return view('tenant.admin.subjects.show', compact('subject', 'tenant'));
    }

    /**
     * Show the form for editing the specified subject
     */
    public function edit(Request $request, $subjectId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $subject = Subject::findOrFail($subjectId);

        // Ensure subject belongs to tenant
        if ($subject->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        return view('tenant.admin.subjects.edit', compact('subject', 'tenant'));
    }

    /**
     * Update the specified subject in storage
     */
    public function update(Request $request, $subjectId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $subject = Subject::findOrFail($subjectId);

        // Ensure subject belongs to tenant
        if ($subject->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'subject_name' => 'required|string|max:100|unique:subjects,subject_name,' . $subject->id . ',id,tenant_id,' . $tenant->id,
            'subject_code' => 'nullable|string|max:20|unique:subjects,subject_code,' . $subject->id . ',id,tenant_id,' . $tenant->id,
            'subject_type' => 'required|in:core,elective,optional,extra_curricular',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $subject->update([
                'subject_name' => $request->subject_name,
                'subject_code' => $request->subject_code,
                'subject_type' => $request->subject_type,
                'description' => $request->description,
                'is_active' => $request->has('is_active') ? $request->is_active : $subject->is_active,
            ]);

            return redirect()
                ->route('tenant.admin.subjects.index', ['subdomain' => $tenant->id])
                ->with('success', 'Subject updated successfully!');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to update subject: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified subject from storage
     */
    public function destroy(Request $request, $subjectId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $subject = Subject::findOrFail($subjectId);

        // Ensure subject belongs to tenant
        if ($subject->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        try {
            $subject->delete();

            return redirect()
                ->route('tenant.admin.subjects.index', ['subdomain' => $tenant->id])
                ->with('success', 'Subject deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete subject: ' . $e->getMessage());
        }
    }
}

