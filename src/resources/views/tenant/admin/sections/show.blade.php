@extends('tenant.layouts.admin')

@section('title', $section->schoolClass->class_name . ' - Section ' . $section->section_name)

@section('content')
{{-- @var $section \App\Models\Section --}}
{{-- @var $students \Illuminate\Support\Collection<\App\Models\Student> --}}
{{-- @var $stats array --}}
{{-- @var $tenant \App\Models\Tenant --}}
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $section->schoolClass->class_name }} - Section {{ $section->section_name }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ $section->room_number ? 'Room: ' . $section->room_number : '' }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex flex-wrap gap-3">
            <a href="{{ url('/admin/sections/' . $section->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Edit
            </a>
            <a href="{{ url('/admin/sections') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
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

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Capacity</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['capacity'] ?: 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Available Seats</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['available_seats'] ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Utilization</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['utilization'] ? $stats['utilization'] . '%' : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Info -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Section Information</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Class</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $section->schoolClass->class_name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Section</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $section->section_name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Room Number</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $section->room_number ?: 'Not Set' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Class Teacher</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $section->classTeacher?->name ?: 'Not Assigned' }}</dd>
            </div>
        </dl>
    </div>

    <!-- Students List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Students</h2>
        </div>

        @if($students->count() > 0)
        <!-- Desktop: Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Admission No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Roll No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($students as $student)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->admission_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->currentEnrollment?->roll_number ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ url('/admin/students/' . $student->id) }}" class="text-primary-600 hover:text-primary-900">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile: Card View -->
        <div class="md:hidden divide-y divide-gray-200">
            @foreach($students as $student)
            <div class="p-4 hover:bg-gray-50">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-3 mb-2">
                            <div class="flex-shrink-0 h-10 w-10 bg-primary-100 rounded-full flex items-center justify-center">
                                <span class="text-primary-600 font-semibold text-sm">{{ strtoupper(substr($student->full_name, 0, 1)) }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $student->full_name }}</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Admission: {{ $student->admission_number }}</p>
                            </div>
                        </div>
                        <div class="ml-13 space-y-1">
                            <div class="flex items-center text-sm text-gray-600">
                                <span class="text-gray-500 w-20">Roll No:</span>
                                <span class="font-medium text-gray-900">{{ $student->currentEnrollment?->roll_number ?? '-' }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-500 w-20">Status:</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Active
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-shrink-0 ml-4">
                        <a href="{{ url('/admin/students/' . $student->id) }}" class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                            View
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="px-4 sm:px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
            <p class="mt-2 text-sm text-gray-500">No students enrolled in this section</p>
        </div>
        @endif
    </div>
</div>
@endsection

