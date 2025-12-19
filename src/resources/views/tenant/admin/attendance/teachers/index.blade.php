@extends('tenant.layouts.admin')

@section('title', 'Teacher Attendance')

@section('content')
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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Teacher Attendance</span>
                </div>
            </li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold text-gray-900">Teacher Attendance</h2>
            <p class="mt-1 text-sm text-gray-500">Track and manage teacher attendance</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/attendance/teachers/mark') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Mark Attendance
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow rounded-lg p-5">
            <dt class="text-sm font-medium text-gray-500">Total Staff</dt>
            <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $todayStats['total'] }}</dd>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg p-5">
            <dt class="text-sm font-medium text-gray-500">Present Today</dt>
            <dd class="mt-1 text-2xl font-semibold text-green-600">{{ $todayStats['present'] }}</dd>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg p-5">
            <dt class="text-sm font-medium text-gray-500">Absent Today</dt>
            <dd class="mt-1 text-2xl font-semibold text-red-600">{{ $todayStats['absent'] }}</dd>
        </div>
        <div class="bg-white overflow-hidden shadow rounded-lg p-5">
            <dt class="text-sm font-medium text-gray-500">Attendance %</dt>
            <dd class="mt-1 text-2xl font-semibold text-blue-600">{{ $todayStats['percentage'] }}%</dd>
        </div>
    </div>

    <!-- Monthly Overview -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">This Month's Overview</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            <div class="text-center">
                <dt class="text-sm text-gray-500">Total Records</dt>
                <dd class="text-2xl font-semibold text-gray-900">{{ $monthlyData['total_records'] }}</dd>
            </div>
            <div class="text-center">
                <dt class="text-sm text-gray-500">Present</dt>
                <dd class="text-2xl font-semibold text-green-600">{{ $monthlyData['present'] }}</dd>
            </div>
            <div class="text-center">
                <dt class="text-sm text-gray-500">Absent</dt>
                <dd class="text-2xl font-semibold text-red-600">{{ $monthlyData['absent'] }}</dd>
            </div>
            <div class="text-center">
                <dt class="text-sm text-gray-500">Late</dt>
                <dd class="text-2xl font-semibold text-yellow-600">{{ $monthlyData['late'] ?? 0 }}</dd>
            </div>
            <div class="text-center">
                <dt class="text-sm text-gray-500">Half Day</dt>
                <dd class="text-2xl font-semibold text-blue-600">{{ $monthlyData['half_day'] ?? 0 }}</dd>
            </div>
            <div class="text-center">
                <dt class="text-sm text-gray-500">On Leave</dt>
                <dd class="text-2xl font-semibold text-purple-600">{{ $monthlyData['on_leave'] }}</dd>
            </div>
            <div class="text-center">
                <dt class="text-sm text-gray-500">Avg Hours</dt>
                <dd class="text-2xl font-semibold text-indigo-600">{{ round($monthlyData['avg_hours'] ?? 0, 1) }}</dd>
            </div>
        </div>
    </div>
</div>
@endsection

