@extends('landing.layout')

@section('title', 'Multi-Tenancy Demo')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-primary-50 to-primary-100 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
                Multi-Tenancy <span class="text-primary-600">Demo</span>
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                See how our School ERP system handles multiple tenants with different color schemes and content.
            </p>
        </div>
    </div>
</section>

<!-- Current Tenant Info -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Current Tenant Information</h2>
            <p class="text-xl text-gray-600">You are currently viewing the system as:</p>
        </div>

        <div class="bg-gray-50 rounded-lg p-8 max-w-2xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <strong class="text-gray-900">Domain:</strong>
                    <p class="text-gray-600">{{ request()->getHost() }}</p>
                </div>
                <div>
                    <strong class="text-gray-900">Tenant ID:</strong>
                    <p class="text-gray-600">{{ app(\App\Services\TenantService::class)->getCurrentTenantId(request()) ?? 'landing' }}</p>
                </div>
                <div>
                    <strong class="text-gray-900">Tenant Type:</strong>
                    <p class="text-gray-600">{{ app(\App\Services\TenantService::class)->getTenantType(request()) ?? 'landing' }}</p>
                </div>
                <div>
                    <strong class="text-gray-900">Database Strategy:</strong>
                    <p class="text-gray-600">{{ app(\App\Services\TenantService::class)->isSharedDatabase(request()) ? 'Shared' : 'Separate' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Available Tenants -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Available Tenants</h2>
            <p class="text-xl text-gray-600">Visit different tenant domains to see their unique color schemes and content</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Landing Page -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Landing Page</h3>
                    <p class="text-gray-600 text-sm">Main marketing site</p>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <strong class="text-gray-900">Domain:</strong>
                        <p class="text-gray-600">myschool.test</p>
                    </div>
                    <div class="mb-4">
                        <strong class="text-gray-900">Type:</strong>
                        <p class="text-gray-600">Landing</p>
                    </div>
                    <div class="mb-4">
                        <strong class="text-gray-900">Database:</strong>
                        <p class="text-gray-600">Shared</p>
                    </div>
                    <a href="http://myschool.test" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Visit Site
                    </a>
                </div>
            </div>

            <!-- School A -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-br from-green-50 to-green-100 p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Delhi Public School</h3>
                    <p class="text-gray-600 text-sm">Premium school (Shared DB)</p>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <strong class="text-gray-900">Domain:</strong>
                        <p class="text-gray-600">schoola.myschool.test</p>
                    </div>
                    <div class="mb-4">
                        <strong class="text-gray-900">Type:</strong>
                        <p class="text-gray-600">School</p>
                    </div>
                    <div class="mb-4">
                        <strong class="text-gray-900">Database:</strong>
                        <p class="text-gray-600">Shared</p>
                    </div>
                    <a href="http://schoola.myschool.test" class="inline-flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition-colors">
                        Visit School
                    </a>
                </div>
            </div>

            <!-- School B -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Mumbai International</h3>
                    <p class="text-gray-600 text-sm">International school (Separate DB)</p>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <strong class="text-gray-900">Domain:</strong>
                        <p class="text-gray-600">schoolb.myschool.test</p>
                    </div>
                    <div class="mb-4">
                        <strong class="text-gray-900">Type:</strong>
                        <p class="text-gray-600">School</p>
                    </div>
                    <div class="mb-4">
                        <strong class="text-gray-900">Database:</strong>
                        <p class="text-gray-600">Separate</p>
                    </div>
                    <a href="http://schoolb.myschool.test" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors">
                        Visit School
                    </a>
                </div>
            </div>

            <!-- School C -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-br from-red-50 to-red-100 p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Bangalore Tech Academy</h3>
                    <p class="text-gray-600 text-sm">Tech-focused academy (Separate DB)</p>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <strong class="text-gray-900">Domain:</strong>
                        <p class="text-gray-600">schoolc.myschool.test</p>
                    </div>
                    <div class="mb-4">
                        <strong class="text-gray-900">Type:</strong>
                        <p class="text-gray-600">School</p>
                    </div>
                    <div class="mb-4">
                        <strong class="text-gray-900">Database:</strong>
                        <p class="text-gray-600">Separate</p>
                    </div>
                    <a href="http://schoolc.myschool.test" class="inline-flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                        Visit School
                    </a>
                </div>
            </div>

            <!-- Internal Admin -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 p-6">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Internal Admin</h3>
                    <p class="text-gray-600 text-sm">Super admin access</p>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <strong class="text-gray-900">Domain:</strong>
                        <p class="text-gray-600">app.myschool.test</p>
                    </div>
                    <div class="mb-4">
                        <strong class="text-gray-900">Type:</strong>
                        <p class="text-gray-600">Internal</p>
                    </div>
                    <div class="mb-4">
                        <strong class="text-gray-900">Database:</strong>
                        <p class="text-gray-600">Shared</p>
                    </div>
                    <a href="http://app.myschool.test" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        Admin Panel
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">How Multi-Tenancy Works</h2>
            <p class="text-xl text-gray-600">Understanding the architecture behind our system</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Domain Detection</h3>
                <p class="text-gray-600">System automatically detects the current domain and identifies the corresponding tenant.</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Dynamic Content</h3>
                <p class="text-gray-600">Each tenant gets personalized content, branding, and color schemes based on their configuration.</p>
            </div>

            <div class="text-center">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Data Isolation</h3>
                <p class="text-gray-600">Schools can choose between shared or separate databases for complete data isolation when needed.</p>
            </div>
        </div>
    </div>
</section>
@endsection
