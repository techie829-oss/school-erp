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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Details</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $class->class_name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Class {{ $class->class_numeric }} - {{ ucfirst($class->class_type) }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
            <a href="{{ url('/admin/classes/' . $class->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Edit Class
            </a>
            <a href="{{ url('/admin/classes') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back to List
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
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

        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Students</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_students'] }}</p>
                </div>
            </div>
        </div>

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
                </div>
            </div>
        </div>
    </div>

    <!-- Sections List -->
    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h2 class="text-lg font-medium text-gray-900">Sections</h2>
            <a href="{{ url('/admin/sections/create?class_id=' . $class->id) }}" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Section
            </a>
        </div>

        @if($class->sections->count() > 0)
        <!-- Desktop: Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Section</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class Teacher</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Students</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($class->sections as $section)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $section->section_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $section->room_number ?: '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $section->classTeacher?->name ?: 'Not Assigned' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $section->capacity ?: 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $section->enrollments()->where('is_current', true)->count() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $section->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $section->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-3">
                                <a href="{{ url('/admin/sections/' . $section->id) }}" class="text-primary-600 hover:text-primary-900">View</a>
                                <a href="{{ url('/admin/sections/' . $section->id . '/edit') }}" class="text-gray-600 hover:text-gray-900">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile: Card View -->
        <div class="md:hidden divide-y divide-gray-200">
            @foreach($class->sections as $section)
            <div class="p-4 hover:bg-gray-50">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="flex-shrink-0 h-10 w-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <span class="text-purple-600 font-semibold text-sm">{{ strtoupper(substr($section->section_name, 0, 1)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-gray-900">{{ $section->section_name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Section</p>
                            </div>
                        </div>
                        <div class="ml-13 space-y-1">
                            <div class="flex items-center text-sm text-gray-600">
                                <span class="text-gray-500 w-20">Room:</span>
                                <span class="font-medium text-gray-900">{{ $section->room_number ?: 'Not assigned' }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <span class="text-gray-500 w-20">Teacher:</span>
                                <span class="font-medium text-gray-900">{{ $section->classTeacher?->name ?: 'Not assigned' }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <span class="text-gray-500 w-20">Capacity:</span>
                                <span class="font-medium text-gray-900">{{ $section->capacity ?: 'N/A' }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-500 w-20">Students:</span>
                                <span class="font-medium text-primary-600">{{ $section->enrollments()->where('is_current', true)->count() }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-500 w-20">Status:</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $section->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $section->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0 ml-4 flex flex-col items-end space-y-2">
                        <a href="{{ url('/admin/sections/' . $section->id) }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                            View
                        </a>
                        <a href="{{ url('/admin/sections/' . $section->id . '/edit') }}" class="text-gray-600 hover:text-gray-900 text-sm font-medium">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-4 sm:px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500">No sections created yet</p>
        </div>
        @endif
    </div>
</div>
@endsection

