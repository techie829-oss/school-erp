<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CMS - @yield('title', 'Content Management') - {{ $cmsSiteName ?? (($tenantSubdomain ?? tenant('data.subdomain')) ? ucfirst($tenantSubdomain ?? tenant('data.subdomain')) : (tenant('data.name') ?? 'School Management')) }}</title>

    @if($cmsFavicon ?? null)
        <link rel="icon" type="image/x-icon" href="{{ $cmsFavicon }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dynamic Tenant Colors (Same as admin) -->
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
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg sidebar-transition transform -translate-x-full lg:translate-x-0 overflow-y-auto">
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 sticky top-0 bg-white z-10">
                <div class="flex items-center">
                    @if($cmsLogo ?? null)
                        <img src="{{ $cmsLogo }}" alt="{{ $cmsSiteName ?? 'Logo' }}" class="h-8 w-auto max-w-[120px] object-contain">
                    @else
                        <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="ml-3">
                        <h1 class="text-lg font-semibold text-gray-900">{{ $tenantSubdomain ? ucfirst($tenantSubdomain) : (tenant('data.subdomain') ? ucfirst(tenant('data.subdomain')) : 'School ERP') }}</h1>
                        <p class="text-xs text-gray-500">{{ $cmsSiteTagline ?? 'CMS System' }}</p>
                    </div>
                </div>
                <button id="sidebar-toggle" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- CMS Navigation -->
            <nav class="mt-6 px-3 pb-6">
                <div class="space-y-1">
                    {{-- CMS DASHBOARD --}}
                    <a href="{{ url('/admin/cms') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/cms') && !request()->is('*/admin/cms/*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-home class="mr-3 h-5 w-5 {{ request()->is('*/admin/cms') && !request()->is('*/admin/cms/*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        CMS Dashboard
                    </a>

                    {{-- CMS SECTION --}}
                    @php
                        $cmsActive = request()->is('*/admin/cms/pages*') || request()->is('*/admin/cms/posts*') || request()->is('*/admin/cms/media*');
                    @endphp
                    <div class="sidebar-section" data-section="cms">
                        <button type="button" class="sidebar-section-header w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:bg-gray-50 rounded-md transition-colors" onclick="toggleSection('cms')">
                            <span>Content</span>
                            <svg class="section-icon h-4 w-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="sidebar-section-content {{ $cmsActive ? '' : 'hidden' }}" data-content="cms">
                            {{-- Pages --}}
                            <a href="{{ url('/admin/cms/pages') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/cms/pages*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <x-heroicon-o-document-text class="mr-3 h-4 w-4 {{ request()->is('*/admin/cms/pages*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                                Pages
                            </a>

                            {{-- Blog/Posts --}}
                            <a href="{{ url('/admin/cms/posts') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/cms/posts*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <x-heroicon-o-newspaper class="mr-3 h-4 w-4 {{ request()->is('*/admin/cms/posts*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                                Blog/News
                            </a>

                            {{-- Media Library --}}
                            <a href="{{ url('/admin/cms/media') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/cms/media*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <x-heroicon-o-photo class="mr-3 h-4 w-4 {{ request()->is('*/admin/cms/media*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                                Media Library
                            </a>
                        </div>
                    </div>

                    {{-- DESIGN SECTION --}}
                    @php
                        $designActive = request()->is('*/admin/cms/menus*') || request()->is('*/admin/cms/sliders*') || request()->is('*/admin/cms/galleries*');
                    @endphp
                    <div class="sidebar-section" data-section="design">
                        <button type="button" class="sidebar-section-header w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:bg-gray-50 rounded-md transition-colors" onclick="toggleSection('design')">
                            <span>Design</span>
                            <svg class="section-icon h-4 w-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="sidebar-section-content {{ $designActive ? '' : 'hidden' }}" data-content="design">
                            {{-- Menus --}}
                            <a href="{{ url('/admin/cms/menus') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/cms/menus*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <x-heroicon-o-bars-3 class="mr-3 h-4 w-4 {{ request()->is('*/admin/cms/menus*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                                Menus
                            </a>

                            {{-- Sliders --}}
                            <a href="{{ url('/admin/cms/sliders') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/cms/sliders*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <x-heroicon-o-photo class="mr-3 h-4 w-4 {{ request()->is('*/admin/cms/sliders*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                                Sliders
                            </a>

                            {{-- Galleries --}}
                            <a href="{{ url('/admin/cms/galleries') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/cms/galleries*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <x-heroicon-o-squares-2x2 class="mr-3 h-4 w-4 {{ request()->is('*/admin/cms/galleries*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                                Galleries
                            </a>
                        </div>
                    </div>

                    {{-- SETTINGS SECTION --}}
                    @php
                        $settingsActive = request()->is('*/admin/cms/settings*') || request()->is('*/admin/cms/seo*') || request()->is('*/admin/cms/faqs*') || request()->is('*/admin/cms/testimonials*');
                    @endphp
                    <div class="sidebar-section" data-section="cms-settings">
                        <button type="button" class="sidebar-section-header w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:bg-gray-50 rounded-md transition-colors" onclick="toggleSection('cms-settings')">
                            <span>Settings</span>
                            <svg class="section-icon h-4 w-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="sidebar-section-content {{ $settingsActive ? '' : 'hidden' }}" data-content="cms-settings">
                            {{-- CMS Settings --}}
                            <a href="{{ url('/admin/cms/settings') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/cms/settings*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <x-heroicon-o-cog-6-tooth class="mr-3 h-4 w-4 {{ request()->is('*/admin/cms/settings*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                                Settings
                            </a>

                            {{-- SEO --}}
                            <a href="{{ url('/admin/cms/seo') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/cms/seo*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <x-heroicon-o-magnifying-glass class="mr-3 h-4 w-4 {{ request()->is('*/admin/cms/seo*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                                SEO
                            </a>

                            {{-- FAQs --}}
                            <a href="{{ url('/admin/cms/faqs') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/cms/faqs*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <x-heroicon-o-question-mark-circle class="mr-3 h-4 w-4 {{ request()->is('*/admin/cms/faqs*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                                FAQs
                            </a>

                            {{-- Testimonials --}}
                            <a href="{{ url('/admin/cms/testimonials') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/cms/testimonials*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                                <x-heroicon-o-star class="mr-3 h-4 w-4 {{ request()->is('*/admin/cms/testimonials*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                                Testimonials
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
                    <div class="flex flex-1 items-center">
                        <!-- Full Site Name in Top Bar -->
                        <div class="hidden md:block">
                            <h2 class="text-lg font-semibold text-gray-900">{{ $cmsSiteName ?? tenant('data.name') ?? 'School ERP' }}</h2>
                        </div>
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <!-- CMS Toggle Switch (2-Way) -->
                        <div class="flex items-center bg-gray-100 rounded-lg p-1">
                            <button type="button"
                                    id="admin-mode-btn"
                                    onclick="window.location.href='{{ url('/admin/dashboard') }}'"
                                    class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-600 hover:text-gray-900">
                                Admin
                            </button>
                            <button type="button"
                                    id="cms-mode-btn"
                                    onclick="window.location.href='{{ url('/admin/cms') }}'"
                                    class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 bg-white text-primary-600 shadow-sm">
                                CMS
                            </button>
                        </div>

                        <!-- User Menu Dropdown -->
                        <div class="relative">
                            <button id="user-menu-button" type="button" class="flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 rounded-lg p-1">
                                <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">
                                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                                    </span>
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Admin' }}</p>
                                    <p class="text-xs text-gray-500">CMS Manager</p>
                                </div>
                                <svg id="user-menu-arrow" class="w-4 h-4 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div id="user-menu-dropdown" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <a href="{{ url('/admin/profile/change-password') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                        Change Password
                                    </a>
                                    <form method="POST" action="{{ url('/logout') }}">
                                        @csrf
                                        <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600">
                                            <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            </div>

            <!-- Page Content -->
            <main class="py-6">
                <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8 content-overflow-visible">
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

        // CMS Toggle (2-Way Switch - handled by onclick on buttons)
        document.addEventListener('DOMContentLoaded', function() {

            // User Menu Dropdown
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenuDropdown = document.getElementById('user-menu-dropdown');
            const userMenuArrow = document.getElementById('user-menu-arrow');

            if (userMenuButton && userMenuDropdown) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userMenuDropdown.classList.toggle('hidden');
                    userMenuArrow.classList.toggle('rotate-180');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userMenuButton.contains(e.target) && !userMenuDropdown.contains(e.target)) {
                        userMenuDropdown.classList.add('hidden');
                        userMenuArrow.classList.remove('rotate-180');
                    }
                });
            }
        });

        // Collapsible sidebar sections
        function toggleSection(sectionName) {
            const section = document.querySelector(`[data-section="${sectionName}"]`);
            const content = section.querySelector(`[data-content="${sectionName}"]`);
            const icon = section.querySelector('.section-icon');

            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');

            // Save state to localStorage
            const isOpen = !content.classList.contains('hidden');
            localStorage.setItem(`cms-section-${sectionName}`, isOpen ? 'open' : 'closed');
        }

        // Restore section states from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.sidebar-section').forEach(section => {
                const sectionName = section.getAttribute('data-section');
                const savedState = localStorage.getItem(`cms-section-${sectionName}`);
                const content = section.querySelector(`[data-content="${sectionName}"]`);
                const icon = section.querySelector('.section-icon');

                if (savedState === 'open') {
                    content.classList.remove('hidden');
                    icon.classList.add('rotate-180');
                }
            });
        });
    </script>
</body>
</html>

