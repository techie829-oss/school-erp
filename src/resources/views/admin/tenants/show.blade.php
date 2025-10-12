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
                        <p class="mt-2 text-lg text-gray-600">{{ ucfirst($tenant->data['type'] ?? 'Institution') }} •
                            {{ $tenant->data['full_domain'] ?? $tenant->id . '.' . config('all.domains.primary') }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('admin.tenants.edit', $tenant) }}"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Tenant
                        </a>
                        <a href="{{ route('admin.tenants.index') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
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
                        <div class="p-3 rounded-lg bg-green-100">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Tenant Type</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ ucfirst($tenant->data['type'] ?? 'School') }}</p>
                        <div class="mt-2 text-xs text-gray-500">
                            <div>{{ $tenant->data['name'] ?? 'Tenant' }}</div>
                        </div>
                    </div>
                </div>

                <!-- User Count -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-lg bg-green-100">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
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
                                    <p class="text-sm text-gray-900 font-mono">
                                        {{ $tenant->data['full_domain'] ?? $tenant->id . '.' . config('all.domains.primary') }}
                                    </p>
                                    <a href="http://{{ $tenant->data['full_domain'] ?? $tenant->id . '.' . config('all.domains.primary') }}"
                                        target="_blank" class="text-blue-600 hover:text-blue-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-2">Subdomain</label>
                                <p class="text-sm text-gray-900 font-mono">{{ $tenant->data['subdomain'] ?? $tenant->id }}
                                </p>
                            </div>
                            @if (isset($tenant->data['custom_domain']))
                                <div>
                                    <label class="block text-sm font-medium text-gray-600 mb-2">Custom Domain</label>
                                    <div class="flex items-center space-x-3">
                                        <p class="text-sm text-gray-900 font-mono">{{ $tenant->data['custom_domain'] }}</p>
                                        <a href="http://{{ $tenant->data['custom_domain'] }}" target="_blank"
                                            class="text-blue-600 hover:text-blue-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Database Operations -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
                        data-section="database-operations">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Database Operations</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">User Count</h4>
                                <p class="text-2xl font-bold text-primary-600" id="user-count-detail">-</p>
                                <p class="text-xs text-gray-500">Total admin users</p>
                            </div>
                            <div class="p-4 border border-gray-200 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Status</h4>
                                <p class="text-sm font-medium text-green-600">✓ Connected</p>
                                <p class="text-xs text-gray-500">Shared database</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column - Sidebar -->
                <div class="lg:col-span-1 space-y-4">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="http://{{ $tenant->data['full_domain'] ?? $tenant->id . '.' . config('all.domains.primary') }}"
                                target="_blank"
                                class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors border border-blue-200">
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
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
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-medium text-gray-900">Manage Users</h4>
                                    <p class="text-sm text-gray-600">Edit tenant user accounts</p>
                                </div>
                            </a>

                            <form method="POST" action="{{ route('admin.tenants.toggle-status', $tenant) }}"
                                class="flex items-center p-3 {{ $tenant->data['active'] ?? true ? 'bg-red-50 hover:bg-red-100 border-red-200' : 'bg-green-50 hover:bg-green-100 border-green-200' }} rounded-lg transition-colors cursor-pointer border"
                                onsubmit="return confirm('Are you sure you want to {{ $tenant->data['active'] ?? true ? 'deactivate' : 'activate' }} this tenant?')">
                                @csrf
                                <button type="submit" class="flex items-center w-full">
                                    <div
                                        class="p-2 {{ $tenant->data['active'] ?? true ? 'bg-red-100' : 'bg-green-100' }} rounded-lg mr-3">
                                        @if ($tenant->data['active'] ?? true)
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="text-left">
                                        <h4 class="font-medium text-gray-900">
                                            {{ $tenant->data['active'] ?? true ? 'Deactivate Tenant' : 'Activate Tenant' }}
                                        </h4>
                                        <p class="text-sm text-gray-600">
                                            {{ $tenant->data['active'] ?? true ? 'Disable tenant access' : 'Enable tenant access' }}
                                        </p>
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
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tenant->data['active'] ?? false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $tenant->data['active'] ?? false ? 'Active' : 'Inactive' }}
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
                        // Update both user count elements
                        const mainCount = document.getElementById('user-count');
                        const detailCount = document.getElementById('user-count-detail');

                        if (mainCount) mainCount.textContent = data.count;
                        if (detailCount) detailCount.textContent = data.count;
                    } else {
                        const mainCount = document.getElementById('user-count');
                        const detailCount = document.getElementById('user-count-detail');

                        if (mainCount) mainCount.textContent = '0';
                        if (detailCount) detailCount.textContent = '0';
                    }
                })
                .catch(error => {
                    console.error('Error loading user count:', error);
                    const mainCount = document.getElementById('user-count');
                    const detailCount = document.getElementById('user-count-detail');

                    if (mainCount) mainCount.textContent = '0';
                    if (detailCount) detailCount.textContent = '0';
                });
        }

        function showNotification(type, message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 max-w-sm ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-300' :
        type === 'info' ? 'bg-blue-100 text-blue-800 border border-blue-300' :
        'bg-red-100 text-red-800 border border-red-300'
    }`;

            notification.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                ${type === 'success' ?
                    '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>' :
                    type === 'info' ?
                    '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>' :
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
