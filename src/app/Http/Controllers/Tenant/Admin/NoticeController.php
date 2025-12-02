<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\NoticeAttachment;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class NoticeController extends Controller
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

        $query = Notice::forTenant($tenant->id)->with(['creator', 'attachments']);

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('notice_type') && $request->notice_type) {
            $query->byType($request->notice_type);
        }

        if ($request->has('priority') && $request->priority) {
            $query->byPriority($request->priority);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        } else {
            // Default: show published and draft, exclude expired/archived
            $query->whereIn('status', ['draft', 'published']);
        }

        if ($request->has('target_audience') && $request->target_audience) {
            $query->forAudience($request->target_audience);
        }

        $notices = $query->latest('created_at')->paginate(20)->withQueryString();

        return view('tenant.admin.notices.index', compact('notices', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        return view('tenant.admin.notices.create', compact('tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'notice_type' => 'required|in:general,academic,event,announcement,circular',
            'priority' => 'required|in:low,normal,high,urgent',
            'target_audience' => 'required|in:all,students,teachers,staff,parents',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:draft,published,expired,archived',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $notice = Notice::create([
                'tenant_id' => $tenant->id,
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'notice_type' => $request->input('notice_type'),
                'priority' => $request->input('priority'),
                'target_audience' => $request->input('target_audience'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'status' => $request->input('status'),
                'created_by' => auth()->id(),
            ]);

            // Handle file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('notices/' . $tenant->id, 'public');
                    NoticeAttachment::create([
                        'notice_id' => $notice->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getMimeType(),
                    ]);
                }
            }

            DB::commit();

            return redirect(url('/admin/notices'))->with('success', 'Notice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create notice: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $notice = Notice::forTenant($tenant->id)
            ->with(['creator', 'attachments', 'reads.user'])
            ->findOrFail($id);

        // Mark as read for current user
        if (auth()->check() && !$notice->isReadBy(auth()->id())) {
            $notice->reads()->create([
                'user_id' => auth()->id(),
                'read_at' => now(),
            ]);
        }

        return view('tenant.admin.notices.show', compact('notice', 'tenant'));
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $notice = Notice::forTenant($tenant->id)
            ->with('attachments')
            ->findOrFail($id);

        return view('tenant.admin.notices.edit', compact('notice', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $notice = Notice::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'notice_type' => 'required|in:general,academic,event,announcement,circular',
            'priority' => 'required|in:low,normal,high,urgent',
            'target_audience' => 'required|in:all,students,teachers,staff,parents',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:draft,published,expired,archived',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $notice->update([
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'notice_type' => $request->input('notice_type'),
                'priority' => $request->input('priority'),
                'target_audience' => $request->input('target_audience'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'status' => $request->input('status'),
            ]);

            // Handle new file attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('notices/' . $tenant->id, 'public');

                    NoticeAttachment::create([
                        'notice_id' => $notice->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_size' => $file->getSize(),
                        'file_type' => $file->getMimeType(),
                    ]);
                }
            }

            // Handle attachment deletion
            if ($request->has('delete_attachments')) {
                foreach ($request->delete_attachments as $attachmentId) {
                    $attachment = NoticeAttachment::find($attachmentId);
                    if ($attachment && $attachment->notice_id == $notice->id) {
                        Storage::disk('public')->delete($attachment->file_path);
                        $attachment->delete();
                    }
                }
            }

            DB::commit();

            return redirect(url('/admin/notices'))->with('success', 'Notice updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update notice: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $notice = Notice::forTenant($tenant->id)->with('attachments')->findOrFail($id);

        DB::beginTransaction();
        try {
            // Delete attachments
            foreach ($notice->attachments as $attachment) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            $notice->delete();

            DB::commit();

            return redirect(url('/admin/notices'))->with('success', 'Notice deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete notice: ' . $e->getMessage());
        }
    }

    public function markAsRead(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $notice = Notice::forTenant($tenant->id)->findOrFail($id);

        if (auth()->check() && !$notice->isReadBy(auth()->id())) {
            $notice->reads()->create([
                'user_id' => auth()->id(),
                'read_at' => now(),
            ]);
        }

        return response()->json(['success' => true]);
    }
}

