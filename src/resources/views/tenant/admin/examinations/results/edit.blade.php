@extends('tenant.layouts.admin')

@section('title', 'Edit Exam Result')

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
                    <a href="{{ url('/admin/examinations/results') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Results</a>
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
                Edit Exam Result
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $result->student->full_name ?? 'Student' }} - {{ $result->exam->exam_name ?? 'Exam' }}
            </p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ url('/admin/examinations/results/' . $result->id) }}" method="POST" class="max-w-2xl">
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
            <!-- Student Info -->
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Student Information</h3>
                <p class="text-sm text-gray-900">{{ $result->student->full_name ?? 'N/A' }}</p>
                <p class="text-xs text-gray-500">{{ $result->schoolClass->class_name ?? '' }} {{ $result->section->section_name ?? '' }}</p>
            </div>

            <!-- Exam Info -->
            <div class="p-4 bg-gray-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Exam Information</h3>
                <p class="text-sm text-gray-900">{{ $result->exam->exam_name ?? 'N/A' }} - {{ $result->subject->subject_name ?? 'N/A' }}</p>
                <p class="text-xs text-gray-500">Max Marks: {{ $result->max_marks }} | Passing: {{ $result->passing_marks ?? 'Not set' }}</p>
            </div>

            <!-- Marks Obtained -->
            <div>
                <label for="marks_obtained" class="block text-sm font-medium text-gray-700">
                    Marks Obtained
                </label>
                <input type="number" name="marks_obtained" id="marks_obtained"
                    value="{{ old('marks_obtained', $result->marks_obtained) }}"
                    min="0" max="{{ $result->max_marks }}" step="0.01"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Out of {{ $result->max_marks }} marks</p>
            </div>

            <!-- Absent -->
            <div class="flex items-center">
                <input type="checkbox" name="is_absent" id="is_absent" value="1"
                    {{ old('is_absent', $result->is_absent) ? 'checked' : '' }}
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="is_absent" class="ml-2 block text-sm text-gray-700">
                    Mark as Absent
                </label>
            </div>

            <!-- Re-exam -->
            <div class="flex items-center">
                <input type="checkbox" name="is_re_exam" id="is_re_exam" value="1"
                    {{ old('is_re_exam', $result->is_re_exam) ? 'checked' : '' }}
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="is_re_exam" class="ml-2 block text-sm text-gray-700">
                    Re-examination
                </label>
            </div>

            <!-- Remarks -->
            <div>
                <label for="remarks" class="block text-sm font-medium text-gray-700">
                    Remarks
                </label>
                <textarea name="remarks" id="remarks" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('remarks', $result->remarks) }}</textarea>
            </div>

            <!-- Current Result Summary -->
            @if($result->percentage || $result->grade)
            <div class="p-4 bg-blue-50 rounded-lg">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Current Result</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Percentage:</span>
                        <span class="font-medium text-gray-900">{{ number_format($result->percentage, 2) }}%</span>
                    </div>
                    @if($result->grade)
                    <div>
                        <span class="text-gray-500">Grade:</span>
                        <span class="font-medium text-gray-900">{{ $result->grade }}</span>
                    </div>
                    @endif
                    @if($result->gpa)
                    <div>
                        <span class="text-gray-500">GPA:</span>
                        <span class="font-medium text-gray-900">{{ $result->gpa }}</span>
                    </div>
                    @endif
                    <div>
                        <span class="text-gray-500">Status:</span>
                        <span class="font-medium text-gray-900">{{ ucfirst($result->status) }}</span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Submit Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/examinations/results') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Update Result
            </button>
        </div>
    </form>
</div>
@endsection

