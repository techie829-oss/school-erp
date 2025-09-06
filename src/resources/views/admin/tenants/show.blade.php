@extends('layouts.admin')

@section('title', 'Tenant Dashboard')
@section('page-title', 'Tenant Dashboard')
@section('page-description')
Comprehensive overview and management for {{ $tenant->data['name'] ?? 'Unnamed Tenant' }}
@endsection

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Main Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $tenant->data['name'] ?? 'Unnamed Tenant' }}</h1>
                    <p class="mt-2 text-lg text-gray-600">{{ ucfirst($tenant->data['type'] ?? 'Institution') }} • {{ $tenant->data['full_domain'] ?? $tenant->id . '.' . config('all.domains.primary') }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.tenants.edit', $tenant) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Tenant
                    </a>
                    <a href="{{ route('admin.tenants.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>


        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Database Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg {{ ($tenant->data['database_strategy'] ?? 'shared') === 'separate' ? 'bg-blue-100' : 'bg-gray-100' }}">
                        <svg class="w-6 h-6 {{ ($tenant->data['database_strategy'] ?? 'separate') === 'separate' ? 'text-blue-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                    </div>
                    @if(($tenant->data['database_strategy'] ?? 'shared') === 'separate')
                    <div class="flex items-center space-x-2">
                        <div id="db-status-indicator" class="w-3 h-3 rounded-full bg-gray-400 animate-pulse"></div>
                        <span id="db-status-text" class="text-xs text-gray-500">Checking...</span>
                    </div>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Database Strategy</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ ucfirst($tenant->data['database_strategy'] ?? 'Shared') }}</p>
                    @if(($tenant->data['database_strategy'] ?? 'shared') === 'separate')
                    <div class="mt-2 text-xs text-gray-500">
                        <div>DB: <span id="db-name" class="font-mono">{{ $tenant->database_name ?? 'N/A' }}</span></div>
                        <div>Host: <span id="db-host" class="font-mono">{{ $tenant->database_host ?? 'N/A' }}</span></div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- User Count -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 rounded-lg bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900" id="user-count">-</p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Left Column - Main Content -->
            <div class="lg:col-span-3 space-y-8">
                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Email Address</label>
                            <p class="text-sm text-gray-900">{{ $tenant->data['email'] ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Tenant ID</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $tenant->id }}</p>
                        </div>
                    </div>
                </div>

                <!-- Domain Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Domain Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Primary Domain</label>
                            <div class="flex items-center space-x-3">
                                <p class="text-sm text-gray-900 font-mono">{{ $tenant->data['full_domain'] ?? $tenant->id . '.' . config('all.domains.primary') }}</p>
                                <a href="http://{{ $tenant->data['full_domain'] ?? $tenant->id . '.' . config('all.domains.primary') }}"
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Subdomain</label>
                            <p class="text-sm text-gray-900 font-mono">{{ $tenant->data['subdomain'] ?? $tenant->id }}</p>
                        </div>
                        @if(isset($tenant->data['custom_domain']))
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-2">Custom Domain</label>
                            <div class="flex items-center space-x-3">
                                <p class="text-sm text-gray-900 font-mono">{{ $tenant->data['custom_domain'] }}</p>
                                <a href="http://{{ $tenant->data['custom_domain'] }}"
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            <!-- Database Operations (for separate database tenants) -->
            @if(($tenant->data['database_strategy'] ?? 'shared') === 'separate')
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" data-section="database-operations">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Database Operations</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <button type="button"
                            onclick="testDatabaseConnection()"
                            class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors border border-blue-200">
                        <div class="p-2 bg-blue-100 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Test Connection</h4>
                            <p class="text-sm text-gray-600">Test database connectivity</p>
                        </div>
                    </button>

                    <button type="button"
                            onclick="createDatabase()"
                            class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors border border-green-200">
                        <div class="p-2 bg-green-100 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Create Database</h4>
                            <p class="text-sm text-gray-600">Create tenant database</p>
                        </div>
                    </button>

                    <button type="button"
                            onclick="runMigrations()"
                            class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors border border-purple-200">
                        <div class="p-2 bg-purple-100 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">Run Migrations</h4>
                            <p class="text-sm text-gray-600">Create database tables</p>
                        </div>
                    </button>

                </div>
            </div>
            @endif
        </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-4">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="http://{{ $tenant->data['full_domain'] ?? $tenant->id . '.' . config('all.domains.primary') }}"
                           target="_blank"
                           class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors border border-blue-200">
                            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Visit Tenant Site</h4>
                                <p class="text-sm text-gray-600">Open tenant website</p>
                            </div>
                        </a>

                        <a href="http://{{ $tenant->data['full_domain'] ?? $tenant->id . '.' . config('all.domains.primary') }}/admin"
                           target="_blank"
                           class="flex items-center p-3 bg-green-50 rounded-lg hover:bg-green-100 transition-colors border border-green-200">
                            <div class="p-2 bg-green-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Admin Panel</h4>
                                <p class="text-sm text-gray-600">Access tenant admin</p>
                            </div>
                        </a>

                        <a href="{{ route('admin.tenants.users.index', $tenant) }}"
                           class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors border border-purple-200">
                            <div class="p-2 bg-purple-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">Manage Users</h4>
                                <p class="text-sm text-gray-600">Edit tenant user accounts</p>
                            </div>
                        </a>

                        <form method="POST" action="{{ route('admin.tenants.toggle-status', $tenant) }}"
                              class="flex items-center p-3 {{ ($tenant->data['active'] ?? true) ? 'bg-red-50 hover:bg-red-100 border-red-200' : 'bg-green-50 hover:bg-green-100 border-green-200' }} rounded-lg transition-colors cursor-pointer border"
                              onsubmit="return confirm('Are you sure you want to {{ ($tenant->data['active'] ?? true) ? 'deactivate' : 'activate' }} this tenant?')">
                            @csrf
                            <button type="submit" class="flex items-center w-full">
                                <div class="p-2 {{ ($tenant->data['active'] ?? true) ? 'bg-red-100' : 'bg-green-100' }} rounded-lg mr-3">
                                    @if($tenant->data['active'] ?? true)
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="text-left">
                                    <h4 class="font-medium text-gray-900">{{ ($tenant->data['active'] ?? true) ? 'Deactivate Tenant' : 'Activate Tenant' }}</h4>
                                    <p class="text-sm text-gray-600">{{ ($tenant->data['active'] ?? true) ? 'Disable tenant access' : 'Enable tenant access' }}</p>
                                </div>
                            </button>
                        </form>
                    </div>
                </div>

            <!-- System Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <h3 class="text-base font-semibold text-gray-900 mb-4">System Status</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Status</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($tenant->data['active'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ($tenant->data['active'] ?? false) ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Created</span>
                        <span class="text-sm text-gray-900">{{ $tenant->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-600">Last Updated</span>
                        <span class="text-sm text-gray-900">{{ $tenant->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

                <!-- Live Database Monitoring -->
                @if(($tenant->data['database_strategy'] ?? 'shared') === 'separate')
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-gray-900">Live Monitoring</h3>
                        <div class="flex items-center space-x-2">
                            <div id="live-monitoring-indicator" class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                            <span class="text-xs text-gray-500">Live</span>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">Connection Status</span>
                                <div id="connection-status" class="flex items-center space-x-2">
                                    <div class="w-2 h-2 rounded-full bg-gray-400"></div>
                                    <span class="text-xs text-gray-500">Checking...</span>
                                </div>
                            </div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">Response Time</span>
                                <span id="response-time" class="text-xs text-gray-500">-</span>
                            </div>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600">Last Check</span>
                                <span id="last-check" class="text-xs text-gray-500">Never</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            <button onclick="startLiveMonitoring()"
                                    id="start-monitoring-btn"
                                    class="px-2 py-1 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded hover:bg-blue-100 transition-colors">
                                Start
                            </button>
                            <button onclick="stopLiveMonitoring()"
                                    id="stop-monitoring-btn"
                                    class="px-2 py-1 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded hover:bg-red-100 transition-colors hidden">
                                Stop
                            </button>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Recent Activity -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Recent Activity</h3>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">Tenant created</p>
                                <p class="text-xs text-gray-500">{{ $tenant->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @if(($tenant->data['database_strategy'] ?? 'shared') === 'separate')
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">Database operations</p>
                                <p class="text-xs text-gray-500">Available</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables for live monitoring
let monitoringInterval = null;
let isMonitoring = false;

// Load user count on page load
document.addEventListener('DOMContentLoaded', function() {
    loadUserCount();
    @if(($tenant->data['database_strategy'] ?? 'shared') === 'separate')
    checkDatabaseStatus();
    loadDatabaseInfo();
    @endif
});

function loadUserCount() {
    const tenantId = '{{ $tenant->id }}';

    fetch(`/admin/tenants/${tenantId}/users/count`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('user-count').textContent = data.count;
        } else {
            document.getElementById('user-count').textContent = '0';
        }
    })
    .catch(error => {
        console.error('Error loading user count:', error);
        document.getElementById('user-count').textContent = '0';
    });
}

function loadDatabaseInfo() {
    const tenantId = '{{ $tenant->id }}';

    fetch(`/admin/tenants/${tenantId}/database-info`, {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update database name and host in the status card
            const dbNameElement = document.getElementById('db-name');
            const dbHostElement = document.getElementById('db-host');

            if (dbNameElement) {
                dbNameElement.textContent = data.database;
            }
            if (dbHostElement) {
                dbHostElement.textContent = data.host;
            }

            // Update user count with database-specific count
            const userCountElement = document.getElementById('user-count');
            if (userCountElement && data.user_count !== undefined) {
                userCountElement.textContent = data.user_count;
            }
        }
    })
    .catch(error => {
        console.error('Error loading database info:', error);
    });
}

// Live Database Monitoring Functions
function checkDatabaseStatus() {
    const tenantId = '{{ $tenant->id }}';
    const startTime = Date.now();

    fetch(`/admin/tenants/${tenantId}/test-database`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        const responseTime = Date.now() - startTime;
        updateDatabaseStatus(data.success, responseTime, data.message);
    })
    .catch(error => {
        const responseTime = Date.now() - startTime;
        updateDatabaseStatus(false, responseTime, 'Connection failed');
    });
}

function updateDatabaseStatus(isConnected, responseTime, message) {
    const statusIndicator = document.getElementById('db-status-indicator');
    const statusText = document.getElementById('db-status-text');
    const connectionStatus = document.getElementById('connection-status');
    const responseTimeElement = document.getElementById('response-time');
    const lastCheckElement = document.getElementById('last-check');

    if (isConnected) {
        // Update main status card
        statusIndicator.className = 'w-3 h-3 rounded-full bg-green-400';
        statusText.textContent = 'Connected';
        statusText.className = 'text-sm text-green-600';

        // Update monitoring section
        if (connectionStatus) {
            connectionStatus.innerHTML = `
                <div class="w-2 h-2 rounded-full bg-green-400"></div>
                <span class="text-xs text-green-600">Connected</span>
            `;
        }

        if (responseTimeElement) {
            responseTimeElement.textContent = `${responseTime}ms`;
            responseTimeElement.className = responseTime < 100 ? 'text-xs text-green-600' :
                                           responseTime < 500 ? 'text-xs text-yellow-600' : 'text-xs text-red-600';
        }
    } else {
        // Update main status card
        statusIndicator.className = 'w-3 h-3 rounded-full bg-red-400';
        statusText.textContent = 'Disconnected';
        statusText.className = 'text-sm text-red-600';

        // Update monitoring section
        if (connectionStatus) {
            connectionStatus.innerHTML = `
                <div class="w-2 h-2 rounded-full bg-red-400"></div>
                <span class="text-xs text-red-600">Disconnected</span>
            `;
        }

        if (responseTimeElement) {
            responseTimeElement.textContent = 'Timeout';
            responseTimeElement.className = 'text-xs text-red-600';
        }
    }

    if (lastCheckElement) {
        lastCheckElement.textContent = new Date().toLocaleTimeString();
    }
}

function startLiveMonitoring() {
    if (isMonitoring) return;

    isMonitoring = true;
    const startBtn = document.getElementById('start-monitoring-btn');
    const stopBtn = document.getElementById('stop-monitoring-btn');
    const liveIndicator = document.getElementById('live-monitoring-indicator');

    startBtn.classList.add('hidden');
    stopBtn.classList.remove('hidden');
    liveIndicator.className = 'w-2 h-2 rounded-full bg-green-400 animate-pulse';

    // Check immediately
    checkDatabaseStatus();

    // Set up interval for every 10 seconds
    monitoringInterval = setInterval(checkDatabaseStatus, 10000);

    showNotification('success', 'Live monitoring started');
}

function stopLiveMonitoring() {
    if (!isMonitoring) return;

    isMonitoring = false;
    const startBtn = document.getElementById('start-monitoring-btn');
    const stopBtn = document.getElementById('stop-monitoring-btn');
    const liveIndicator = document.getElementById('live-monitoring-indicator');

    startBtn.classList.remove('hidden');
    stopBtn.classList.add('hidden');
    liveIndicator.className = 'w-2 h-2 rounded-full bg-gray-400';

    if (monitoringInterval) {
        clearInterval(monitoringInterval);
        monitoringInterval = null;
    }

    showNotification('info', 'Live monitoring stopped');
}

function testDatabaseConnection() {
    const tenantId = '{{ $tenant->id }}';
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;

    button.innerHTML = `
        <div class="p-2 bg-blue-100 rounded-lg mr-3">
            <svg class="w-5 h-5 text-blue-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">Testing...</h4>
            <p class="text-sm text-gray-600">Please wait</p>
        </div>
    `;
    button.disabled = true;

    fetch(`/admin/tenants/${tenantId}/test-database`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Error testing database connection');
    })
    .finally(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

function createDatabase() {
    const tenantId = '{{ $tenant->id }}';
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;

    if (!confirm('Are you sure you want to create the database? This action cannot be undone.')) {
        return;
    }

    button.innerHTML = `
        <div class="p-2 bg-green-100 rounded-lg mr-3">
            <svg class="w-5 h-5 text-green-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">Creating...</h4>
            <p class="text-sm text-gray-600">Please wait</p>
        </div>
    `;
    button.disabled = true;

    fetch(`/admin/tenants/${tenantId}/create-database`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Error creating database');
    })
    .finally(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}

function runMigrations() {
    const tenantId = '{{ $tenant->id }}';
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;

    if (!confirm('Are you sure you want to run migrations? This will create tables in the tenant database.')) {
        return;
    }

    button.innerHTML = `
        <div class="p-2 bg-purple-100 rounded-lg mr-3">
            <svg class="w-5 h-5 text-purple-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
        </div>
        <div>
            <h4 class="font-medium text-gray-900">Running...</h4>
            <p class="text-sm text-gray-600">Please wait</p>
        </div>
    `;
    button.disabled = true;

    fetch(`/admin/tenants/${tenantId}/run-migrations`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('success', data.message);

            // Display tables if available
            if (data.tables && data.tables.length > 0) {
                displayTables(data.tables);
            }

            // Display admin user info if available
            if (data.admin_user) {
                displayAdminUserInfo(data.admin_user);
            }
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Error running migrations');
    })
    .finally(() => {
        button.innerHTML = originalContent;
        button.disabled = false;
    });
}


function displayTables(tables) {
    // Find or create tables section
    let tablesSection = document.getElementById('database-tables-section');
    if (!tablesSection) {
        // Create tables section after database operations
        const dbOpsSection = document.querySelector('[data-section="database-operations"]');
        if (dbOpsSection) {
            tablesSection = document.createElement('div');
            tablesSection.id = 'database-tables-section';
            tablesSection.className = 'mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6';
            tablesSection.innerHTML = `
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Database Tables</h3>
                <div id="tables-list" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2"></div>
            `;
            dbOpsSection.parentNode.insertBefore(tablesSection, dbOpsSection.nextSibling);
        }
    }

    // Update tables list
    const tablesList = document.getElementById('tables-list');
    if (tablesList) {
        tablesList.innerHTML = tables.map(table =>
            `<div class="px-3 py-2 bg-gray-50 rounded-lg border text-sm font-mono text-gray-700">${table}</div>`
        ).join('');
    }
}

function displayAdminUserInfo(adminUser) {
    // Find or create admin user section
    let adminSection = document.getElementById('admin-user-section');
    if (!adminSection) {
        // Create admin user section after tables section
        const tablesSection = document.getElementById('database-tables-section');
        if (tablesSection) {
            adminSection = document.createElement('div');
            adminSection.id = 'admin-user-section';
            adminSection.className = 'mt-6 bg-white rounded-xl shadow-sm border border-blue-200 p-6 bg-blue-50';
            adminSection.innerHTML = `
                <h3 class="text-lg font-semibold text-blue-900 mb-4">Primary Admin User</h3>
                <div id="admin-user-info" class="space-y-4"></div>
            `;
            tablesSection.parentNode.insertBefore(adminSection, tablesSection.nextSibling);
        }
    }

    // Update admin user info
    const adminInfo = document.getElementById('admin-user-info');
    if (adminInfo) {
        adminInfo.innerHTML = `
            <div class="bg-white p-4 rounded-lg border border-blue-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600">Email:</label>
                        <p class="text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded border">${adminUser.email}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-600">Default Password:</label>
                        <p class="text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded border">${adminUser.default_password}</p>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800">
                        <strong>Important:</strong> Please change the default password after first login.
                    </p>
                </div>
            </div>
        `;
    }
}

function showNotification(type, message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-300' :
        'bg-red-100 text-red-800 border border-red-300'
    }`;

    notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                ${type === 'success' ?
                    '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>' :
                    '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>'
                }
            </svg>
            <span class="text-sm font-medium">${message}</span>
        </div>
    `;

    document.body.appendChild(notification);

    // Remove notification after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}
</script>
@endsection
