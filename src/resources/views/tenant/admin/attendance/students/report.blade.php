@extends('tenant.layouts.admin')

@section('title', 'Student Attendance Report')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Student Attendance Report
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Generate comprehensive attendance reports with filters and export options
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/attendance/students') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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

            <form method="GET" action="{{ url('/admin/attendance/students/report') }}" class="space-y-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Report Type -->
                    <div>
                        <label for="report_type" class="block text-sm font-medium text-gray-700">Report Type</label>
                        <select id="report_type" name="report_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="daily" {{ request('report_type') == 'daily' ? 'selected' : '' }}>Daily Report</option>
                            <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>Monthly Summary</option>
                            <option value="student_wise" {{ request('report_type') == 'student_wise' ? 'selected' : '' }}>Student-wise History</option>
                            <option value="class_wise" {{ request('report_type') == 'class_wise' ? 'selected' : '' }}>Class-wise Summary</option>
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

                    <!-- Class Filter -->
                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                        <select id="class_id" name="class_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Section Filter (Optional - only shown if sections exist) -->
                    @if($sections->count() > 0)
                    <div>
                        <label for="section_id" class="block text-sm font-medium text-gray-700">Section (Optional)</label>
                        <select id="section_id" name="section_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">All Sections</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                    {{ $section->section_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- Student Filter (for student-wise report) -->
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700">Student (Optional)</label>
                        <select id="student_id" name="student_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">All Students</option>
                            @if(isset($students))
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->first_name }} {{ $student->last_name }} ({{ $student->admission_number }})
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
                            placeholder="Default: 75%">
                        <p class="mt-1 text-xs text-gray-500">For defaulter list. Leave empty to use default (75%)</p>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                    <button type="button" onclick="window.location.href='{{ url('/admin/attendance/students/report') }}'"
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
                <a href="{{ url('/admin/attendance/students/export') }}?{{ http_build_query(request()->all()) }}&format=excel"
                    class="inline-flex items-center px-4 py-2 border border-green-600 shadow-sm text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export to Excel
                </a>
                <a href="{{ url('/admin/attendance/students/export') }}?{{ http_build_query(request()->all()) }}&format=pdf"
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
                @include('tenant.admin.attendance.students.reports.daily', ['data' => $reportData])
            @elseif($reportData['type'] == 'monthly')
                @include('tenant.admin.attendance.students.reports.monthly', ['data' => $reportData])
            @elseif($reportData['type'] == 'student_wise')
                @include('tenant.admin.attendance.students.reports.student-wise', ['data' => $reportData])
            @elseif($reportData['type'] == 'class_wise')
                @include('tenant.admin.attendance.students.reports.class-wise', ['data' => $reportData])
            @elseif($reportData['type'] == 'defaulters')
                @include('tenant.admin.attendance.students.reports.defaulters', ['data' => $reportData])
            @endif
        </div>
    </div>
    @endif
</div>
@endsection

