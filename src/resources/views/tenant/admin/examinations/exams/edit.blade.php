@extends('tenant.layouts.admin')

@section('title', 'Edit Exam')

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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Edit Exam
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Update exam details
            </p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ url('/admin/examinations/exams/' . $exam->id) }}" method="POST" class="max-w-2xl">
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
            <!-- Exam Name -->
            <div>
                <label for="exam_name" class="block text-sm font-medium text-gray-700">
                    Exam Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="exam_name" id="exam_name" value="{{ old('exam_name', $exam->exam_name) }}" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <!-- Exam Type -->
            <div>
                <label for="exam_type" class="block text-sm font-medium text-gray-700">
                    Exam Type <span class="text-red-500">*</span>
                </label>
                <select name="exam_type" id="exam_type" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="unit_test" {{ old('exam_type', $exam->exam_type) == 'unit_test' ? 'selected' : '' }}>Unit Test</option>
                    <option value="mid_term" {{ old('exam_type', $exam->exam_type) == 'mid_term' ? 'selected' : '' }}>Mid-term</option>
                    <option value="final" {{ old('exam_type', $exam->exam_type) == 'final' ? 'selected' : '' }}>Final</option>
                    <option value="quiz" {{ old('exam_type', $exam->exam_type) == 'quiz' ? 'selected' : '' }}>Quiz</option>
                    <option value="assignment" {{ old('exam_type', $exam->exam_type) == 'assignment' ? 'selected' : '' }}>Assignment</option>
                    <option value="preliminary" {{ old('exam_type', $exam->exam_type) == 'preliminary' ? 'selected' : '' }}>Preliminary</option>
                    <option value="practical" {{ old('exam_type', $exam->exam_type) == 'practical' ? 'selected' : '' }}>Practical</option>
                    <option value="oral" {{ old('exam_type', $exam->exam_type) == 'oral' ? 'selected' : '' }}>Oral</option>
                </select>
            </div>

            <!-- Academic Year -->
            <div>
                <label for="academic_year" class="block text-sm font-medium text-gray-700">
                    Academic Year
                </label>
                <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year', $exam->academic_year) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <!-- Class -->
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700">
                    Class (Optional)
                </label>
                <select name="class_id" id="class_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}>
                            {{ $class->class_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">
                        Start Date
                    </label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $exam->start_date ? $exam->start_date->format('Y-m-d') : '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">
                        End Date
                    </label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $exam->end_date ? $exam->end_date->format('Y-m-d') : '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">
                    Description
                </label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('description', $exam->description) }}</textarea>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">
                    Status
                </label>
                <select name="status" id="status"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="draft" {{ old('status', $exam->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="scheduled" {{ old('status', $exam->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="ongoing" {{ old('status', $exam->status) == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="completed" {{ old('status', $exam->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="published" {{ old('status', $exam->status) == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ old('status', $exam->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>

            <!-- Exam Options -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Exam Options</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="admit_card_enabled" id="admit_card_enabled" value="1"
                            {{ old('admit_card_enabled', $exam->admit_card_enabled ?? true) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="admit_card_enabled" class="ml-2 block text-sm text-gray-700">
                            Enable Admit Card
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 ml-6">Allow generation of admit cards for this exam</p>

                    <div class="flex items-center">
                        <input type="checkbox" name="result_enabled" id="result_enabled" value="1"
                            {{ old('result_enabled', $exam->result_enabled ?? true) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="result_enabled" class="ml-2 block text-sm text-gray-700">
                            Enable Result
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 ml-6">Allow result entry and publication for this exam</p>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/examinations/exams') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Update Exam
            </button>
        </div>
    </form>
</div>
@endsection

