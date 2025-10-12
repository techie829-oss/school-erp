<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\ActivityLog;
use App\Models\AdminUser;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with(['creator', 'assignee', 'tenant']);

        // Apply filters
        if ($request->filled('tenant_id')) {
            $query->byTenant($request->tenant_id);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('priority')) {
            $query->byPriority($request->priority);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('assigned_to')) {
            $query->assignedTo($request->assigned_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get filter options
        $tenants = Tenant::all();
        $adminUsers = AdminUser::all();
        $statuses = ['open', 'in_progress', 'resolved', 'closed'];
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $types = ['bug', 'feature_request', 'support', 'general'];

        return view('admin.tickets.index', compact(
            'tickets', 'tenants', 'adminUsers', 'statuses', 'priorities', 'types'
        ));
    }

    public function create()
    {
        $tenants = Tenant::all();
        $adminUsers = AdminUser::all();
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $types = ['bug', 'feature_request', 'support', 'general'];

        return view('admin.tickets.create', compact('tenants', 'adminUsers', 'priorities', 'types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'type' => 'required|in:bug,feature_request,support,general',
            'tenant_id' => 'nullable|exists:tenants,id',
            'assigned_to' => 'nullable|exists:admin_users,id',
            'due_date' => 'nullable|date|after:now'
        ]);

        $validated['created_by'] = Auth::id();

        $ticket = Ticket::create($validated);

        // Log activity
        ActivityLog::log('created', $ticket, [
            'title' => $ticket->title,
            'priority' => $ticket->priority,
            'type' => $ticket->type
        ], $ticket->tenant_id);

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket created successfully!');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['creator', 'assignee', 'tenant', 'comments.user']);
        $adminUsers = AdminUser::all();

        return view('admin.tickets.show', compact('ticket', 'adminUsers'));
    }

    public function edit(Ticket $ticket)
    {
        $tenants = Tenant::all();
        $adminUsers = AdminUser::all();
        $priorities = ['low', 'medium', 'high', 'urgent'];
        $types = ['bug', 'feature_request', 'support', 'general'];
        $statuses = ['open', 'in_progress', 'resolved', 'closed'];

        return view('admin.tickets.edit', compact('ticket', 'tenants', 'adminUsers', 'priorities', 'types', 'statuses'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:open,in_progress,resolved,closed',
            'priority' => 'required|in:low,medium,high,urgent',
            'type' => 'required|in:bug,feature_request,support,general',
            'tenant_id' => 'nullable|exists:tenants,id',
            'assigned_to' => 'nullable|exists:admin_users,id',
            'due_date' => 'nullable|date'
        ]);

        $oldData = $ticket->toArray();
        $ticket->update($validated);

        // Log activity
        ActivityLog::log('updated', $ticket, [
            'changes' => array_diff_assoc($validated, $oldData),
            'old_data' => $oldData
        ], $ticket->tenant_id);

        // Set resolved_at if status changed to resolved
        if ($validated['status'] === 'resolved' && $oldData['status'] !== 'resolved') {
            $ticket->update(['resolved_at' => now()]);
        }

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully!');
    }

    public function destroy(Ticket $ticket)
    {
        // Log activity before deletion
        ActivityLog::log('deleted', null, [
            'ticket_number' => $ticket->ticket_number,
            'title' => $ticket->title
        ], $ticket->tenant_id);

        $ticket->delete();

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket deleted successfully!');
    }

    public function addComment(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'comment' => 'required|string',
            'is_internal' => 'boolean'
        ]);

        $validated['user_id'] = Auth::id();
        $validated['user_type'] = 'admin';

        $comment = $ticket->comments()->create($validated);

        // Log activity
        ActivityLog::log('commented', $ticket, [
            'comment_id' => $comment->id,
            'is_internal' => $comment->is_internal
        ], $ticket->tenant_id);

        return redirect()->route('admin.tickets.show', $ticket)
            ->with('success', 'Comment added successfully!');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);

        $oldStatus = $ticket->status;
        $ticket->update($validated);

        // Set resolved_at if status changed to resolved
        if ($validated['status'] === 'resolved' && $oldStatus !== 'resolved') {
            $ticket->update(['resolved_at' => now()]);
        }

        // Log activity
        ActivityLog::log('status_changed', $ticket, [
            'old_status' => $oldStatus,
            'new_status' => $validated['status']
        ], $ticket->tenant_id);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully!'
        ]);
    }
}
