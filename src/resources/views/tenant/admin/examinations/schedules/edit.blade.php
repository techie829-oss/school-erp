@extends('tenant.layouts.admin')

@section('title', 'Edit Exam Schedule')

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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Edit Exam Schedule
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Update schedule for {{ $schedule->exam->exam_name ?? 'Exam' }}
            </p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ url('/admin/examinations/schedules/' . $schedule->id) }}" method="POST" class="max-w-2xl">
        @csrf
        @method('PUT')

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

        <div class="bg-white shadow rounded-lg p-6 space-y-6">
            <!-- Subject -->
            <div>
                <label for="subject_id" class="block text-sm font-medium text-gray-700">
                    Subject <span class="text-red-500">*</span>
                </label>
                <select name="subject_id" id="subject_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', $schedule->subject_id) == $subject->id ? 'selected' : '' }}>
                            {{ $subject->subject_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Class -->
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700">
                    Class <span class="text-red-500">*</span>
                </label>
                <select name="class_id" id="class_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id', $schedule->class_id) == $class->id ? 'selected' : '' }}>
                            {{ $class->class_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Section -->
            <div>
                <label for="section_id" class="block text-sm font-medium text-gray-700">
                    Section (Optional)
                </label>
                <select name="section_id" id="section_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Sections</option>
                </select>
            </div>

            <!-- Exam Date -->
            <div>
                <label for="exam_date" class="block text-sm font-medium text-gray-700">
                    Exam Date <span class="text-red-500">*</span>
                </label>
                <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date', $schedule->exam_date->format('Y-m-d')) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <!-- Time Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">
                        Start Time <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $schedule->start_time->format('H:i')) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">
                        End Time <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $schedule->end_time->format('H:i')) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>

            <!-- Duration -->
            <div>
                <label for="duration_minutes" class="block text-sm font-medium text-gray-700">
                    Duration (minutes)
                </label>
                <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $schedule->duration_minutes) }}" min="1"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <!-- Marks -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="max_marks" class="block text-sm font-medium text-gray-700">
                        Maximum Marks <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="max_marks" id="max_marks" value="{{ old('max_marks', $schedule->max_marks) }}" required min="0" step="0.01"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label for="passing_marks" class="block text-sm font-medium text-gray-700">
                        Passing Marks
                    </label>
                    <input type="number" name="passing_marks" id="passing_marks" value="{{ old('passing_marks', $schedule->passing_marks) }}" min="0" step="0.01"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>

            <!-- Room Number -->
            <div>
                <label for="room_number" class="block text-sm font-medium text-gray-700">
                    Room Number
                </label>
                <input type="text" name="room_number" id="room_number" value="{{ old('room_number', $schedule->room_number) }}" maxlength="50"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <!-- Supervisor -->
            <div>
                <label for="supervisor_id" class="block text-sm font-medium text-gray-700">
                    Supervisor
                </label>
                <select name="supervisor_id" id="supervisor_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">Not assigned</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('supervisor_id', $schedule->supervisor_id) == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->full_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Instructions -->
            <div>
                <label for="instructions" class="block text-sm font-medium text-gray-700">
                    Instructions
                </label>
                <textarea name="instructions" id="instructions" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('instructions', $schedule->instructions) }}</textarea>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/examinations/schedules') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Update Schedule
            </button>
        </div>
    </form>
</div>
@endsection

