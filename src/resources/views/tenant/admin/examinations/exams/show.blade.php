@extends('tenant.layouts.admin')

@section('title', $exam->exam_name . ' - Exam Details')

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
                    <a href="{{ url('/admin/examinations/exams') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Exams</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $exam->exam_name }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $exam->exam_name }}</h1>
            <p class="mt-1 text-sm text-gray-500">{{ ucfirst(str_replace('_', ' ', $exam->exam_type)) }} - {{ $exam->academic_year }}</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ url('/admin/examinations/exams/' . $exam->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Edit Exam
            </a>
            <a href="{{ url('/admin/examinations/exams') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back to List
            </a>
        </div>
    </div>

    <!-- Progress Indicators -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-6">Exam Progress</h2>
        <div class="space-y-6">
            <!-- Schedules Progress -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Schedules Created</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">{{ $stats['total_schedules'] }} schedule{{ $stats['total_schedules'] != 1 ? 's' : '' }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $stats['schedule_progress'] }}%"></div>
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ $stats['unique_subjects'] }} unique subject{{ $stats['unique_subjects'] != 1 ? 's' : '' }}</p>
            </div>

            <!-- Results Progress -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Results Entered</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">{{ $stats['students_with_results'] }} / {{ $stats['total_students'] }} students</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $stats['results_progress'] }}%"></div>
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ $stats['results_progress'] }}% complete • {{ $stats['results_pending'] }} pending</p>
            </div>

            <!-- Admit Cards Progress -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Admit Cards Generated</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">{{ $stats['students_with_admit_cards'] }} / {{ $stats['total_students'] }} students</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-purple-600 h-2.5 rounded-full" style="width: {{ $stats['admit_cards_progress'] }}%"></div>
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ $stats['admit_cards_progress'] }}% complete</p>
            </div>

            <!-- Report Cards Progress -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Report Cards Generated</span>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">{{ $stats['students_with_report_cards'] }} / {{ $stats['total_students'] }} students</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ $stats['report_cards_progress'] }}%"></div>
                </div>
                <p class="mt-1 text-xs text-gray-500">{{ $stats['report_cards_progress'] }}% complete</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Schedules</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_schedules'] }}</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Results Pending</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['results_pending'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4 sm:p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Average Score</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['average_score'] ? number_format($stats['average_score'], 1) : 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Details -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Exam Information</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Exam Name</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $exam->exam_name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Exam Type</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $exam->exam_type)) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $exam->academic_year }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Class</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $exam->schoolClass->class_name ?? 'All Classes' }}</dd>
            </div>
            @if($exam->start_date && $exam->end_date)
            <div>
                <dt class="text-sm font-medium text-gray-500">Date Range</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $exam->start_date->format('M d, Y') }} - {{ $exam->end_date->format('M d, Y') }}
                </dd>
            </div>
            @endif
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                    @php
                        $statusColors = [
                            'draft' => 'bg-gray-100 text-gray-800',
                            'scheduled' => 'bg-blue-100 text-blue-800',
                            'ongoing' => 'bg-yellow-100 text-yellow-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'published' => 'bg-purple-100 text-purple-800',
                            'archived' => 'bg-red-100 text-red-800',
                        ];
                        $color = $statusColors[$exam->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                        {{ ucfirst($exam->status) }}
                    </span>
                </dd>
            </div>
            @if($exam->description)
            <div class="sm:col-span-2 lg:col-span-3">
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $exam->description }}</dd>
            </div>
            @endif
        </dl>
    </div>

    <!-- Schedule Views -->
    @if($stats['total_schedules'] > 0)
    <div class="bg-white shadow rounded-lg p-3">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-3">
                    <div>
                <h2 class="text-base font-medium text-gray-900">Schedule Views</h2>
            </div>
            <div class="flex gap-1.5">
                <button onclick="switchView('date-wise')" id="btn-date-wise" class="view-btn active inline-flex items-center px-2.5 py-1.5 border border-transparent rounded text-xs font-medium text-white bg-primary-600 hover:bg-primary-700">
                    Date-wise
                </button>
                <button onclick="switchView('date-class')" id="btn-date-class" class="view-btn inline-flex items-center px-2.5 py-1.5 border border-gray-300 rounded text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Date & Class
                </button>
            </div>
        </div>

        <!-- Date-wise View -->
        <div id="view-date-wise" class="schedule-view">
            @if($dateWiseData && $dateWiseData->count() > 0)
            @php
                $colorPalette = [
                    0 => 'bg-primary-50 text-primary-700 border-primary-600',
                    1 => 'bg-secondary-100 text-secondary-700 border-secondary-600',
                    2 => 'bg-accent-50 text-accent-700 border-accent-600',
                    3 => 'bg-green-50 text-green-700 border-green-600',
                    4 => 'bg-yellow-50 text-yellow-700 border-yellow-600',
                    5 => 'bg-red-50 text-red-700 border-red-600',
                    6 => 'bg-blue-50 text-blue-700 border-blue-600',
                ];
            @endphp
            <div class="space-y-2">
                @foreach($dateWiseData as $dayData)
                <div class="border-l-2 border-primary-500 pl-2 py-1.5 bg-gray-50 rounded-r">
                    <div class="mb-1.5">
                        <h3 class="text-sm font-semibold text-gray-900">
                            {{ $dayData['date']->format('M d, Y') }}
                        </h3>
                    </div>
                    @php
                        $shifts = $dayData['shifts'] ?? [];
                        $shiftCount = count($shifts);
                        $gridCols = min($shiftCount, 4);
                    @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2" style="grid-template-columns: repeat({{ $gridCols }}, minmax(0, 1fr));">
                        @foreach($shifts as $shiftIndex => $shiftData)
                        @php
                            $shiftColorClass = $colorPalette[$shiftIndex] ?? 'bg-gray-50 text-gray-700 border-gray-600';
                        @endphp
                        <div class="bg-white rounded border border-gray-200 p-1.5">
                            <div class="text-xs font-medium {{ $shiftColorClass }} mb-1 pb-1 border-b border-gray-200 px-1 rounded">
                                {{ $shiftData['shift_name'] }}
                            </div>
                            <div class="space-y-0.5">
                                @foreach($shiftData['schedules'] as $scheduleItem)
                                @php
                                    $isDone = $scheduleItem['is_done'] ?? false;
                                    $missingAdmitCards = $scheduleItem['missing_admit_cards'] ?? false;
                                    $itemHighlight = ($exam->admit_card_enabled && $missingAdmitCards) ? 'ring-2 ring-yellow-400' : '';
                                @endphp
                                <div class="text-xs p-1 {{ $shiftColorClass }} rounded hover:opacity-80 transition-colors group {{ $itemHighlight }}">
                                    <div class="flex items-center justify-between gap-1">
                                        <div class="flex-1 min-w-0 flex items-center gap-1.5 flex-wrap">
                                            <span class="font-medium truncate">{{ $scheduleItem['subject'] }}</span>
                                            <span class="text-[10px] opacity-90">{{ $scheduleItem['time'] }}</span>
                                            <span class="text-[10px] opacity-75">{{ $scheduleItem['class'] }}@if($scheduleItem['section']) - {{ $scheduleItem['section'] }}@endif</span>
                                            @if($isDone)
                                            <span class="inline-flex items-center" title="Exam Done">
                                                <svg class="h-2.5 w-2.5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                            @endif
                                            @if($exam->admit_card_enabled && $missingAdmitCards)
                                            <span class="inline-flex items-center" title="Missing Admit Card">
                                                <svg class="h-2.5 w-2.5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            </span>
                                            @endif
                </div>
                                        <a href="{{ url('/admin/examinations/schedules/' . $scheduleItem['id'] . '/edit') }}"
                                           class="opacity-0 group-hover:opacity-100 flex-shrink-0 p-0.5 hover:bg-white hover:bg-opacity-50 rounded transition-all"
                                           title="Edit">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                                        </a>
                                    </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-xs text-gray-500 text-center py-4">No schedules found</p>
            @endif
        </div>

        <!-- Date & Class View -->
        <div id="view-date-class" class="schedule-view hidden">
            @php
                // Dynamic shift color mapping based on shift index/ID - defined once for the entire view
                // Color palette: primary (0), secondary (1), accent (2), success (3), warning (4), error (5), info (6)
                $colorPalette = [
                    0 => 'bg-primary-50 text-primary-700 border-primary-600 font-bold',      // 1st shift - Primary
                    1 => 'bg-secondary-100 text-secondary-700 border-secondary-600 font-bold', // 2nd shift - Secondary
                    2 => 'bg-accent-50 text-accent-700 border-accent-600 font-bold',          // 3rd shift - Accent
                    3 => 'bg-green-50 text-green-700 border-green-600 font-bold',             // 4th shift - Success (green)
                    4 => 'bg-yellow-50 text-yellow-700 border-yellow-600 font-bold',          // 5th shift - Warning (yellow)
                    5 => 'bg-red-50 text-red-700 border-red-600 font-bold',                  // 6th shift - Error (red)
                    6 => 'bg-blue-50 text-blue-700 border-blue-600 font-bold',                 // 7th shift - Info (blue)
                ];

                // Ensure shiftColorIndex is available
                $shiftIndexMap = $shiftColorIndex ?? [];
            @endphp
            @if($dateClassData && isset($dateClassData['dates']) && count($dateClassData['dates']) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-r border-gray-300 sticky left-0 bg-gray-50 z-10">
                                Date
                            </th>
                            @foreach($dateClassData['classes'] as $className)
                            <th scope="col" class="px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider border-r border-gray-300 min-w-[180px]">
                                {{ $className }}
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($dateClassData['dates'] as $dateKey)
                            @php
                                $dateObj = \Carbon\Carbon::parse($dateKey);
                                $rowData = $dateClassData['table'][$dateKey] ?? null;
                                $shiftsForDate = $rowData['shifts'] ?? [];

                                // Get all schedules for all shifts and classes for this date
                                $allSchedulesForDate = [];
                                foreach ($shiftsForDate as $shiftName => $shiftData) {
                                    foreach ($shiftData['classes'] as $className => $classSchedules) {
                                        foreach ($classSchedules as $scheduleItem) {
                                            // Ensure schedule is an array and add shift name
                                            $scheduleWithShift = is_array($scheduleItem) ? $scheduleItem : (array)$scheduleItem;
                                            $scheduleWithShift['shift_name'] = $shiftName;
                                            $allSchedulesForDate[] = [
                                                'class' => $className,
                                                'schedule' => $scheduleWithShift
                                            ];
                                        }
                                    }
                                }

                                // Group by class
                                $schedulesByClass = [];
                                foreach ($allSchedulesForDate as $item) {
                                    if (!isset($schedulesByClass[$item['class']])) {
                                        $schedulesByClass[$item['class']] = [];
                                    }
                                    $schedulesByClass[$item['class']][] = $item['schedule'];
                                }
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 whitespace-nowrap border-r border-gray-300 sticky left-0 bg-white z-10">
                                    <div class="text-xs font-semibold text-gray-900">{{ $dateObj->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $dateObj->format('l') }}</div>
                                </td>
                                @foreach($dateClassData['classes'] as $className)
                                <td class="px-2 py-1 border-r border-gray-300 align-top">
                                    @if(isset($schedulesByClass[$className]) && count($schedulesByClass[$className]) > 0)
                                        <div class="space-y-1">
                                            @foreach($schedulesByClass[$className] as $scheduleItem)
                                            @php
                                                // Ensure scheduleItem is an array
                                                $item = is_array($scheduleItem) ? $scheduleItem : [];
                                                $shiftId = $item['shift_id'] ?? null;

                                                // Get color class based on shift ID/index
                                                if ($shiftId && isset($shiftIndexMap[$shiftId])) {
                                                    $shiftIndex = $shiftIndexMap[$shiftId];
                                                    $shiftColorClass = $colorPalette[$shiftIndex] ?? $colorPalette[$shiftIndex % count($colorPalette)];
                                                } else {
                                                    $shiftColorClass = 'bg-gray-50 text-gray-700 border-gray-600 font-bold'; // Default for no shift
                                                }

                                                $subject = $item['subject'] ?? 'N/A';
                                                $time = $item['time'] ?? 'N/A';
                                                $section = $item['section'] ?? null;
                                                $scheduleId = $item['id'] ?? null;
                                                $isDone = $item['is_done'] ?? false;
                                                $missingAdmitCards = $item['missing_admit_cards'] ?? false;
                                                $itemHighlight = ($exam->admit_card_enabled && $missingAdmitCards) ? 'ring-2 ring-yellow-400' : '';
                                            @endphp
                                            @if($scheduleId)
                                            <div class="flex items-center gap-1.5 text-xs py-0.5 px-1.5 rounded hover:opacity-90 transition-colors group {{ $itemHighlight }}">
                                                <span class="px-2 py-1 rounded text-[10px] border-2 {{ $shiftColorClass }} flex-shrink-0 truncate max-w-[120px] shadow-sm" title="{{ $subject }} - {{ $shiftName }}">
                                                    {{ $subject }}
                                                </span>
                                                <span class="text-gray-600 flex-shrink-0">{{ $time }}</span>
                                                @if($section)
                                                <span class="text-gray-500 flex-shrink-0">({{ $section }})</span>
                                                @endif
                                                <div class="flex items-center gap-0.5 flex-shrink-0">
                                                    @if($isDone)
                                                    <svg class="h-2.5 w-2.5 text-green-600" fill="currentColor" viewBox="0 0 20 20" title="Done">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    @endif
                                                    @if($exam->admit_card_enabled && $missingAdmitCards)
                                                    <svg class="h-2.5 w-2.5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20" title="Missing Admit Card">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    @endif
                                                </div>
                                                <a href="{{ url('/admin/examinations/schedules/' . $scheduleId . '/edit') }}"
                                                   class="opacity-0 group-hover:opacity-100 flex-shrink-0 p-0.5 text-gray-400 hover:text-primary-600 transition-all"
                                                   title="Edit">
                                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </a>
                                            </div>
                                            @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-[10px] text-gray-300 text-center py-1">-</div>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-sm text-gray-500 text-center py-8">No schedules found</p>
            @endif
        </div>

    </div>

    <script>
        function switchView(viewType) {
            // Hide all views
            document.querySelectorAll('.schedule-view').forEach(view => {
                view.classList.add('hidden');
            });

            // Remove active class from all buttons
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-primary-600', 'text-white', 'border-transparent');
                btn.classList.add('border-gray-300', 'text-gray-700', 'bg-white');
            });

            // Show selected view
            document.getElementById('view-' + viewType).classList.remove('hidden');

            // Add active class to selected button
            const activeBtn = document.getElementById('btn-' + viewType);
            activeBtn.classList.add('active', 'bg-primary-600', 'text-white', 'border-transparent');
            activeBtn.classList.remove('border-gray-300', 'text-gray-700', 'bg-white');
        }
    </script>
    @endif

    <!-- Exam Schedules Table -->
    @if($stats['total_schedules'] > 0)
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h2 class="text-lg font-medium text-gray-900">Exam Schedules</h2>
                <p class="text-sm text-gray-500 mt-1">Manage and edit exam schedules</p>
            </div>
            <a href="{{ url('/admin/examinations/schedules/smart-bulk-create?exam_id=' . $exam->id) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Schedule
            </a>
        </div>

        <!-- Filters and Sorting -->
        <form method="GET" action="{{ url('/admin/examinations/exams/' . $exam->id) }}" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label for="sort_by" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select name="sort_by" id="sort_by" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="exam_date" {{ request('sort_by') == 'exam_date' ? 'selected' : '' }}>Date</option>
                        <option value="subject" {{ request('sort_by') == 'subject' ? 'selected' : '' }}>Subject</option>
                    </select>
                </div>
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Order</label>
                    <select name="sort_order" id="sort_order" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="asc" {{ request('sort_order', 'asc') == 'asc' ? 'selected' : '' }}>Ascending</option>
                        <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>Descending</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4">
                <a href="{{ url('/admin/examinations/exams/' . $exam->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Clear
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    Apply Filters
                </button>
            </div>
        </form>

        <!-- Summary -->
        @php
            $schedulesCount = is_array($schedules) ? count($schedules) : $schedules->count();
        @endphp
        <div class="mb-4 p-3 bg-gray-50 rounded-md">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    <span class="font-medium">Total Schedules:</span>
                    <span class="font-semibold text-gray-900">{{ $schedulesCount }}</span>
                    @if($stats['total_schedules'] != $schedulesCount)
                        <span class="text-amber-600 ml-2">
                            ({{ $stats['total_schedules'] }} total in database)
                        </span>
                    @endif
                </div>
                @if(request()->hasAny(['date_from', 'date_to']))
                <div class="text-xs text-gray-500">
                    Filters applied:
                    @if(request('date_from')) From {{ request('date_from') }} @endif
                    @if(request('date_to')) To {{ request('date_to') }} @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Schedules Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class/Section</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supervisor</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($schedules as $schedule)
                    @php
                        // Ensure we can safely access schedule properties
                        $subject = is_object($schedule) && $schedule->subject ? $schedule->subject->subject_name : (is_array($schedule) ? ($schedule['subject'] ?? 'N/A') : 'N/A');
                        $subjectCode = is_object($schedule) && $schedule->subject ? ($schedule->subject->subject_code ?? null) : (is_array($schedule) ? ($schedule['subject_code'] ?? null) : null);
                        $className = is_object($schedule) && $schedule->schoolClass ? $schedule->schoolClass->class_name : (is_array($schedule) ? ($schedule['class'] ?? 'N/A') : 'N/A');
                        $sectionName = is_object($schedule) && $schedule->section ? $schedule->section->section_name : (is_array($schedule) ? ($schedule['section'] ?? null) : null);
                        $examDate = is_object($schedule) ? $schedule->exam_date : (is_array($schedule) ? (isset($schedule['exam_date']) ? \Carbon\Carbon::parse($schedule['exam_date']) : null) : null);
                        $startTime = is_object($schedule) ? $schedule->start_time : (is_array($schedule) ? ($schedule['time'] ?? null) : null);
                        $endTime = is_object($schedule) ? $schedule->end_time : (is_array($schedule) ? ($schedule['end_time'] ?? null) : null);
                        $duration = is_object($schedule) ? $schedule->duration_minutes : (is_array($schedule) ? ($schedule['duration'] ?? null) : null);
                        $shiftName = is_object($schedule) && $schedule->shift ? $schedule->shift->shift_name : (is_array($schedule) ? ($schedule['shift'] ?? null) : null);
                        $supervisorName = is_object($schedule) && $schedule->supervisor ? $schedule->supervisor->full_name : (is_array($schedule) ? ($schedule['supervisor'] ?? null) : null);
                        $scheduleId = is_object($schedule) ? $schedule->id : (is_array($schedule) ? ($schedule['id'] ?? null) : null);
                        $examId = is_object($schedule) ? $schedule->exam_id : (is_array($schedule) ? ($schedule['exam_id'] ?? null) : null);
                        $isDone = is_object($schedule) ? ($schedule->is_done ?? false) : (is_array($schedule) ? ($schedule['is_done'] ?? false) : false);
                        $missingAdmitCards = is_object($schedule) ? ($schedule->missing_admit_cards ?? false) : (is_array($schedule) ? ($schedule['missing_admit_cards'] ?? false) : false);
                        $rowHighlight = ($exam->admit_card_enabled && $missingAdmitCards) ? 'bg-yellow-50 border-l-4 border-yellow-400' : '';
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors {{ $rowHighlight }}">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 text-primary-700 text-sm font-semibold">
                                {{ $loop->iteration }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $subject }}</div>
                            @if($subjectCode)
                            <div class="text-xs text-gray-500">{{ $subjectCode }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $className }}</div>
                            @if($sectionName)
                            <div class="text-xs text-gray-500">Section: {{ $sectionName }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $examDate ? $examDate->format('M d, Y') : 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $examDate ? $examDate->format('l') : '' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($startTime && $endTime)
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($startTime)->format('h:i A') }}</div>
                            <div class="text-xs text-gray-500">to {{ \Carbon\Carbon::parse($endTime)->format('h:i A') }}</div>
                            @else
                            <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $duration ? $duration . ' min' : '-' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($shiftName)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ $shiftName }}
                            </span>
                            @else
                            <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $supervisorName ?? 'Not assigned' }}
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex flex-col gap-1">
                                @if($isDone)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Done
                                </span>
                                @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                    Pending
                                </span>
                                @endif
                                @if($exam->admit_card_enabled && $missingAdmitCards)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Missing Admit Card
                                </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($scheduleId)
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ url('/admin/examinations/schedules/' . $scheduleId . '/edit') }}" class="inline-flex items-center justify-center p-2 text-gray-600 hover:text-primary-600 hover:bg-gray-100 rounded-md transition-colors" title="View Details">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ url('/admin/examinations/schedules/' . $scheduleId . '/edit') }}" class="inline-flex items-center justify-center p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 rounded-md transition-colors" title="Edit Schedule">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ url('/admin/examinations/schedules/' . $scheduleId) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this schedule?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center justify-center p-2 text-gray-600 hover:text-red-600 hover:bg-gray-100 rounded-md transition-colors" title="Delete Schedule">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-4 py-12 text-center">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No schedules found</h3>
                                <p class="mt-1 text-sm text-gray-500">Try adjusting your filters</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Schedule View Options -->
    @if($stats['total_schedules'] > 0)
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">View & Manage Schedule</h2>
        <p class="text-sm text-gray-500 mb-6">Choose how you want to view or manage the exam schedule</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Option 1: Basic Simple -->
            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-blue-500 transition-colors cursor-pointer" onclick="window.open('{{ url('/admin/examinations/schedules?exam_id=' . $exam->id . '&view=basic') }}', '_blank')">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-600 mb-4 mx-auto">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2 text-center">Basic Simple</h3>
                <p class="text-sm text-gray-600 mb-4 text-center">
                    Clean, printable format like traditional exam schedules. Perfect for printing and distribution.
                </p>
                <div class="text-xs text-gray-500 space-y-1 mb-4">
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        School header & logo
                    </div>
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Shift information
                    </div>
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Date-wise table
                    </div>
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Student info fields
                    </div>
                </div>
                <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                    View Basic Format
                </button>
            </div>

            <!-- Option 2: Advanced for Editing -->
            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-green-500 transition-colors cursor-pointer" onclick="window.location.href='{{ url('/admin/examinations/schedules?exam_id=' . $exam->id . '&view=advanced') }}'">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-green-100 text-green-600 mb-4 mx-auto">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2 text-center">Advanced for Editing</h3>
                <p class="text-sm text-gray-600 mb-4 text-center">
                    Detailed view with all information. Perfect for editing, reviewing, and administrative tasks.
                </p>
                <div class="text-xs text-gray-500 space-y-1 mb-4">
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Complete schedule details
                    </div>
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Room numbers & supervisors
                    </div>
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Time slots & durations
                    </div>
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Editable format
                    </div>
                </div>
                <button class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors text-sm font-medium">
                    View & Edit
                </button>
            </div>

            <!-- Option 3: Custom -->
            <div class="border-2 border-gray-200 rounded-lg p-6 hover:border-purple-500 transition-colors cursor-pointer" onclick="window.location.href='{{ url('/admin/examinations/schedules?exam_id=' . $exam->id . '&view=custom') }}'">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-purple-100 text-purple-600 mb-4 mx-auto">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2 text-center">Custom</h3>
                <p class="text-sm text-gray-600 mb-4 text-center">
                    Customize the layout and choose what information to display. Create your own format.
                </p>
                <div class="text-xs text-gray-500 space-y-1 mb-4">
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Customizable layout
                    </div>
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Select fields to display
                    </div>
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Choose color scheme
                    </div>
                    <div class="flex items-center">
                        <svg class="h-4 w-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Export to PDF/Excel
                    </div>
                </div>
                <button class="w-full px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors text-sm font-medium">
                    Customize & Export
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Enhanced Quick Actions -->
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ url('/admin/examinations/schedules/smart-bulk-create?exam_id=' . $exam->id) }}" class="flex items-center p-4 border-2 border-primary-300 rounded-lg hover:border-primary-400 hover:bg-primary-50 transition-colors bg-primary-50">
                <div class="flex-shrink-0 bg-primary-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Smart Bulk Create</p>
                    <p class="text-xs text-gray-500">Create all schedules at once</p>
                </div>
            </a>

            @if($stats['total_schedules'] > 0)
            <form action="{{ url('/admin/examinations/schedules/bulk/delete') }}" method="POST" class="flex items-center p-4 border-2 border-red-200 rounded-lg hover:border-red-300 hover:bg-red-50 transition-colors bg-red-50" onsubmit="return confirm('Are you sure you want to delete ALL {{ $stats['total_schedules'] }} schedule(s) for this exam? This action cannot be undone. Make sure you have no results entered for these schedules.');">
                @csrf
                @method('DELETE')
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <p class="text-sm font-medium text-gray-900">Delete All Schedules</p>
                    <p class="text-xs text-gray-500">Delete all {{ $stats['total_schedules'] }} schedule(s) to regenerate</p>
                </div>
                <button type="submit" class="ml-2 px-3 py-1 text-sm font-medium text-red-700 hover:text-red-900">
                    Delete
                </button>
            </form>
            @endif

            <a href="{{ url('/admin/examinations/schedules/select-exam?exam_id=' . $exam->id) }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Create Single Schedule</p>
                    <p class="text-xs text-gray-500">Add one schedule</p>
                </div>
            </a>

            <a href="{{ url('/admin/examinations/schedules?exam_id=' . $exam->id) }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">View Schedules</p>
                    <p class="text-xs text-gray-500">{{ $stats['total_schedules'] }} schedule{{ $stats['total_schedules'] != 1 ? 's' : '' }}</p>
                </div>
            </a>

            <a href="{{ url('/admin/examinations/results/quick-entry?exam_id=' . $exam->id) }}" class="flex items-center p-4 border-2 border-primary-300 rounded-lg hover:border-primary-400 hover:bg-primary-50 transition-colors bg-primary-50">
                <div class="flex-shrink-0 bg-primary-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Quick Results Entry</p>
                    <p class="text-xs text-gray-500">{{ $stats['results_pending'] }} pending</p>
                </div>
            </a>

            <a href="{{ url('/admin/examinations/results/entry?exam_id=' . $exam->id) }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Enter Results (Classic)</p>
                    <p class="text-xs text-gray-500">Traditional entry method</p>
                </div>
            </a>

            <a href="{{ url('/admin/examinations/results?exam_id=' . $exam->id) }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors">
                <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">View Results</p>
                    <p class="text-xs text-gray-500">{{ $stats['total_results'] }} entries</p>
                </div>
            </a>

            <a href="{{ url('/admin/examinations/admit-cards/generate?exam_id=' . $exam->id) }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Generate Admit Cards</p>
                    <p class="text-xs text-gray-500">{{ $stats['admit_cards_generated'] }} generated</p>
                </div>
            </a>

            <a href="{{ url('/admin/examinations/admit-cards?exam_id=' . $exam->id) }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors">
                <div class="flex-shrink-0 bg-pink-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">View Admit Cards</p>
                    <p class="text-xs text-gray-500">Manage cards</p>
                </div>
            </a>

            <a href="{{ url('/admin/examinations/report-cards/generate?exam_id=' . $exam->id) }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors">
                <div class="flex-shrink-0 bg-teal-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Generate Report Cards</p>
                    <p class="text-xs text-gray-500">{{ $stats['report_cards_generated'] }} generated</p>
                </div>
            </a>

            <a href="{{ url('/admin/examinations/report-cards?exam_id=' . $exam->id) }}" class="flex items-center p-4 border-2 border-gray-200 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors">
                <div class="flex-shrink-0 bg-cyan-100 rounded-lg p-3">
                    <svg class="h-6 w-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">View Report Cards</p>
                    <p class="text-xs text-gray-500">Manage cards</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

