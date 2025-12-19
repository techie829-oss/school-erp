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

    <!-- Timeline View -->
    @if($timelineDates && $timelineDates->count() > 0)
    <div class="bg-white shadow rounded-lg p-4 sm:p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Exam Timeline</h2>
        <div class="space-y-4">
            @foreach($timelineDates as $dayData)
            <div class="border-l-4 border-primary-500 pl-4 py-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">
                            {{ $dayData['date']->format('l, M d, Y') }}
                        </h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $dayData['count'] }} exam{{ $dayData['count'] != 1 ? 's' : '' }} scheduled</p>
                    </div>
                </div>
                <div class="mt-2 space-y-1">
                    @foreach($dayData['schedules'] as $schedule)
                    <div class="flex items-center text-xs text-gray-600">
                        <svg class="h-3 w-3 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="font-medium">{{ $schedule['subject'] }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $schedule['time'] }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $schedule['class'] }}</span>
                        @if($schedule['section'])
                        <span class="ml-1 text-gray-500">({{ $schedule['section'] }})</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
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

