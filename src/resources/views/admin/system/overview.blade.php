@extends('layouts.admin')

@section('title', 'System Overview')
@section('page-title', 'System Overview')
@section('page-description', 'Monitor system health and performance')

@section('content')
<!-- System Status Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Server Status -->
    <div class="stat-card p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Server Status</p>
                <p class="text-2xl font-bold text-green-600">Online</p>
            </div>
        </div>
    </div>

    <!-- Database Status -->
    <div class="stat-card p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Database</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['db_status'] }}</p>
            </div>
        </div>
    </div>

    <!-- Memory Usage -->
    <div class="stat-card p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Memory Usage</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $stats['memory_usage']['percentage'] }}%</p>
                <p class="text-xs text-gray-500">{{ $stats['memory_usage']['used'] }} / {{ $stats['memory_usage']['limit'] }}</p>
            </div>
        </div>
    </div>

    <!-- Disk Usage -->
    <div class="stat-card p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Disk Usage</p>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['disk_usage']['percentage'] }}%</p>
                <p class="text-xs text-gray-500">{{ $stats['disk_usage']['used'] }} / {{ $stats['disk_usage']['total'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- System Information -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Application Info -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Application Information</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Laravel Version</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['laravel_version'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">PHP Version</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['php_version'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Environment</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['environment'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Debug Mode</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['debug_mode'] ? 'Enabled' : 'Disabled' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Cache Driver</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['cache_driver'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Queue Driver</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['queue_driver'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Total Tenants</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['total_tenants'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Active Tenants</span>
                <span class="text-sm font-medium text-green-600">{{ $stats['active_tenants'] }}</span>
            </div>
        </div>
    </div>

    <!-- Database Info -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Database Information</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Driver</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['db_driver'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Host</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['db_host'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Database</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['db_name'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Total Tables</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['table_count'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Connection Status</span>
                <span class="text-sm font-medium {{ $stats['db_status'] === 'Connected' ? 'text-green-600' : 'text-red-600' }}">{{ $stats['db_status'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Total Users</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['total_users'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Active Users</span>
                <span class="text-sm font-medium text-green-600">{{ $stats['active_users'] }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Storage Information -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Storage Path</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Total Size</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['storage_path_size'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Cache Size</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['cache_size'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Logs Size</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['logs_size'] }}</span>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Server Information</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">CPU Load</span>
                <span class="text-sm font-medium text-gray-900">{{ $stats['cpu_usage'] }}{{ is_numeric($stats['cpu_usage']) ? '%' : '' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Server Time</span>
                <span class="text-sm font-medium text-gray-900">{{ now()->format('Y-m-d H:i:s') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Timezone</span>
                <span class="text-sm font-medium text-gray-900">{{ config('app.timezone') }}</span>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-2">
            <form action="{{ route('admin.system.cache.clear') }}" method="POST" class="inline-block w-full">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 text-sm bg-blue-50 text-blue-700 rounded hover:bg-blue-100 transition-colors">
                    🔄 Clear Cache
                </button>
            </form>
            <form action="{{ route('admin.system.route.clear') }}" method="POST" class="inline-block w-full">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 text-sm bg-green-50 text-green-700 rounded hover:bg-green-100 transition-colors">
                    🛣️ Clear Routes
                </button>
            </form>
            <form action="{{ route('admin.system.view.clear') }}" method="POST" class="inline-block w-full">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 text-sm bg-purple-50 text-purple-700 rounded hover:bg-purple-100 transition-colors">
                    👁️ Clear Views
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Recent Logs -->
<div class="card p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Recent System Logs (Last 20)</h3>
        <a href="{{ route('admin.system.logs') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
            View All Logs →
        </a>
    </div>

    <div class="space-y-3 max-h-96 overflow-y-auto">
        @forelse($logs as $log)
        <div class="flex items-start border-b border-gray-100 pb-3">
            <div class="w-2 h-2 rounded-full mr-3 mt-1.5 flex-shrink-0
                {{ $log['level'] === 'ERROR' ? 'bg-red-500' :
                   ($log['level'] === 'WARNING' ? 'bg-yellow-500' :
                   ($log['level'] === 'INFO' ? 'bg-blue-500' : 'bg-green-500')) }}">
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                        {{ $log['level'] === 'ERROR' ? 'bg-red-100 text-red-800' :
                           ($log['level'] === 'WARNING' ? 'bg-yellow-100 text-yellow-800' :
                           ($log['level'] === 'INFO' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800')) }}">
                        {{ $log['level'] }}
                    </span>
                    <span class="text-xs text-gray-500">{{ $log['timestamp'] }}</span>
                </div>
                <p class="text-sm text-gray-900 break-words">{{ Str::limit($log['message'], 150) }}</p>
                @if(strlen($log['message']) > 150)
                <button onclick="this.nextElementSibling.classList.toggle('hidden')" class="text-xs text-primary-600 hover:text-primary-700 mt-1">
                    Show more
                </button>
                <pre class="hidden text-xs text-gray-600 mt-2 p-2 bg-gray-50 rounded overflow-x-auto">{{ $log['full_message'] }}</pre>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p>No log entries found</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
