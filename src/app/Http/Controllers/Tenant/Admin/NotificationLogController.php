<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\TenantService;
use Illuminate\Http\Request;

class NotificationLogController extends Controller
{
    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Show notification (SMS/Email) logs for current tenant.
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $query = ActivityLog::byTenant($tenant->id)
            ->where('action', 'like', 'notification_%');

        // Optional quick filters
        if ($request->filled('channel')) {
            $query->where('properties->channel', $request->channel);
        }

        if ($request->filled('type')) {
            $query->where('properties->type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('action', 'notification_' . $request->status);
        }

        $logs = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        // Distinct filter values for quick dropdowns
        $channels = ActivityLog::byTenant($tenant->id)
            ->where('action', 'like', 'notification_%')
            ->distinct()
            ->pluck('properties->channel')
            ->filter()
            ->values();

        $types = ActivityLog::byTenant($tenant->id)
            ->where('action', 'like', 'notification_%')
            ->distinct()
            ->pluck('properties->type')
            ->filter()
            ->values();

        return view('tenant.admin.notifications.logs', compact(
            'tenant',
            'logs',
            'channels',
            'types'
        ));
    }
}


