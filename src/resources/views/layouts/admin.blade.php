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
            overflow-x: hidden;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--color-primary-600) 0%, var(--color-primary-900) 100%);
            transition: transform 0.3s ease-in-out;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 50;
            width: 16rem;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
            display: none;
        }

        .sidebar-overlay.show {
            display: block;
        }

        .content-area {
            min-height: 100vh;
            margin-left: 16rem;
            transition: margin-left 0.3s ease-in-out;
        }

        .content-area.sidebar-collapsed {
            margin-left: 0;
        }

        .top-header {
            position: sticky;
            top: 0;
            z-index: 30;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            backdrop-filter: blur(10px);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
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

        /* Mobile responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .content-area {
                margin-left: 0;
            }
        }

        /* Tablet responsive */
        @media (min-width: 769px) and (max-width: 1024px) {
            .sidebar {
                width: 14rem;
            }

            .content-area {
                margin-left: 14rem;
            }
        }
    </style>
</head>

<body class="font-sans antialiased">
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="p-6">
            <!-- Logo -->
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-primary-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h1 class="text-xl font-bold text-white">School ERP</h1>
                </div>
                <!-- Close button for mobile -->
                <button class="lg:hidden text-white hover:text-gray-300" onclick="toggleSidebar()">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'text-white bg-white bg-opacity-20' : 'text-purple-200 hover:text-white hover:bg-white hover:bg-opacity-10' }} rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.users.*') ? 'text-white bg-white bg-opacity-20' : 'text-purple-200 hover:text-white hover:bg-white hover:bg-opacity-10' }} rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                    Admin Users
                </a>

                <a href="{{ route('admin.tenants.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.tenants.*') ? 'text-white bg-white bg-opacity-20' : 'text-purple-200 hover:text-white hover:bg-white hover:bg-opacity-10' }} rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Tenants
                </a>

                <a href="{{ route('admin.vhost.index') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.vhost.*') ? 'text-white bg-white bg-opacity-20' : 'text-purple-200 hover:text-white hover:bg-white hover:bg-opacity-10' }} rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                    Vhost Management
                </a>

                <a href="{{ route('admin.system.overview') }}" class="flex items-center px-4 py-3 {{ request()->routeIs('admin.system.overview') ? 'text-white bg-white bg-opacity-20' : 'text-purple-200 hover:text-white hover:bg-white hover:bg-opacity-10' }} rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    System Overview
                </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content-area" id="contentArea">
        <!-- Top Header -->
        <header class="top-header">
            <div class="px-4 sm:px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <!-- Single menu button for all screen sizes -->
                        <button class="mr-4 text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-opacity-50 rounded-md p-1" onclick="toggleSidebar()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">@yield('page-title', 'Dashboard')</h2>
                            <p class="text-sm text-gray-600 mt-1 hidden sm:block">@yield('page-description', 'Welcome to your admin dashboard')</p>
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="relative">
                        <button id="userMenuButton" class="flex items-center space-x-3 p-2 text-sm text-gray-700 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-opacity-50">
                            <!-- User Avatar -->
                            <div class="relative">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center shadow-md">
                                    <span class="text-white font-semibold text-sm">
                                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                                    </span>
                                </div>
                                <!-- Online indicator -->
                                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></div>
                            </div>

                            <!-- User Info -->
                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Admin User' }}</div>
                                <div class="text-xs text-gray-500">{{ auth()->user()->email ?? 'admin@example.com' }}</div>
                            </div>

                            <!-- Dropdown Arrow -->
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div id="userDropdown" class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50 hidden">
                            <!-- User Info Header -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center shadow-md">
                                        <span class="text-white font-semibold text-sm">
                                            {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Admin User' }}</div>
                                        <div class="text-xs text-gray-500">{{ auth()->user()->email ?? 'admin@example.com' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Menu Items -->
                            <div class="py-2">
                                <!-- Profile -->
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Profile
                                </a>

                                <!-- Settings -->
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Settings
                                </a>

                                <!-- Notifications -->
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <div class="relative w-4 h-4 mr-3">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4.5 19.5h15a2 2 0 002-2v-6.5a2 2 0 00-2-2h-15a2 2 0 00-2 2v6.5a2 2 0 002 2z"/>
                                        </svg>
                                        <!-- Notification badge -->
                                        <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 text-white text-xs rounded-full flex items-center justify-center text-[10px]">3</span>
                                    </div>
                                    Notifications
                                    <span class="ml-auto bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">3</span>
                                </a>

                                <!-- Help & Support -->
                                <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Help & Support
                                </a>
                            </div>

                            <!-- Divider -->
                            <div class="border-t border-gray-100"></div>

                            <!-- Logout -->
                            <div class="py-2">
                                <form method="POST" action="{{ route('admin.logout') }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-4 sm:p-6">
            @yield('content')
        </main>
    </div>

    <script>
        // Universal sidebar toggle function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const contentArea = document.getElementById('contentArea');

            // Check if we're on mobile or desktop
            if (window.innerWidth <= 768) {
                // Mobile behavior
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            } else {
                // Desktop behavior
                sidebar.classList.toggle('collapsed');
                contentArea.classList.toggle('sidebar-collapsed');
            }
        }

        // Close sidebar when clicking overlay (mobile only)
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                toggleSidebar();
            }
        });

        // Close sidebar on mobile when clicking nav links
        document.querySelectorAll('.sidebar nav a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 768) {
                    toggleSidebar();
                }
            });
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const contentArea = document.getElementById('contentArea');

            if (window.innerWidth > 768) {
                // Desktop: remove mobile classes
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            } else {
                // Mobile: remove desktop classes
                sidebar.classList.remove('collapsed');
                contentArea.classList.remove('sidebar-collapsed');
            }
        });

        // User dropdown functionality
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const userMenuButton = document.getElementById('userMenuButton');
            const userDropdown = document.getElementById('userDropdown');

            if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.add('hidden');
            }
        });

        // Initialize sidebar state based on screen size
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth <= 768) {
                // Mobile: sidebar should be hidden by default
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.remove('show');
            }

            // Add click event to user menu button
            document.getElementById('userMenuButton').addEventListener('click', function(e) {
                e.stopPropagation();
                toggleUserDropdown();
            });
        });
    </script>
</body>
</html>

