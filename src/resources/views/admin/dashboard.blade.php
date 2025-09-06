@extends('layouts.admin')

@section('title', 'Super Admin Dashboard')
@section('page-title', 'Super Admin Dashboard')
@section('page-description', 'Welcome to your admin dashboard')

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
    <!-- Total Tenants -->
    <div class="stat-card p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-primary-100 text-primary-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Tenants</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Tenant::count() }}</p>
            </div>
        </div>
    </div>

    <!-- Active Tenants -->
    <div class="stat-card p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Active Tenants</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Tenant::where('data->active', true)->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Admin Users -->
    <div class="stat-card p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Admin Users</p>
                <p class="text-2xl font-bold text-gray-900">{{ \App\Models\AdminUser::count() }}</p>
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="stat-card p-6">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">System Status</p>
                <p class="text-2xl font-bold text-green-600">Healthy</p>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
    <!-- Quick Actions Card -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="space-y-3">
            <a href="{{ route('admin.tenants.create') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create New Tenant
            </a>

            <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Add Admin User
            </a>

            <a href="{{ route('admin.admin.system.overview') }}" class="flex items-center p-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                <svg class="w-5 h-5 mr-3 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                System Settings
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="card p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h3>
        <div class="space-y-4">
            <div class="flex items-center">
                <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900">System started successfully</p>
                    <p class="text-xs text-gray-500">2 minutes ago</p>
                </div>
            </div>

            <div class="flex items-center">
                <div class="w-2 h-2 bg-blue-400 rounded-full mr-3"></div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900">New tenant created</p>
                    <p class="text-xs text-gray-500">1 hour ago</p>
                </div>
            </div>

            <div class="flex items-center">
                <div class="w-2 h-2 bg-yellow-400 rounded-full mr-3"></div>
                <div class="flex-1">
                    <p class="text-sm text-gray-900">Admin user logged in</p>
                    <p class="text-xs text-gray-500">3 hours ago</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Tenants -->
<div class="card p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Recent Tenants</h3>
        <a href="{{ route('admin.tenants.index') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View All</a>
    </div>

    <div class="overflow-x-auto -mx-4 sm:mx-0">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Domain</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Type</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden lg:table-cell">Created</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse(\App\Models\Tenant::latest()->take(5)->get() as $tenant)
                <tr>
                    <td class="px-3 sm:px-6 py-4 text-sm font-medium text-gray-900">
                        <div>
                            <div class="font-medium">{{ $tenant->data['name'] ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 sm:hidden">{{ $tenant->id }}.{{ config('all.domains.primary') }}</div>
                        </div>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                        {{ $tenant->id }}.{{ config('all.domains.primary') }}
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
                        {{ ucfirst($tenant->data['type'] ?? 'school') }}
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ ($tenant->data['active'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ($tenant->data['active'] ?? false) ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                        {{-- {{ @$tenant->created_at->format('M d, Y') }} --}}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-3 sm:px-6 py-4 text-center text-sm text-gray-500">
                        No tenants found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
