<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

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
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 py-10 sm:pt-0 bg-gradient-to-br from-primary-50 via-white to-secondary-50">
            <!-- Background Pattern -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
                <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-accent-50 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
                <div class="absolute top-40 left-40 w-80 h-80 bg-secondary-100 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
            </div>

            <!-- Logo and Tenant Info -->
            <div class="relative z-10 my-10 text-center mb-8">
                <a href="/" wire:navigate class="inline-block">
                    <div class="w-20 h-20 mx-auto mb-4 bg-primary-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-12 h-12 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838l-2.727 1.17a1 1 0 00-.356.257l-4 1.714a1 1 0 01-1.788-1.838l7-3a1 1 0 00.788 0l7 3a1 1 0 000-1.84L14.75 7.051a.999.999 0 01-.356-.257l-4-1.714a1 1 0 11-.788-1.838l2.727 1.17a1 1 0 00.356.257l4 1.714a1 1 0 001.788-1.838l-7-3z"/>
                        </svg>
                    </div>
                </a>

                @if($currentTenant)
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-primary-900 mb-2">
                            {{ $currentTenant->data['name'] ?? 'School ERP' }}
                        </h1>
                        <p class="text-secondary-600 text-lg">
                            @if($currentTenant->data['type'] === 'internal')
                                Super Admin Portal
                            @elseif($currentTenant->data['type'] === 'school')
                                School Management System
                            @else
                                Welcome Back
                            @endif
                        </p>
                        @if($currentTenant->data['type'] === 'school')
                            <div class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-accent-50 text-accent-600">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                {{ ucfirst($currentTenant->data['database_strategy'] ?? 'shared') }} Database
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mb-6">
                        <h1 class="text-3xl font-bold text-primary-900 mb-2">School ERP</h1>
                        <p class="text-secondary-600 text-lg">Welcome Back</p>
                    </div>
                @endif
            </div>

            <!-- Login Form Container -->
            <div class="relative z-10 w-full sm:max-w-md px-6 py-8 bg-white/80 backdrop-blur-sm shadow-2xl rounded-2xl border border-white/20">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="relative z-10 mt-8 text-center">
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
