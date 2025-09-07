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
        <div class="min-h-screen">
            @yield('content')
        </div>
    </body>
</html>
