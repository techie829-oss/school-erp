<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'tenant']);

        // Apply filters
        if ($request->filled('tenant_id')) {
            $query->byTenant($request->tenant_id);
        }

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        if ($request->filled('model_type')) {
            $query->byModel($request->model_type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', "%{$search}%")
                  ->orWhere('model_type', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $activities = $query->orderBy('created_at', 'desc')->paginate(50);

        // Get filter options
        $tenants = Tenant::all();
        $actions = ActivityLog::distinct()->pluck('action')->sort()->values();
        $modelTypes = ActivityLog::distinct()->pluck('model_type')->filter()->sort()->values();
        $userTypes = ActivityLog::distinct()->pluck('user_type')->sort()->values();

        // Get statistics
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'this_week' => ActivityLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => ActivityLog::whereMonth('created_at', now()->month)->count(),
        ];

        return view('admin.activity-logs.index', compact(
            'activities', 'tenants', 'actions', 'modelTypes', 'userTypes', 'stats'
        ));
    }

    public function show(ActivityLog $activityLog)
    {
        $activityLog->load(['user', 'tenant', 'model']);

        return view('admin.activity-logs.show', compact('activityLog'));
    }

    public function export(Request $request)
    {
        $query = ActivityLog::with(['user', 'tenant']);

        // Apply same filters as index
        if ($request->filled('tenant_id')) {
            $query->byTenant($request->tenant_id);
        }

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        if ($request->filled('model_type')) {
            $query->byModel($request->model_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->orderBy('created_at', 'desc')->get();

        $csvData = "Date,Time,User,Action,Model,Properties,Tenant,IP Address\n";

        foreach ($activities as $activity) {
            $csvData .= sprintf(
                "%s,%s,%s,%s,%s,\"%s\",%s,%s\n",
                $activity->created_at->format('Y-m-d'),
                $activity->created_at->format('H:i:s'),
                $activity->user ? $activity->user->name : 'Unknown',
                $activity->action,
                $activity->model_name,
                json_encode($activity->properties),
                $activity->tenant ? $activity->tenant->name : 'N/A',
                $activity->ip_address ?? 'N/A'
            );
        }

        $filename = 'activity_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function clear(Request $request)
    {
        $query = ActivityLog::query();

        // Apply filters for selective clearing
        if ($request->filled('tenant_id')) {
            $query->byTenant($request->tenant_id);
        }

        if ($request->filled('days')) {
            $query->where('created_at', '<', now()->subDays($request->days));
        }

        $count = $query->count();
        $query->delete();

        return redirect()->route('admin.activity-logs.index')
            ->with('success', "Cleared {$count} activity log entries.");
    }
}
