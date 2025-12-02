@extends('tenant.layouts.admin')

@section('title', 'Enter Exam Results')

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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Entry</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Enter Exam Results
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $exam->exam_name }} - {{ $schedule->subject->subject_name ?? 'Subject' }} ({{ $schedule->schoolClass->class_name ?? 'Class' }})
            </p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ url('/admin/examinations/results') }}" method="POST">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">
        <input type="hidden" name="exam_schedule_id" value="{{ $schedule->id }}">

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
            <div class="mb-4 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-gray-700">
                    <strong>Max Marks:</strong> {{ $schedule->max_marks }} |
                    <strong>Passing Marks:</strong> {{ $schedule->passing_marks ?? 'Not set' }} |
                    <strong>Date:</strong> {{ $schedule->exam_date->format('M d, Y') }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Marks Obtained</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Absent</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($students as $student)
                        <tr>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                                <div class="text-xs text-gray-500">{{ $student->admission_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <input type="number"
                                    name="results[{{ $student->id }}][marks_obtained]"
                                    value="{{ $existingResults[$student->id] ?? '' }}"
                                    min="0"
                                    max="{{ $schedule->max_marks }}"
                                    step="0.01"
                                    class="marks-input w-24 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    data-student-id="{{ $student->id }}">
                                <input type="hidden" name="results[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <input type="checkbox"
                                    name="results[{{ $student->id }}][is_absent]"
                                    value="1"
                                    class="absent-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                    data-student-id="{{ $student->id }}">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text"
                                    name="results[{{ $student->id }}][remarks]"
                                    placeholder="Optional remarks"
                                    maxlength="500"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/examinations/schedules') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Save Results
            </button>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.absent-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const studentId = this.dataset.studentId;
        const marksInput = document.querySelector(`.marks-input[data-student-id="${studentId}"]`);

        if (this.checked) {
            marksInput.value = '0';
            marksInput.disabled = true;
        } else {
            marksInput.disabled = false;
        }
    });
});
</script>
@endsection

