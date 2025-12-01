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
        /* Allow main content to show overflow on large screens to avoid unnecessary horizontal scrollbar
           but keep scrolling on small screens via existing overflow wrappers. */
        @media (min-width: 1024px) {
            .content-overflow-visible {
                overflow-x: visible !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg sidebar-transition transform -translate-x-full lg:translate-x-0 overflow-y-auto">
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 sticky top-0 bg-white z-10">
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
            <nav class="mt-6 px-3 pb-6">
                <div class="space-y-1">
                    {{-- DASHBOARD --}}
                    <a href="{{ url('/admin/dashboard') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/dashboard') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-home class="mr-3 h-5 w-5 {{ request()->is('*/admin/dashboard') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Dashboard
                    </a>

                    {{-- ACADEMICS HEADER --}}
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Academics</p>
                    </div>

                    {{-- Classes --}}
                    @if(($featureSettings['classes'] ?? true))
                    <a href="{{ url('/admin/classes') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/classes*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-academic-cap class="mr-3 h-5 w-5 {{ request()->is('*/admin/classes*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Classes
                    </a>
                    @endif

                    {{-- Sections --}}
                    <a href="{{ url('/admin/sections') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/sections*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-squares-2x2 class="mr-3 h-5 w-5 {{ request()->is('*/admin/sections*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Sections
                    </a>

                    {{-- Subjects --}}
                    <a href="{{ url('/admin/subjects') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/subjects*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-book-open class="mr-3 h-5 w-5 {{ request()->is('*/admin/subjects*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Subjects
                    </a>

                    {{-- Departments --}}
                    <a href="{{ url('/admin/departments') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/departments*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-building-office class="mr-3 h-5 w-5 {{ request()->is('*/admin/departments*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Departments
                    </a>

                    {{-- STUDENTS HEADER --}}
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Students</p>
                    </div>

                    {{-- Students --}}
                    @if(($featureSettings['students'] ?? true))
                    <a href="{{ url('/admin/students') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/students*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-user-group class="mr-3 h-5 w-5 {{ request()->is('*/admin/students*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Students
                    </a>
                    @endif

                    {{-- Student Attendance --}}
                    @if(($featureSettings['attendance'] ?? true))
                    <a href="{{ url('/admin/attendance/students') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/attendance/students') && !request()->is('*/admin/attendance/students/report') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-clipboard-document-check class="mr-3 h-4 w-4 {{ request()->is('*/admin/attendance/students') && !request()->is('*/admin/attendance/students/report') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Attendance
                    </a>

                    {{-- Student Attendance Reports --}}
                    <a href="{{ url('/admin/attendance/students/report') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/attendance/students/report*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-chart-bar class="mr-3 h-4 w-4 {{ request()->is('*/admin/attendance/students/report*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Reports
                    </a>

                    {{-- Holiday Management --}}
                    <a href="{{ url('/admin/attendance/holidays') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/attendance/holidays*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-calendar-days class="mr-3 h-4 w-4 {{ request()->is('*/admin/attendance/holidays*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Holidays
                    </a>
                    @endif

                    {{-- DIVIDER --}}
                    <div class="my-4 border-t border-gray-200"></div>

                    {{-- HR & STAFF HEADER --}}
                    <div class="pt-0 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">HR & Staff</p>
                    </div>

                    {{-- Teachers --}}
                    @if(($featureSettings['teachers'] ?? true))
                    <a href="{{ url('/admin/teachers') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/teachers*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-users class="mr-3 h-5 w-5 {{ request()->is('*/admin/teachers*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Teachers
                    </a>
                    @endif

                    {{-- Teacher Attendance --}}
                    @if(($featureSettings['attendance'] ?? true))
                    <a href="{{ url('/admin/attendance/teachers') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/attendance/teachers') && !request()->is('*/admin/attendance/teachers/report') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-clock class="mr-3 h-4 w-4 {{ request()->is('*/admin/attendance/teachers') && !request()->is('*/admin/attendance/teachers/report') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Attendance
                    </a>

                    {{-- Teacher Attendance Reports --}}
                    <a href="{{ url('/admin/attendance/teachers/report') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/attendance/teachers/report*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-chart-bar class="mr-3 h-4 w-4 {{ request()->is('*/admin/attendance/teachers/report*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Reports
                    </a>
                    @endif

                    {{-- LEARNING HEADER --}}
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Learning</p>
                    </div>

                    {{-- Courses (LMS) --}}
                    @if(($featureSettings['assignments'] ?? true))
                    <a href="{{ url('/admin/lms/courses') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/lms/courses*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-book-open class="mr-3 h-5 w-5 {{ request()->is('*/admin/lms/courses*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Courses
                    </a>
                    @endif

                    {{-- EXAMINATIONS HEADER --}}
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Examinations</p>
                    </div>

                    {{-- Grade Scales --}}
                    @if(($featureSettings['grades'] ?? true))
                    <a href="{{ url('/admin/examinations/grade-scales') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/examinations/grade-scales*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-scale class="mr-3 h-4 w-4 {{ request()->is('*/admin/examinations/grade-scales*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Grade Scales
                    </a>
                    @endif

                    {{-- TODO: Implement these examination features --}}
                    {{-- Exams --}}
                    {{-- <a href="{{ url('/admin/examinations/exams') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/examinations/exams*') && !request()->is('*/admin/examinations/exams/*/schedules*') && !request()->is('*/admin/examinations/exams/*/results*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-document-text class="mr-3 h-4 w-4 {{ request()->is('*/admin/examinations/exams*') && !request()->is('*/admin/examinations/exams/*/schedules*') && !request()->is('*/admin/examinations/exams/*/results*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Exams
                    </a> --}}

                    {{-- Exam Schedules --}}
                    {{-- <a href="{{ url('/admin/examinations/schedules') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/examinations/schedules*') || request()->is('*/admin/examinations/exams/*/schedules*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-calendar-days class="mr-3 h-4 w-4 {{ request()->is('*/admin/examinations/schedules*') || request()->is('*/admin/examinations/exams/*/schedules*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Exam Schedules
                    </a> --}}

                    {{-- Results Entry --}}
                    {{-- <a href="{{ url('/admin/examinations/results') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/examinations/results*') || request()->is('*/admin/examinations/exams/*/results*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-document-check class="mr-3 h-4 w-4 {{ request()->is('*/admin/examinations/results*') || request()->is('*/admin/examinations/exams/*/results*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Results Entry
                    </a> --}}

                    {{-- Admit Cards --}}
                    {{-- <a href="{{ url('/admin/examinations/admit-cards') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/examinations/admit-cards*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-identification class="mr-3 h-4 w-4 {{ request()->is('*/admin/examinations/admit-cards*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Admit Cards
                    </a> --}}

                    {{-- Report Cards --}}
                    {{-- <a href="{{ url('/admin/examinations/report-cards') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/examinations/report-cards*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-document-text class="mr-3 h-4 w-4 {{ request()->is('*/admin/examinations/report-cards*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Report Cards
                    </a> --}}

                    {{-- Examination Reports --}}
                    {{-- <a href="{{ url('/admin/examinations/reports') }}" class="group flex items-center px-6 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/examinations/reports*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-chart-bar-square class="mr-3 h-4 w-4 {{ request()->is('*/admin/examinations/reports*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Reports
                    </a> --}}

                    {{-- FINANCE HEADER --}}
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Finance</p>
                    </div>

                    {{-- Fee Collection --}}
                    @if(($featureSettings['fees'] ?? true))
                    <a href="{{ url('/admin/fees/collection') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/fees/collection*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-banknotes class="mr-3 h-5 w-5 {{ request()->is('*/admin/fees/collection*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Fee Collection
                    </a>

                    {{-- Fee Components --}}
                    <a href="{{ url('/admin/fees/components') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/fees/components*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-circle-stack class="mr-3 h-5 w-5 {{ request()->is('*/admin/fees/components*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Fee Components
                    </a>

                    {{-- Fee Plans --}}
                    <a href="{{ url('/admin/fees/plans') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/fees/plans*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-document-text class="mr-3 h-5 w-5 {{ request()->is('*/admin/fees/plans*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Fee Plans
                    </a>

                    {{-- Fee Reports --}}
                    <a href="{{ url('/admin/fees/reports') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/fees/reports*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-chart-bar-square class="mr-3 h-5 w-5 {{ request()->is('*/admin/fees/reports*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Fee Reports
                    </a>
                    @endif

                    {{-- SETTINGS HEADER --}}
                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Settings</p>
                    </div>

                    {{-- Settings --}}
                    <a href="{{ url('/admin/settings') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/settings*') ? 'bg-primary-100 text-primary-700' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-cog-6-tooth class="mr-3 h-5 w-5 {{ request()->is('*/admin/settings*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Settings
                    </a>

                    {{-- Notification Logs --}}
                    <a href="{{ url('/admin/notifications/logs') }}" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md {{ request()->is('*/admin/notifications/logs*') ? 'bg-primary-100 text-primary-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                        <x-heroicon-o-chart-bar-square class="mr-3 h-4 w-4 {{ request()->is('*/admin/notifications/logs*') ? 'text-primary-500' : 'text-gray-400 group-hover:text-gray-500' }}" />
                        Notification Logs
                    </a>
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
                                <form method="POST" action="{{ url('/logout') }}" class="inline">
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
                <!-- Allow content to use the full available width (previously limited by max-w-7xl) -->
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
    </script>
</body>
</html>
