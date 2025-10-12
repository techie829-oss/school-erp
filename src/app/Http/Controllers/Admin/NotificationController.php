<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get unassigned tickets
        $unassignedTickets = Ticket::whereNull('assigned_to')
            ->where('status', '!=', 'closed')
            ->count();

        // Get high priority tickets
        $highPriorityTickets = Ticket::where('priority', 'high')
            ->orWhere('priority', 'urgent')
            ->where('status', '!=', 'closed')
            ->count();

        // Get overdue tickets
        $overdueTickets = Ticket::where('due_date', '<', now())
            ->where('status', '!=', 'closed')
            ->where('status', '!=', 'resolved')
            ->count();

        // Get recent activities
        $recentActivities = ActivityLog::with(['user', 'tenant'])
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get tickets assigned to current user
        $myTickets = Ticket::where('assigned_to', $user->id)
            ->where('status', '!=', 'closed')
            ->with(['creator', 'tenant'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.notifications.index', compact(
            'unassignedTickets',
            'highPriorityTickets',
            'overdueTickets',
            'recentActivities',
            'myTickets'
        ));
    }

    public function getNotifications()
    {
        $user = Auth::user();

        $notifications = [];

        // Unassigned tickets
        $unassignedCount = Ticket::whereNull('assigned_to')
            ->where('status', '!=', 'closed')
            ->count();

        if ($unassignedCount > 0) {
            $notifications[] = [
                'type' => 'warning',
                'title' => 'Unassigned Tickets',
                'message' => "You have {$unassignedCount} unassigned tickets.",
                'count' => $unassignedCount,
                'url' => route('admin.tickets.index', ['assigned_to' => 'unassigned'])
            ];
        }

        // High priority tickets
        $highPriorityCount = Ticket::whereIn('priority', ['high', 'urgent'])
            ->where('status', '!=', 'closed')
            ->count();

        if ($highPriorityCount > 0) {
            $notifications[] = [
                'type' => 'error',
                'title' => 'High Priority Tickets',
                'message' => "You have {$highPriorityCount} high priority tickets.",
                'count' => $highPriorityCount,
                'url' => route('admin.tickets.index', ['priority' => 'high'])
            ];
        }

        // Overdue tickets
        $overdueCount = Ticket::where('due_date', '<', now())
            ->where('status', '!=', 'closed')
            ->where('status', '!=', 'resolved')
            ->count();

        if ($overdueCount > 0) {
            $notifications[] = [
                'type' => 'error',
                'title' => 'Overdue Tickets',
                'message' => "You have {$overdueCount} overdue tickets.",
                'count' => $overdueCount,
                'url' => route('admin.tickets.index', ['overdue' => 'true'])
            ];
        }

        // Tickets assigned to user
        $myTicketsCount = Ticket::where('assigned_to', $user->id)
            ->where('status', '!=', 'closed')
            ->count();

        if ($myTicketsCount > 0) {
            $notifications[] = [
                'type' => 'info',
                'title' => 'My Tickets',
                'message' => "You have {$myTicketsCount} tickets assigned to you.",
                'count' => $myTicketsCount,
                'url' => route('admin.tickets.index', ['assigned_to' => $user->id])
            ];
        }

        return response()->json($notifications);
    }

    public function markAsRead(Request $request)
    {
        // This could be expanded to track read notifications
        return response()->json(['success' => true]);
    }
}
