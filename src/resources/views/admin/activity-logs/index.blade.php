@extends('layouts.admin')

@section('title', 'Activity Logs')
@section('page-title', 'Activity Logs')
@section('page-description', 'View system activity and audit logs with tenant-wise filtering')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Activity Logs</h2>
            <p class="mt-1 text-sm text-gray-600">Monitor system activities and user actions</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.activity-logs.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-600">Total Activities</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-600">Today</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['today']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-600">This Week</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_week']) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="text-sm text-gray-600">This Month</div>
            <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['this_month']) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tenant</label>
                <select name="tenant_id" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Tenants</option>
                    @foreach($tenants as $tenant)
                        <option value="{{ $tenant->id }}" {{ request('tenant_id') == $tenant->id ? 'selected' : '' }}>
                            {{ $tenant->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User Type</label>
                <select name="user_type" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach($userTypes as $type)
                        <option value="{{ $type }}" {{ request('user_type') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                <select name="action" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $action)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Model Type</label>
                <select name="model_type" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Models</option>
                    @foreach($modelTypes as $model)
                        <option value="{{ $model }}" {{ request('model_type') == $model ? 'selected' : '' }}>
                            {{ class_basename($model) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Activity Logs List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($activities->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tenant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($activities as $activity)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>{{ $activity->created_at->format('M j, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $activity->created_at->format('H:i:s') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $activity->user ? $activity->user->name : 'System' }}</div>
                                    <div class="text-xs text-gray-500">{{ $activity->user_type }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $activity->action_badge }}">
                                        {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $activity->model_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $activity->tenant ? $activity->tenant->name : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $activity->ip_address ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.activity-logs.show', $activity) }}" class="text-blue-600 hover:text-blue-900">View Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $activities->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <p class="text-lg font-medium text-gray-900">No activity logs found</p>
                <p class="text-sm text-gray-500 mt-1">Activity will appear here as users interact with the system</p>
            </div>
        @endif
    </div>
</div>
@endsection
