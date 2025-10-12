<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Error {{ $exception->getStatusCode() ?? 'Unknown' }} - {{ config('app.name', 'School ERP') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dynamic Tenant Colors -->
    <style>
        @php
            $tenantService = app(\App\Services\TenantService::class);
            $colorService = app(\App\Services\ColorPaletteService::class);
            $currentTenant = $tenantService->getCurrentTenant(request());
            $colors = $currentTenant ? $colorService->getAllColors(request()) : [];
        @endphp

        :root {
            --color-primary-50: {{ $colors['primary']['50'] ?? '#eff6ff' }};
            --color-primary-100: {{ $colors['primary']['100'] ?? '#dbeafe' }};
            --color-primary-500: {{ $colors['primary']['500'] ?? '#3b82f6' }};
            --color-primary-600: {{ $colors['primary']['600'] ?? '#2563eb' }};
            --color-primary-700: {{ $colors['primary']['700'] ?? '#1d4ed8' }};
            --color-primary-900: {{ $colors['primary']['900'] ?? '#1e3a8a' }};
            --color-secondary-50: {{ $colors['secondary']['50'] ?? '#f8fafc' }};
            --color-secondary-100: {{ $colors['secondary']['100'] ?? '#f1f5f9' }};
            --color-secondary-500: {{ $colors['secondary']['500'] ?? '#64748b' }};
            --color-secondary-600: {{ $colors['secondary']['600'] ?? '#475569' }};
            --color-secondary-700: {{ $colors['secondary']['700'] ?? '#334155' }};
            --color-secondary-900: {{ $colors['secondary']['900'] ?? '#0f172a' }};
            --color-accent-50: {{ $colors['accent']['50'] ?? '#fef3c7' }};
            --color-accent-500: {{ $colors['accent']['500'] ?? '#f59e0b' }};
            --color-accent-600: {{ $colors['accent']['600'] ?? '#d97706' }};
            --color-accent-700: {{ $colors['accent']['700'] ?? '#b45309' }};
            --color-success: {{ $colors['success'] ?? '#10b981' }};
            --color-warning: {{ $colors['warning'] ?? '#f59e0b' }};
            --color-error: {{ $colors['error'] ?? '#ef4444' }};
            --color-info: {{ $colors['info'] ?? '#3b82f6' }};
        }

        .bg-primary-50 { background-color: var(--color-primary-50) !important; }
        .bg-primary-100 { background-color: var(--color-primary-100) !important; }
        .bg-primary-500 { background-color: var(--color-primary-500) !important; }
        .bg-primary-600 { background-color: var(--color-primary-600) !important; }
        .bg-primary-700 { background-color: var(--color-primary-700) !important; }
        .bg-primary-900 { background-color: var(--color-primary-900) !important; }

        .text-primary-50 { color: var(--color-primary-50) !important; }
        .text-primary-100 { color: var(--color-primary-100) !important; }
        .text-primary-500 { color: var(--color-primary-500) !important; }
        .text-primary-600 { color: var(--color-primary-600) !important; }
        .text-primary-700 { color: var(--color-primary-700) !important; }
        .text-primary-900 { color: var(--color-primary-900) !important; }

        .border-primary-500 { border-color: var(--color-primary-500) !important; }
        .border-primary-600 { border-color: var(--color-primary-600) !important; }

        .focus\:ring-primary-500:focus { --tw-ring-color: var(--color-primary-500) !important; }
        .focus\:border-primary-500:focus { border-color: var(--color-primary-500) !important; }

        .hover\:bg-primary-600:hover { background-color: var(--color-primary-600) !important; }
        .hover\:bg-primary-700:hover { background-color: var(--color-primary-700) !important; }

        .bg-secondary-50 { background-color: var(--color-secondary-50) !important; }
        .bg-secondary-100 { background-color: var(--color-secondary-100) !important; }
        .text-secondary-600 { color: var(--color-secondary-600) !important; }
        .text-secondary-700 { color: var(--color-secondary-700) !important; }
        .text-secondary-900 { color: var(--color-secondary-900) !important; }

        .bg-accent-50 { background-color: var(--color-accent-50) !important; }
        .text-accent-600 { color: var(--color-accent-600) !important; }

        .bg-error-50 { background-color: #fef2f2 !important; }
        .text-error-600 { color: var(--color-error) !important; }
        .text-error-700 { color: #b91c1c !important; }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-primary-50 via-white to-secondary-50 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-accent-50 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
            <div class="absolute top-40 left-40 w-80 h-80 bg-error-50 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 text-center max-w-2xl py-10 mx-auto px-6">
            <!-- Logo -->
            <div class="mb-8">
                <div class="w-24 h-24 mx-auto mb-6 bg-primary-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-14 h-14 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838l-2.727 1.17a1 1 0 00-.356.257l-4 1.714a1 1 0 01-1.788-1.838l7-3a1 1 0 00.788 0l7 3a1 1 0 000-1.84L14.75 7.051a.999.999 0 01-.356-.257l-4-1.714a1 1 0 11-.788-1.838l2.727 1.17a1 1 0 00.356.257l4 1.714a1 1 0 001.788-1.838l-7-3z"/>
                    </svg>
                </div>

                @if($currentTenant)
                    <h1 class="text-2xl font-bold text-primary-900 mb-2">
                        {{ $currentTenant->data['name'] ?? 'School ERP' }}
                    </h1>
                @else
                    <h1 class="text-2xl font-bold text-primary-900 mb-2">School ERP</h1>
                @endif
            </div>

            <!-- Error Code -->
            <div class="mb-8">
                <div class="text-8xl font-bold text-error-600 mb-4">
                    {{ $exception->getStatusCode() ?? 'ERROR' }}
                </div>
                <h2 class="text-3xl font-bold text-secondary-900 mb-4">
                    @switch($exception->getStatusCode())
                        @case(403)
                            Access Forbidden
                            @break
                        @case(401)
                            Unauthorized Access
                            @break
                        @case(429)
                            Too Many Requests
                            @break
                        @case(503)
                            Service Unavailable
                            @break
                        @default
                            Something Went Wrong
                    @endswitch
                </h2>
                <p class="text-lg text-secondary-600 mb-6">
                    @switch($exception->getStatusCode())
                        @case(403)
                            You don't have permission to access this resource.
                            @break
                        @case(401)
                            Please log in to access this page.
                            @break
                        @case(429)
                            You've made too many requests. Please try again later.
                            @break
                        @case(503)
                            Our service is temporarily unavailable. Please try again later.
                            @break
                        @default
                            An unexpected error occurred. Please try again.
                    @endswitch
                </p>
            </div>

            <!-- Error Details -->
            <div class="bg-white/80 backdrop-blur-sm rounded-xl p-6 mb-8 shadow-lg border border-white/20">
                <div class="flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-error-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                    <h3 class="text-lg font-semibold text-secondary-900">Error Details</h3>
                </div>
                <div class="bg-error-50 border border-error-200 rounded-lg p-4">
                    <p class="text-error-700 text-sm">
                        <strong>Error Code:</strong> {{ $exception->getStatusCode() ?? 'Unknown' }}<br>
                        <strong>Time:</strong> {{ now()->format('Y-m-d H:i:s T') }}<br>
                        @if($exception->getMessage())
                            <strong>Message:</strong> {{ $exception->getMessage() }}
                        @endif
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                @if($exception->getStatusCode() == 401)
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Login
                    </a>
                @else
                    <button onclick="window.history.back()"
                            class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Go Back
                    </button>
                @endif

                @if($currentTenant)
                    <a href="{{ url('/') }}"
                       class="inline-flex items-center px-6 py-3 bg-secondary-100 text-secondary-700 font-medium rounded-lg hover:bg-secondary-200 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('landing.home') }}"
                       class="inline-flex items-center px-6 py-3 bg-secondary-100 text-secondary-700 font-medium rounded-lg hover:bg-secondary-200 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        Go to Homepage
                    </a>
                @endif
            </div>

            <!-- Helpful Information -->
            <div class="bg-white/60 backdrop-blur-sm rounded-xl p-6 shadow-lg border border-white/20">
                <h4 class="text-lg font-semibold text-secondary-900 mb-4">What you can do:</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    @switch($exception->getStatusCode())
                        @case(403)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                <span class="text-secondary-600">Contact administrator for access</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                <span class="text-secondary-600">Try logging in with different account</span>
                            </div>
                            @break
                        @case(401)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                <span class="text-secondary-600">Login to your account</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-secondary-600">Check your credentials</span>
                            </div>
                            @break
                        @case(429)
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-secondary-600">Wait a few minutes</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span class="text-secondary-600">Try again later</span>
                            </div>
                            @break
                        @default
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                <span class="text-secondary-600">Refresh the page</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-primary-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-secondary-600">Contact support if it persists</span>
                            </div>
                    @endswitch
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 text-center">
            <p class="text-secondary-600 text-sm">
                © {{ date('Y') }} {{ config('all.company.name', 'School ERP') }}. All rights reserved.
            </p>
        </div>
    </div>

    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob { animation: blob 7s infinite; }
        .animation-delay-2000 { animation-delay: 2s; }
        .animation-delay-4000 { animation-delay: 4s; }
    </style>
</body>
</html>
