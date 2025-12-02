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

    <!-- Exam Details -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Exam Information</h2>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">Description</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $exam->description }}</dd>
            </div>
            @endif
        </dl>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ url('/admin/examinations/schedules?exam_id=' . $exam->id) }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <svg class="h-8 w-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">View Schedules</p>
                    <p class="text-xs text-gray-500">Manage exam schedules</p>
                </div>
            </a>
            <a href="{{ url('/admin/examinations/results?exam_id=' . $exam->id) }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <svg class="h-8 w-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">View Results</p>
                    <p class="text-xs text-gray-500">Manage exam results</p>
                </div>
            </a>
            <a href="{{ url('/admin/examinations/admit-cards?exam_id=' . $exam->id) }}" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                <svg class="h-8 w-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Admit Cards</p>
                    <p class="text-xs text-gray-500">Generate admit cards</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection

