<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - School ERP</title>
    <meta name="description" content="@yield('description', 'Admin Dashboard for School ERP System')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800" rel="stylesheet" />

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

    <style>
        body {
            min-height: 100vh;
            background-color: var(--color-secondary-50);
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--color-primary-600) 0%, var(--color-primary-900) 100%);
        }

        .content-area {
            min-height: 100vh;
        }

        .card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .stat-card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .btn-primary {
            background-color: var(--color-primary-600);
            border-color: var(--color-primary-600);
        }

        .btn-primary:hover {
            background-color: var(--color-primary-700);
            border-color: var(--color-primary-700);
        }

        .text-primary {
            color: var(--color-primary-600) !important;
        }

        .border-primary {
            border-color: var(--color-primary-600) !important;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <div class="flex">
        <!-- Sidebar -->
        <div class="sidebar w-64 flex-shrink-0">
            <div class="p-6">
                <!-- Logo -->
                <div class="flex items-center mb-8">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold text-white">School ERP</h1>
                </div>

                <!-- Navigation -->
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 text-white bg-white bg-opacity-20 rounded-lg">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('admin.admin.users.index') }}" class="flex items-center px-4 py-3 text-purple-200 hover:text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        Admin Users
                    </a>

                    <a href="{{ route('admin.tenants.index') }}" class="flex items-center px-4 py-3 text-purple-200 hover:text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Tenants
                    </a>

                    <a href="{{ route('admin.vhost.index') }}" class="flex items-center px-4 py-3 text-purple-200 hover:text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                        </svg>
                        Vhost Management
                    </a>

                    <a href="{{ route('admin.admin.system.overview') }}" class="flex items-center px-4 py-3 text-purple-200 hover:text-white hover:bg-white hover:bg-opacity-10 rounded-lg transition-colors">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        System Overview
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-area flex-1">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-sm text-gray-600 mt-1">@yield('page-description', 'Welcome to your admin dashboard')</p>
                        </div>

                        <div class="flex items-center space-x-4">
                            <!-- User Menu -->
                            <div class="relative">
                                <button class="flex items-center text-sm text-gray-700 hover:text-gray-900 focus:outline-none">
                                    <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center text-white font-medium">
                                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                                    </div>
                                    <span class="ml-2">{{ auth()->user()->name ?? 'Admin' }}</span>
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </div>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 focus:outline-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
