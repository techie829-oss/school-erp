<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'School ERP') - {{ tenant('data.name') ?? 'School Management' }}</title>

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
            @if(isset($colors['primary']))
                @foreach($colors['primary'] as $shade => $color)
                    --color-primary-{{ $shade }}: {{ $color }};
                @endforeach
            @else
                --color-primary-50: #eff6ff;
                --color-primary-100: #dbeafe;
                --color-primary-500: #3b82f6;
                --color-primary-600: #2563eb;
                --color-primary-700: #1d4ed8;
                --color-primary-900: #1e3a8a;
            @endif

            @if(isset($colors['secondary']))
                @foreach($colors['secondary'] as $shade => $color)
                    --color-secondary-{{ $shade }}: {{ $color }};
                @endforeach
            @else
                --color-secondary-50: #f8fafc;
                --color-secondary-100: #f1f5f9;
                --color-secondary-500: #64748b;
                --color-secondary-600: #475569;
                --color-secondary-700: #334155;
                --color-secondary-900: #0f172a;
            @endif

            @if(isset($colors['accent']))
                @foreach($colors['accent'] as $shade => $color)
                    --color-accent-{{ $shade }}: {{ $color }};
                @endforeach
            @else
                --color-accent-50: #fef3c7;
                --color-accent-100: #fde68a;
                --color-accent-500: #f59e0b;
                --color-accent-600: #d97706;
                --color-accent-700: #b45309;
                --color-accent-900: #78350f;
            @endif

            @if(isset($colors['success']))
                --color-success: {{ $colors['success'] }};
            @else
                --color-success: #10b981;
            @endif

            @if(isset($colors['warning']))
                --color-warning: {{ $colors['warning'] }};
            @else
                --color-warning: #f59e0b;
            @endif

            @if(isset($colors['error']))
                --color-error: {{ $colors['error'] }};
            @else
                --color-error: #ef4444;
            @endif

            @if(isset($colors['info']))
                --color-info: {{ $colors['info'] }};
            @else
                --color-info: #3b82f6;
            @endif
        }

        /* Override Tailwind classes with CSS variables */
        .text-primary-600 { color: var(--color-primary-600) !important; }
        .bg-primary-600 { background-color: var(--color-primary-600) !important; }
        .bg-primary-50 { background-color: var(--color-primary-50) !important; }
        .bg-primary-100 { background-color: var(--color-primary-100) !important; }
        .text-primary-700 { color: var(--color-primary-700) !important; }
        .hover\:bg-primary-700:hover { background-color: var(--color-primary-700) !important; }
        .hover\:text-primary-600:hover { color: var(--color-primary-600) !important; }
        .border-primary-600 { border-color: var(--color-primary-600) !important; }
        .ring-primary-500 { --tw-ring-color: var(--color-primary-500) !important; }
        .focus\:ring-primary-500:focus { --tw-ring-color: var(--color-primary-500) !important; }
        .focus\:border-primary-500:focus { border-color: var(--color-primary-500) !important; }

        .text-secondary-600 { color: var(--color-secondary-600) !important; }
        .bg-secondary-100 { background-color: var(--color-secondary-100) !important; }
        .hover\:bg-secondary-200:hover { background-color: var(--color-secondary-200) !important; }

        .text-accent-600 { color: var(--color-accent-600) !important; }
        .bg-accent-50 { background-color: var(--color-accent-50) !important; }
        .bg-accent-100 { background-color: var(--color-accent-100) !important; }

        .text-success { color: var(--color-success) !important; }
        .bg-success { background-color: var(--color-success) !important; }
        .text-warning { color: var(--color-warning) !important; }
        .bg-warning { background-color: var(--color-warning) !important; }
        .text-error { color: var(--color-error) !important; }
        .bg-error { background-color: var(--color-error) !important; }
        .text-info { color: var(--color-info) !important; }
        .bg-info { background-color: var(--color-info) !important; }

        .sidebar-transition {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg sidebar-transition transform -translate-x-full lg:translate-x-0">
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-lg font-semibold text-gray-900">{{ tenant('data.name') ?? 'School ERP' }}</h1>
                        <p class="text-xs text-gray-500">Management System</p>
                    </div>
                </div>
                <button id="sidebar-toggle" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('tenant.admin.dashboard', ['tenant' => request()->route('tenant')]) }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tenant.admin.dashboard') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('tenant.admin.dashboard') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Students -->
                    <a href="{{ route('tenant.admin.students.index', ['tenant' => request()->route('tenant')]) }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tenant.admin.students.*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('tenant.admin.students.*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                        Students
                    </a>

                    <!-- Teachers -->
                    <a href="{{ route('tenant.admin.teachers.index', ['tenant' => request()->route('tenant')]) }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tenant.admin.teachers.*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('tenant.admin.teachers.*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Teachers
                    </a>

                    <!-- Classes -->
                    <a href="{{ route('tenant.admin.classes.index', ['tenant' => request()->route('tenant')]) }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tenant.admin.classes.*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('tenant.admin.classes.*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Classes
                    </a>

                    <!-- Attendance -->
                    <a href="{{ route('tenant.admin.attendance.index', ['tenant' => request()->route('tenant')]) }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tenant.admin.attendance.*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('tenant.admin.attendance.*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Attendance
                    </a>

                    <!-- Grades -->
                    <a href="{{ route('tenant.admin.grades.index', ['tenant' => request()->route('tenant')]) }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tenant.admin.grades.*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('tenant.admin.grades.*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Grades
                    </a>

                    <!-- Reports -->
                    <div class="pt-4">
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Reports</h3>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('tenant.admin.reports.attendance', ['tenant' => request()->route('tenant')]) }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tenant.admin.reports.*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('tenant.admin.reports.*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Reports
                            </a>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="pt-4">
                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Settings</h3>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('tenant.admin.settings.index', ['tenant' => request()->route('tenant')]) }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tenant.admin.settings.*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('tenant.admin.settings.*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Settings
                            </a>
                            <a href="{{ route('tenant.admin.color-palettes.index', ['tenant' => request()->route('tenant')]) }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->routeIs('tenant.admin.color-palettes.*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('tenant.admin.color-palettes.*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                                </svg>
                                Color Themes
                            </a>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="lg:pl-64">
            <!-- Top Navigation -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <button id="mobile-menu-button" type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1"></div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- User Menu -->
                        <div class="relative">
                            <div class="flex items-center space-x-3">
                                <!-- User Avatar -->
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-medium text-white">
                                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="hidden sm:block">
                                        <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Admin' }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', auth()->user()->admin_type ?? 'Admin')) }}</p>
                                    </div>
                                </div>

                                <!-- Logout -->
                                <form method="POST" action="{{ route('tenant.logout', ['tenant' => request()->route('tenant')]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="flex items-center space-x-1 px-3 py-2 text-sm font-medium text-gray-700 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        <span class="hidden sm:inline">Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <main class="py-6">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile sidebar overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden hidden"></div>

    <script>
        // Mobile sidebar toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        // Close sidebar when clicking overlay
        document.getElementById('sidebar-overlay').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        // Close sidebar when clicking close button
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    </script>
</body>
</html>
