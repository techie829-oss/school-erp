@extends('landing.layout')

@section('title', 'Multi-Tenancy Demo')
@section('description', 'See how our School ERP handles different tenant types and database strategies')

@section('content')
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-secondary-900 mb-4">Multi-Tenancy System Demo</h1>
            <p class="text-xl text-secondary-600 max-w-3xl mx-auto">
                See how our School ERP handles different tenant types with shared and separate database strategies.
            </p>
        </div>

        <!-- Current Tenant Info -->
        <div class="bg-primary-50 rounded-2xl p-8 mb-12">
            <h2 class="text-2xl font-semibold text-primary-900 mb-6">Current Tenant Information</h2>
            @php
                $tenantService = app(\App\Services\TenantService::class);
                $tenantInfo = $tenantService->getTenantInfo(request());
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-semibold text-secondary-900 mb-2">Tenant ID</h3>
                    <p class="text-primary-600 font-mono">{{ $tenantInfo['id'] ?? 'Unknown' }}</p>
                </div>

                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-semibold text-secondary-900 mb-2">Name</h3>
                    <p class="text-primary-600">{{ $tenantInfo['name'] ?? 'Unknown' }}</p>
                </div>

                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-semibold text-secondary-900 mb-2">Domain</h3>
                    <p class="text-primary-600 font-mono">{{ $tenantInfo['domain'] ?? 'Unknown' }}</p>
                </div>

                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-semibold text-secondary-900 mb-2">Database Strategy</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $tenantInfo['database_strategy'] === 'shared' ? 'bg-green-100 text-green-800' :
                           ($tenantInfo['database_strategy'] === 'separate' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($tenantInfo['database_strategy'] ?? 'Unknown') }}
                    </span>
                </div>

                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-semibold text-secondary-900 mb-2">Type</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $tenantInfo['type'] === 'internal' ? 'bg-purple-100 text-purple-800' :
                           ($tenantInfo['type'] === 'school' ? 'bg-indigo-100 text-indigo-800' :
                           ($tenantInfo['type'] === 'landing' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                        {{ ucfirst($tenantInfo['type'] ?? 'Unknown') }}
                    </span>
                </div>

                <div class="bg-white rounded-lg p-4">
                    <h3 class="font-semibold text-secondary-900 mb-2">Status</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $tenantInfo['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($tenantInfo['status'] ?? 'Unknown') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- All Tenants Overview -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold text-secondary-900 mb-6">All Tenants Overview</h2>
            @php
                $allTenants = $tenantService->getAllTenants();
                $sharedTenants = $tenantService->getSharedDatabaseTenants();
                $separateTenants = $tenantService->getSeparateDatabaseTenants();

                // Filter out internal admin tenants from public view
                $publicSharedTenants = collect($sharedTenants)->filter(function($tenant) {
                    return $tenant['type'] !== 'internal';
                })->values();

                $publicSeparateTenants = collect($separateTenants)->filter(function($tenant) {
                    return $tenant['type'] !== 'internal';
                })->values();
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Shared Database Tenants -->
                <div class="bg-green-50 rounded-2xl p-6">
                    <h3 class="text-xl font-semibold text-green-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                        Shared Database Strategy ({{ count($publicSharedTenants) }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($publicSharedTenants as $tenant)
                        <div class="bg-white rounded-lg p-4 border border-green-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-semibold text-green-900">{{ $tenant['name'] }}</h4>
                                    <p class="text-sm text-green-700">{{ $tenant['domain'] }}</p>
                                    <p class="text-xs text-green-600">{{ $tenant['description'] }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ ucfirst($tenant['type']) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Separate Database Tenants -->
                <div class="bg-blue-50 rounded-2xl p-6">
                    <h3 class="text-xl font-semibold text-blue-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                        Separate Database Strategy ({{ count($publicSeparateTenants) }})
                    </h3>
                    <div class="space-y-3">
                        @foreach($publicSeparateTenants as $tenant)
                        <div class="bg-white rounded-lg p-4 border border-blue-200">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-semibold text-blue-900">{{ $tenant['name'] }}</h4>
                                    <p class="text-sm text-blue-700">{{ $tenant['domain'] }}</p>
                                    <p class="text-xs text-blue-600">{{ $tenant['description'] }}</p>
                                    @if($tenant['student_count'])
                                    <p class="text-xs text-blue-500 mt-1">Students: {{ number_format($tenant['student_count']) }}</p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($tenant['type']) }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Strategy Explanation -->
        <div class="bg-secondary-50 rounded-2xl p-8 mb-12">
            <h2 class="text-2xl font-semibold text-secondary-900 mb-6">Database Strategy Explanation</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Shared Database -->
                <div>
                    <h3 class="text-lg font-semibold text-green-700 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                        Shared Database
                    </h3>
                    <ul class="text-secondary-700 space-y-2">
                        <li>• All tenants share the same database</li>
                        <li>• Data is separated using tenant_id field</li>
                        <li>• Lower cost and easier maintenance</li>
                        <li>• Suitable for smaller schools and internal use</li>
                        <li>• All tables include tenant_id for data isolation</li>
                    </ul>
                </div>

                <!-- Separate Database -->
                <div>
                    <h3 class="text-lg font-semibold text-blue-700 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                        </svg>
                        Separate Database
                    </h3>
                    <ul class="text-secondary-700 space-y-2">
                        <li>• Each tenant gets their own database</li>
                        <li>• Complete data isolation and security</li>
                        <li>• Higher cost but better performance</li>
                        <li>• Suitable for large schools and enterprises</li>
                        <li>• Still maintains tenant_id for portability</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Test Different Tenants -->
        <div class="bg-accent-50 rounded-2xl p-8">
            <h2 class="text-2xl font-semibold text-accent-900 mb-6">Test Different Tenants</h2>
            <p class="text-accent-700 mb-6">
                Visit these different domains to see how the system automatically detects tenants and applies their specific color palettes:
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @php
                    // Get all public tenants for testing (excluding internal admin)
                    $publicTenants = collect($allTenants)->filter(function($tenant) {
                        return $tenant['type'] !== 'internal';
                    })->values();
                @endphp

                @foreach($publicTenants as $tenant)
                <a href="http://{{ $tenant['domain'] }}" class="bg-white rounded-lg p-4 border border-accent-200 hover:border-accent-300 transition-colors">
                    <h4 class="font-semibold text-accent-900">{{ $tenant['name'] }}</h4>
                    <p class="text-sm text-accent-700">{{ $tenant['domain'] }}</p>
                    <p class="text-xs text-accent-600">
                        {{ ucfirst($tenant['type']) }} theme, {{ $tenant['database_strategy'] }} DB
                        @if($tenant['student_count'])
                        <br>Students: {{ number_format($tenant['student_count']) }}
                        @endif
                    </p>
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
