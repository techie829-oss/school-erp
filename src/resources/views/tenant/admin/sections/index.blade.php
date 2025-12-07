@extends('tenant.layouts.admin')

@section('title', 'Sections - ' . ($tenant->data['school_name'] ?? 'School'))

@section('content')
{{-- @var $sections \Illuminate\Pagination\LengthAwarePaginator<\App\Models\Section> --}}
{{-- @var $section \App\Models\Section --}}
{{-- @var $classes \Illuminate\Support\Collection<\App\Models\SchoolClass> --}}
{{-- @var $tenant \App\Models\Tenant --}}
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Sections</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Sections
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Manage class sections and assignments
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/sections/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add New Section
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Filters & Search -->
    <div class="bg-white shadow rounded-lg p-4">
        <form method="GET" action="{{ url('/admin/sections') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Section name, room number..."
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <!-- Class Filter -->
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                    <select name="class_id" id="class_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex justify-end space-x-3">
                <a href="{{ url('/admin/sections') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Clear
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Sections List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="divide-y divide-gray-200">
            @forelse($sections as $section)
            <div class="p-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <!-- Left Section: Section Info -->
                    <div class="flex items-center space-x-4 flex-1 min-w-0">
                        <!-- Section Icon -->
                        <div class="flex-shrink-0 h-12 w-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-lg">{{ strtoupper(substr($section->section_name, 0, 1)) }}</span>
                        </div>

                        <!-- Section Details -->
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-semibold text-gray-900">{{ $section->section_name }}</h3>
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $section->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $section->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $section->schoolClass->class_name }}</p>

                            <!-- Additional Info (Mobile: Stack, Desktop: Inline) -->
                            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-gray-400 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                    </svg>
                                    <span class="text-gray-500">Room:</span>
                                    <span class="ml-1 font-medium text-gray-900">{{ $section->room_number ?: 'Not assigned' }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-gray-400 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-gray-500">Teacher:</span>
                                    <span class="ml-1 font-medium text-gray-900">{{ $section->classTeacher?->name ?: 'Not assigned' }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-gray-400 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span class="text-gray-500">Capacity:</span>
                                    <span class="ml-1 font-medium text-gray-900">{{ $section->capacity ?: 'N/A' }}</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="h-4 w-4 text-gray-400 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <span class="text-gray-500">Students:</span>
                                    <span class="ml-1 font-medium text-primary-600">{{ $section->enrollments()->where('is_current', true)->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section: Actions -->
                    <div class="flex-shrink-0 flex items-center justify-end space-x-3 md:ml-4">
                        <a href="{{ url('/admin/sections/' . $section->id) }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                            View
                        </a>
                        <a href="{{ url('/admin/sections/' . $section->id . '/edit') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                            Edit
                        </a>
                        <form action="{{ url('/admin/sections/' . $section->id) }}" method="POST" class="inline"
                            onsubmit="return confirm('Are you sure you want to delete this section?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12">
                <div class="text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5z"/>
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900">No sections found</h3>
                    <p class="mt-2 text-sm text-gray-500">Get started by creating your first section</p>
                    <div class="mt-6">
                        <a href="{{ url('/admin/sections/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add New Section
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if($sections->hasPages())
    <div class="mt-6 bg-white px-4 py-3 border border-gray-200 rounded-lg shadow sm:px-6">
        {{ $sections->links() }}
    </div>
    @endif
</div>
@endsection
