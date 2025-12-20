@extends('tenant.layouts.admin')

@section('title', $class->class_name . ' - Class Details')

@section('content')
{{-- @var $class \App\Models\SchoolClass --}}
{{-- @var $stats array --}}
{{-- @var $tenant \App\Models\Tenant --}}
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ url('/admin/classes') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Classes</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $class->class_name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $class->class_name }}</h1>
                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $class->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $class->is_active ? 'Active' : 'Inactive' }}
                </span>
                @if($class->has_sections)
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800">
                    Has Sections
                </span>
                @endif
            </div>
            <p class="mt-1 text-sm text-gray-500">Class {{ $class->class_numeric }} - {{ ucfirst($class->class_type) }}</p>
            @if($class->description)
            <p class="mt-2 text-sm text-gray-600">{{ $class->description }}</p>
            @endif
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
            <a href="{{ url('/admin/classes/' . $class->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Class
            </a>
            <a href="{{ url('/admin/classes') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    @php
        // Determine if class has sections (either by flag or actual sections exist)
        $hasSections = $class->has_sections || $class->sections->count() > 0;
        $showSectionStats = $hasSections;
        $statsCols = $showSectionStats ? 'lg:grid-cols-4' : 'lg:grid-cols-2';

        // Calculate enrollment percentage for sections
        $enrollmentPercentage = 0;
        if ($hasSections && $stats['total_capacity'] > 0) {
            $enrollmentPercentage = round(($stats['total_students'] / $stats['total_capacity']) * 100, 1);
        } elseif (!$hasSections && $class->capacity > 0) {
            $enrollmentPercentage = round(($stats['total_students'] / $class->capacity) * 100, 1);
        }
    @endphp

    <!-- Class Information -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Class Information</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Class Name</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $class->class_name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Class Number</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $class->class_numeric }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Class Type</dt>
                <dd class="mt-1">
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $class->class_type == 'school' ? 'bg-blue-100 text-blue-800' : ($class->class_type == 'college' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                        {{ ucfirst($class->class_type) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $class->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $class->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Has Sections</dt>
                <dd class="mt-1">
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $class->has_sections ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $class->has_sections ? 'Yes' : 'No' }}
                    </span>
                </dd>
            </div>
            @if(!$hasSections)
            <div>
                <dt class="text-sm font-medium text-gray-500">Room Number</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $class->room_number ?: 'Not Set' }}</dd>
            </div>
            @endif
            @if(!$hasSections && $class->capacity)
            <div>
                <dt class="text-sm font-medium text-gray-500">Class Capacity</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $class->capacity }}</dd>
            </div>
            @endif
            @if($class->description)
            <div class="sm:col-span-2 lg:col-span-3">
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $class->description }}</dd>
            </div>
            @endif
        </dl>
    </div>

    <!-- Class Teacher Information -->
    @if(!$hasSections)
        @if($class->classTeacher)
        <div class="bg-white shadow rounded-lg p-4 sm:p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Class Teacher</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $class->classTeacher->full_name }}</dd>
                </div>
                @if($class->classTeacher->employee_id)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Employee ID</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $class->classTeacher->employee_id }}</dd>
                </div>
                @endif
                @if($class->classTeacher->department)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Department</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $class->classTeacher->department->department_name }}</dd>
                </div>
                @endif
                @if($class->classTeacher->email)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <a href="mailto:{{ $class->classTeacher->email }}" class="text-primary-600 hover:text-primary-900">{{ $class->classTeacher->email }}</a>
                    </dd>
                </div>
                @endif
                @if($class->classTeacher->phone)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <a href="tel:{{ $class->classTeacher->phone }}" class="text-primary-600 hover:text-primary-900">{{ $class->classTeacher->phone }}</a>
                    </dd>
                </div>
                @endif
                @if($class->classTeacher->designation)
                <div>
                    <dt class="text-sm font-medium text-gray-500">Designation</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $class->classTeacher->designation }}</dd>
                </div>
                @endif
            </div>
            <div class="mt-4">
                <a href="{{ url('/admin/teachers/' . $class->classTeacher->id) }}" class="inline-flex items-center text-sm text-primary-600 hover:text-primary-900">
                    View Full Teacher Profile
                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
        @else
        <div class="bg-white shadow rounded-lg p-4 sm:p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Class Teacher</h2>
            <div class="text-center py-4">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <p class="mt-2 text-sm text-gray-500">No class teacher assigned</p>
                <a href="{{ url('/admin/classes/' . $class->id . '/edit') }}" class="mt-2 inline-flex items-center text-sm text-primary-600 hover:text-primary-900">
                    Assign a class teacher
                </a>
            </div>
        </div>
        @endif
    @else
    <div class="bg-white shadow rounded-lg p-4 sm:p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Class Teacher</h2>
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        This class has sections. Teachers are assigned at the <strong>section level</strong>, not at the class level.
                        View the sections below to see the assigned class teachers for each section.
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    @php
        $hasSections = $class->has_sections || $class->sections->count() > 0;
        $showSectionStats = $hasSections;
        $statsCols = $showSectionStats ? 'lg:grid-cols-4' : 'lg:grid-cols-2';
    @endphp

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 {{ $statsCols }} gap-4 sm:gap-6 mb-6">
        @if($showSectionStats)
        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Sections</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_sections'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Sections</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_sections'] }}</p>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-500">Total Students</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_students'] }}</p>
                    @if($enrollmentPercentage > 0)
                        <p class="text-xs text-gray-500 mt-1">{{ $enrollmentPercentage }}% enrolled</p>
                    @endif
                </div>
            </div>
        </div>

        @if($showSectionStats)
        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Capacity</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_capacity'] ?: 'N/A' }}</p>
                    @if($stats['total_capacity'] && isset($stats['available_seats']))
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['available_seats'] }} seats available</p>
                    @endif
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Class Capacity</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $class->capacity ?: 'N/A' }}</p>
                    @if($class->capacity)
                        @php
                            $available = $class->capacity - $stats['total_students'];
                        @endphp
                        <p class="text-xs text-gray-500 mt-1">{{ $available }} seats available</p>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @if($stats['total_students'] > 0)
            <a href="{{ url('/admin/students?class_id=' . $class->id) }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-primary-300 transition-colors">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">View Students</p>
                    <p class="text-xs text-gray-500">{{ $stats['total_students'] }} enrolled</p>
                </div>
            </a>
            @endif

            <a href="{{ url('/admin/examinations/schedules/select-exam?class_id=' . $class->id) }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-primary-300 transition-colors">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Exam Schedule</p>
                    <p class="text-xs text-gray-500">Create schedule</p>
                </div>
            </a>

            <a href="{{ url('/admin/classes/' . $class->id . '/edit') }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-primary-300 transition-colors">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Edit Class</p>
                    <p class="text-xs text-gray-500">Update details</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Exams Section -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
            <h2 class="text-lg font-medium text-gray-900">Exams</h2>
            <a href="{{ url('/admin/examinations/exams/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create Exam
            </a>
        </div>

        @if($exams->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($exams as $exam)
                    @php
                        $examStats = $exam->class_stats ?? [];
                        $resultsProgress = $examStats['results_progress'] ?? 0;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $exam->exam_name }}</div>
                            @if($exam->academic_year)
                            <div class="text-xs text-gray-500">{{ $exam->academic_year }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('_', ' ', $exam->exam_type)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="text-gray-600">Results</span>
                                    <span class="font-medium text-gray-900">{{ $resultsProgress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div class="bg-primary-600 h-1.5 rounded-full" style="width: {{ $resultsProgress }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $examStats['total_schedules'] ?? 0 }} schedules •
                                    {{ $examStats['students_with_results'] ?? 0 }}/{{ $examStats['total_students'] ?? 0 }} students
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($exam->start_date && $exam->end_date)
                                {{ \Carbon\Carbon::parse($exam->start_date)->format('M d') }} - {{ \Carbon\Carbon::parse($exam->end_date)->format('M d, Y') }}
                            @else
                                <span class="text-gray-400">Not set</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-800',
                                    'scheduled' => 'bg-blue-100 text-blue-800',
                                    'ongoing' => 'bg-yellow-100 text-yellow-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'published' => 'bg-purple-100 text-purple-800',
                                ];
                                $color = $statusColors[$exam->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                {{ ucfirst($exam->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ url('/admin/examinations/exams/' . $exam->id) }}" class="text-primary-600 hover:text-primary-900">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No exams</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new exam for this class.</p>
            <div class="mt-6">
                <a href="{{ url('/admin/examinations/exams/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Exam
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- Sections List -->
    @if($hasSections)
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-4">
            <h2 class="text-lg font-medium text-gray-900">Sections</h2>
            <a href="{{ url('/admin/sections/create?class_id=' . $class->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Section
            </a>
        </div>

        @if($class->sections->count() > 0)
        <div class="space-y-6">
            @foreach($class->sections as $section)
            @php
                $commonSubjects = $class->subjects ?? collect();
                $sectionSpecificSubjects = $section->subjects ?? collect();
                $allSectionSubjects = $commonSubjects->merge($sectionSpecificSubjects)->unique('id');
                $sectionStudentCount = $section->enrollments()->where('is_current', true)->count();
                $sectionAvailableSeats = $section->capacity ? ($section->capacity - $sectionStudentCount) : null;
                $sectionUtilization = $section->capacity ? round(($sectionStudentCount / $section->capacity) * 100, 1) : null;
            @endphp
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <!-- Section Header -->
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <span class="text-indigo-600 font-bold text-lg">{{ strtoupper(substr($section->section_name, 0, 1)) }}</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    Section {{ $section->section_name }}
                                    @if($section->group_name)
                                        <span class="text-sm font-normal text-gray-600">({{ $section->group_name }})</span>
                                    @endif
                                </h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $section->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $section->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                    @if($section->room_number)
                                    <span class="text-xs text-gray-500">Room: {{ $section->room_number }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ url('/admin/sections/' . $section->id . '/edit') }}" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Section Stats -->
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Students</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $sectionStudentCount }}</p>
                            @if($section->capacity)
                                <p class="text-xs text-gray-400">/ {{ $section->capacity }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Capacity</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $section->capacity ?: 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Available</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $sectionAvailableSeats !== null ? $sectionAvailableSeats : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Utilization</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $sectionUtilization ? $sectionUtilization . '%' : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Section Information -->
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Section Information</h4>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @if($section->group_name)
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Group Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $section->group_name }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Room Number</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $section->room_number ?: 'Not Set' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Capacity</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $section->capacity ?: 'Not Set' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Class Teacher</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($section->classTeacher)
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $section->classTeacher->full_name }}</span>
                                        @if($section->classTeacher->employee_id)
                                            <span class="text-xs text-gray-500">ID: {{ $section->classTeacher->employee_id }}</span>
                                        @endif
                                        @if($section->classTeacher->department)
                                            <span class="text-xs text-gray-500">Dept: {{ $section->classTeacher->department->department_name }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">Not Assigned</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Section Subjects -->
                <div class="px-4 sm:px-6 py-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-900">Assigned Subjects</h4>
                        <a href="{{ url('/admin/sections/' . $section->id . '/edit') }}" class="text-xs text-primary-600 hover:text-primary-900">Edit</a>
                    </div>
                    @if($allSectionSubjects->count() > 0)
                        @if($commonSubjects->count() > 0)
                        <div class="mb-4">
                            <h5 class="text-xs font-medium text-gray-700 mb-2">Common Subjects (from Class)</h5>
                            <div class="flex flex-wrap gap-2">
                                @foreach($commonSubjects as $subject)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $subject->subject_name }}
                                    @if($subject->subject_code)
                                        <span class="ml-1 text-blue-600">({{ $subject->subject_code }})</span>
                                    @endif
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($sectionSpecificSubjects->count() > 0)
                        <div>
                            <h5 class="text-xs font-medium text-gray-700 mb-2">Section-Specific Subjects</h5>
                            <div class="flex flex-wrap gap-2">
                                @foreach($sectionSpecificSubjects as $subject)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                    {{ $subject->subject_name }}
                                    @if($subject->subject_code)
                                        <span class="ml-1 text-primary-600">({{ $subject->subject_code }})</span>
                                    @endif
                                </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    @else
                    <div class="text-center py-4">
                        <p class="text-xs text-gray-500">No subjects assigned</p>
                        <a href="{{ url('/admin/sections/' . $section->id . '/edit') }}" class="mt-2 inline-block text-xs text-primary-600 hover:text-primary-900">Assign subjects</a>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white shadow rounded-lg px-4 sm:px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <p class="mt-2 text-sm font-medium text-gray-500">No sections created yet</p>
            <p class="mt-1 text-xs text-gray-400">Create sections to organize students and assign section-specific teachers and subjects</p>
            <a href="{{ url('/admin/sections/create?class_id=' . $class->id) }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create First Section
            </a>
        </div>
        @endif
    </div>
    @endif

    <!-- Assigned Subjects (Only show if class has NO sections) -->
    @if(!$hasSections)
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h2 class="text-lg font-medium text-gray-900">Assigned Subjects</h2>
            <a href="{{ url('/admin/classes/' . $class->id . '/edit') }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit Subjects
            </a>
        </div>

        @if($class->subjects->count() > 0)
        <div class="px-4 sm:px-6 py-4">
            <div class="flex flex-wrap gap-2">
                @foreach($class->subjects as $subject)
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-primary-100 text-primary-800 border border-primary-200">
                    <svg class="w-4 h-4 mr-1.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    {{ $subject->subject_name }}
                    @if($subject->subject_code)
                        <span class="ml-1.5 text-primary-600 font-normal">({{ $subject->subject_code }})</span>
                    @endif
                </span>
                @endforeach
            </div>
        </div>
        @else
        <div class="px-4 sm:px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="mt-2 text-sm font-medium text-gray-500">No subjects assigned to this class</p>
            <p class="mt-1 text-xs text-gray-400">Assign subjects in the edit page to filter them when creating exam schedules and timetables</p>
            <a href="{{ url('/admin/classes/' . $class->id . '/edit') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Assign Subjects
            </a>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection

