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
                <p class="text-2xl font-bold text-blue-600">Connected</p>
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
                <p class="text-2xl font-bold text-yellow-600">68%</p>
            </div>
        </div>
    </div>

    <!-- CPU Usage -->
    <div class="stat-card p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">CPU Usage</p>
                <p class="text-2xl font-bold text-purple-600">42%</p>
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
                <span class="text-sm font-medium text-gray-900">{{ app()->version() }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">PHP Version</span>
                <span class="text-sm font-medium text-gray-900">{{ PHP_VERSION }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Environment</span>
                <span class="text-sm font-medium text-gray-900">{{ app()->environment() }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Debug Mode</span>
                <span class="text-sm font-medium text-gray-900">{{ config('app.debug') ? 'Enabled' : 'Disabled' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Cache Driver</span>
                <span class="text-sm font-medium text-gray-900">{{ config('cache.default') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Queue Driver</span>
                <span class="text-sm font-medium text-gray-900">{{ config('queue.default') }}</span>
            </div>
        </div>
    </div>

    <!-- Database Info -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Database Information</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Driver</span>
                <span class="text-sm font-medium text-gray-900">{{ config('database.default') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Host</span>
                <span class="text-sm font-medium text-gray-900">{{ config('database.connections.mysql.host') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Database</span>
                <span class="text-sm font-medium text-gray-900">{{ config('database.connections.mysql.database') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Total Tables</span>
                <span class="text-sm font-medium text-gray-900">{{ \DB::select('SHOW TABLES')[0]->{'Tables_in_' . config('database.connections.mysql.database')} ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Connection Status</span>
                <span class="text-sm font-medium text-green-600">Connected</span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Logs -->
<div class="card p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Recent System Logs</h3>
        <a href="#" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View All Logs</a>
    </div>

    <div class="space-y-4">
        <div class="flex items-center">
            <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
            <div class="flex-1">
                <p class="text-sm text-gray-900">Application started successfully</p>
                <p class="text-xs text-gray-500">{{ now()->format('M d, Y H:i:s') }}</p>
            </div>
        </div>

        <div class="flex items-center">
            <div class="w-2 h-2 bg-blue-400 rounded-full mr-3"></div>
            <div class="flex-1">
                <p class="text-sm text-gray-900">Database connection established</p>
                <p class="text-xs text-gray-500">{{ now()->subMinutes(2)->format('M d, Y H:i:s') }}</p>
            </div>
        </div>

        <div class="flex items-center">
            <div class="w-2 h-2 bg-yellow-400 rounded-full mr-3"></div>
            <div class="flex-1">
                <p class="text-sm text-gray-900">Cache cleared</p>
                <p class="text-xs text-gray-500">{{ now()->subMinutes(5)->format('M d, Y H:i:s') }}</p>
            </div>
        </div>

        <div class="flex items-center">
            <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
            <div class="flex-1">
                <p class="text-sm text-gray-900">Admin user logged in</p>
                <p class="text-xs text-gray-500">{{ now()->subMinutes(10)->format('M d, Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
