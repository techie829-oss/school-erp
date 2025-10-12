@extends('landing.layout')

@section('title', 'Multi-Tenancy Demo')
@section('description', 'See how our School ERP handles different tenant types and database strategies')

@section('content')
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-secondary-900 mb-4">Multi-Tenancy System Demo</h1>
            <p class="text-xl text-secondary-600 max-w-3xl mx-auto">
                See how our School ERP handles multiple tenants with shared database architecture.
            </p>
        </div>

        <!-- Demo Tenants Overview -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold text-secondary-900 mb-6">Example Tenant Scenarios</h2>
            <p class="text-secondary-600 mb-8 text-center">Below are example scenarios showing how different types of educational institutions can use our system.</p>

            @php
                // Demo/Example tenants - not real production data for security
                $demoTenants = [
                    [
                        'name' => 'Green Valley High School',
                        'type' => 'school',
                        'description' => 'K-12 public school with 800 students',
                        'icon' => '🏫',
                        'features' => ['Attendance Tracking', 'Grade Management', 'Parent Portal']
                    ],
                    [
                        'name' => 'Riverside Community College',
                        'type' => 'college',
                        'description' => 'Community college serving 2,500 students',
                        'icon' => '🎓',
                        'features' => ['Course Registration', 'Department Management', 'Academic Records']
                    ],
                    [
                        'name' => 'Metropolitan University',
                        'type' => 'university',
                        'description' => 'Multi-campus university with 15,000+ students',
                        'icon' => '🏛️',
                        'features' => ['Multi-Campus Support', 'Research Tracking', 'Advanced Analytics']
                    ],
                ];
            @endphp

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @foreach($demoTenants as $tenant)
                <div class="bg-white rounded-xl p-6 border border-gray-200 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <span class="text-4xl mr-3">{{ $tenant['icon'] }}</span>
                        <div>
                            <h4 class="font-semibold text-secondary-900">{{ $tenant['name'] }}</h4>
                            <span class="text-xs px-2 py-1 rounded-full
                                {{ $tenant['type'] === 'school' ? 'bg-indigo-100 text-indigo-800' :
                                   ($tenant['type'] === 'college' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($tenant['type']) }}
                            </span>
                        </div>
                    </div>
                    <p class="text-sm text-secondary-600 mb-4">{{ $tenant['description'] }}</p>
                    <div class="space-y-2">
                        <p class="text-xs font-semibold text-gray-700">Key Features:</p>
                        @foreach($tenant['features'] as $feature)
                        <div class="flex items-center text-xs text-gray-600">
                            <svg class="w-4 h-4 text-green-500 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            {{ $feature }}
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-8 p-6 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900 mb-2">🔒 Privacy & Security</h4>
                        <p class="text-sm text-blue-700">
                            These are example scenarios only. We respect our clients' privacy and don't publicly expose production tenant information.
                            Contact our sales team for a personalized demo with real-world scenarios tailored to your institution.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Institution Types Explanation -->
        <div class="bg-secondary-50 rounded-2xl p-8 mb-12">
            <h2 class="text-2xl font-semibold text-secondary-900 mb-6 text-center">Supported Institution Types</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- School Type -->
                <div>
                    <h3 class="text-lg font-semibold text-blue-700 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                        School
                    </h3>
                    <ul class="text-secondary-700 space-y-2 text-sm">
                        <li>• K-12 education management</li>
                        <li>• Student attendance tracking</li>
                        <li>• Grade management</li>
                        <li>• Parent portal access</li>
                    </ul>
                </div>

                <!-- College Type -->
                <div>
                    <h3 class="text-lg font-semibold text-purple-700 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                        </svg>
                        College
                    </h3>
                    <ul class="text-secondary-700 space-y-2 text-sm">
                        <li>• Higher education management</li>
                        <li>• Department organization</li>
                        <li>• Course registration</li>
                        <li>• Academic records</li>
                    </ul>
                </div>

                <!-- University Type -->
                <div>
                    <h3 class="text-lg font-semibold text-green-700 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"/>
                        </svg>
                        University
                    </h3>
                    <ul class="text-secondary-700 space-y-2 text-sm">
                        <li>• Multi-campus management</li>
                        <li>• Research tracking</li>
                        <li>• Faculty management</li>
                        <li>• Advanced analytics</li>
                    </ul>
                </div>
            </div>

            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900">Secure Data Isolation</h4>
                        <p class="text-sm text-blue-700 mt-1">
                            All tenants use a shared database with complete data isolation using tenant_id. This ensures security, efficiency, and easy maintenance.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Get Started -->
        <div class="bg-accent-50 rounded-2xl p-8">
            <h2 class="text-2xl font-semibold text-accent-900 mb-6 text-center">Ready to Get Started?</h2>
            <p class="text-accent-700 mb-8 text-center max-w-2xl mx-auto">
                Experience our multi-tenant School ERP system designed for educational institutions of all sizes.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg p-6 text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Schedule a Demo</h3>
                    <p class="text-sm text-gray-600">See the system in action with a personalized walkthrough</p>
                </div>

                <div class="bg-white rounded-lg p-6 text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Free Trial</h3>
                    <p class="text-sm text-gray-600">Try our system risk-free for 30 days with full features</p>
                </div>

                <div class="bg-white rounded-lg p-6 text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Contact Sales</h3>
                    <p class="text-sm text-gray-600">Discuss custom solutions for your institution</p>
                </div>
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('landing.contact') }}" class="inline-flex items-center px-8 py-3 bg-accent-600 text-white font-semibold rounded-lg hover:bg-accent-700 transition-colors shadow-lg">
                    Get In Touch
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
