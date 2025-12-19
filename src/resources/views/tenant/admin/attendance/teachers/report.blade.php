@extends('tenant.layouts.admin')

@section('title', 'Teacher Attendance Report')

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
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ url('/admin/attendance/teachers') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Teacher Attendance</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Reports</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Teacher Attendance Report
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Generate comprehensive teacher attendance reports with filters and export options
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/attendance/teachers') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Report Filters</h3>

            <form method="GET" action="{{ url('/admin/attendance/teachers/report') }}" class="space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Report Type -->
                    <div>
                        <label for="report_type" class="block text-sm font-medium text-gray-700">Report Type</label>
                        <select id="report_type" name="report_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="daily" {{ request('report_type') == 'daily' ? 'selected' : '' }}>Daily Report</option>
                            <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>Monthly Summary</option>
                            <option value="teacher_wise" {{ request('report_type') == 'teacher_wise' ? 'selected' : '' }}>Teacher-wise History</option>
                            @if($departments->count() > 0)
                            <option value="department_wise" {{ request('report_type') == 'department_wise' ? 'selected' : '' }}>Department-wise Summary</option>
                            @endif
                            <option value="defaulters" {{ request('report_type') == 'defaulters' ? 'selected' : '' }}>Defaulter List</option>
                        </select>
                    </div>

                    <!-- Date From -->
                    <div>
                        <label for="date_from" class="block text-sm font-medium text-gray-700">From Date</label>
                        <input type="date" name="date_from" id="date_from"
                            value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>

                    <!-- Date To -->
                    <div>
                        <label for="date_to" class="block text-sm font-medium text-gray-700">To Date</label>
                        <input type="date" name="date_to" id="date_to"
                            value="{{ request('date_to', now()->format('Y-m-d')) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>

                    <!-- Department Filter -->
                    @if($departments->count() > 0)
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700">Department (Optional)</label>
                        <select id="department_id" name="department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->department_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Teacher Filter (for teacher-wise report) -->
                    <div>
                        <label for="teacher_id" class="block text-sm font-medium text-gray-700">Teacher (Optional)</label>
                        <select id="teacher_id" name="teacher_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">All Teachers</option>
                            @if(isset($teachers))
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->first_name }} {{ $teacher->last_name }} ({{ $teacher->employee_id }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Threshold (for defaulters) -->
                    <div>
                        <label for="threshold" class="block text-sm font-medium text-gray-700">Attendance Threshold (%) <span class="text-gray-400">(Optional)</span></label>
                        <input type="number" name="threshold" id="threshold"
                            value="{{ request('threshold') }}" min="0" max="100" step="1"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                            placeholder="Default: 90%">
                        <p class="mt-1 text-xs text-gray-500">For defaulter list. Leave empty to use default (90%)</p>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <button type="button" onclick="window.location.href='{{ url('/admin/attendance/teachers/report') }}'"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Reset Filters
                    </button>

                    <div class="flex space-x-3">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($reportData))
    <!-- Export Options -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-4 sm:px-6 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Export Report</h3>
            <div class="flex space-x-3">
                <a href="{{ url('/admin/attendance/teachers/export') }}?{{ http_build_query(request()->all()) }}&format=excel"
                    class="inline-flex items-center px-4 py-2 border border-green-600 shadow-sm text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export to Excel
                </a>
                <a href="{{ url('/admin/attendance/teachers/export') }}?{{ http_build_query(request()->all()) }}&format=pdf"
                    class="inline-flex items-center px-4 py-2 border border-red-600 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    Export to PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Report Results -->
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                {{ $reportData['title'] }}
            </h3>

            @if($reportData['type'] == 'daily')
                @include('tenant.admin.attendance.teachers.reports.daily', ['data' => $reportData])
            @elseif($reportData['type'] == 'monthly')
                @include('tenant.admin.attendance.teachers.reports.monthly', ['data' => $reportData])
            @elseif($reportData['type'] == 'teacher_wise')
                @include('tenant.admin.attendance.teachers.reports.teacher-wise', ['data' => $reportData])
            @elseif($reportData['type'] == 'department_wise')
                @include('tenant.admin.attendance.teachers.reports.department-wise', ['data' => $reportData])
            @elseif($reportData['type'] == 'defaulters')
                @include('tenant.admin.attendance.teachers.reports.defaulters', ['data' => $reportData])
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

