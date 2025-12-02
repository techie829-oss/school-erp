@extends('tenant.layouts.admin')

@section('title', 'Bulk Create Exam Schedules')

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
                    <a href="{{ url('/admin/examinations/schedules') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Schedules</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Bulk Create</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Bulk Create Exam Schedules
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Create multiple schedules for {{ $exam->exam_name }} at once
            </p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ url('/admin/examinations/schedules/bulk') }}" method="POST" id="bulkScheduleForm">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                <div class="text-sm text-red-700">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <div id="schedules-container">
                <!-- Schedule items will be added here dynamically -->
            </div>

            <div class="mt-4">
                <button type="button" id="add-schedule" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Schedule
                </button>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/examinations/schedules') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Create All Schedules
            </button>
        </div>
    </form>
</div>

<script>
let scheduleCount = 0;

function addScheduleRow() {
    scheduleCount++;
    const container = document.getElementById('schedules-container');
    const row = document.createElement('div');
    row.className = 'border border-gray-200 rounded-lg p-4 mb-4';
    row.id = `schedule-${scheduleCount}`;

    row.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">Schedule ${scheduleCount}</h3>
            <button type="button" onclick="removeSchedule(${scheduleCount})" class="text-red-600 hover:text-red-900">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Subject <span class="text-red-500">*</span></label>
                <select name="schedules[${scheduleCount}][subject_id]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">Select Subject</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->subject_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Class <span class="text-red-500">*</span></label>
                <select name="schedules[${scheduleCount}][class_id]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Exam Date <span class="text-red-500">*</span></label>
                <input type="date" name="schedules[${scheduleCount}][exam_date]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Start Time <span class="text-red-500">*</span></label>
                <input type="time" name="schedules[${scheduleCount}][start_time]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">End Time <span class="text-red-500">*</span></label>
                <input type="time" name="schedules[${scheduleCount}][end_time]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Max Marks <span class="text-red-500">*</span></label>
                <input type="number" name="schedules[${scheduleCount}][max_marks]" required min="0" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>
        </div>
    `;

    container.appendChild(row);
}

function removeSchedule(id) {
    const row = document.getElementById(`schedule-${id}`);
    if (row) {
        row.remove();
    }
}

document.getElementById('add-schedule').addEventListener('click', addScheduleRow);

// Add first schedule row on page load
addScheduleRow();
</script>
@endsection

