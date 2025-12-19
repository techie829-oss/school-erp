@extends('tenant.layouts.admin')

@section('title', 'Quick Results Entry')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="flex mb-6" aria-label="Breadcrumb">
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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Quick Entry</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Quick Results Entry</h1>
        <p class="mt-1 text-sm text-gray-500">Enter exam results quickly with bulk actions</p>
    </div>

    <!-- Step Indicator -->
    <div class="mb-8">
        <div class="flex items-center">
            <div class="flex items-center relative flex-1">
                <div id="step-indicator-1" class="flex items-center justify-center w-10 h-10 rounded-full border-2 {{ $selectedSchedule ? 'border-green-600 bg-green-600 text-white' : 'border-primary-600 bg-primary-600 text-white' }} z-10">
                    <span class="text-sm font-semibold">1</span>
                </div>
                <div class="absolute top-5 left-10 right-0 h-0.5 bg-gray-300"></div>
            </div>
            <div class="flex-1 ml-4">
                <p class="text-sm font-medium {{ $selectedSchedule ? 'text-gray-900' : 'text-gray-900' }}">Select Schedule</p>
                <p class="text-xs text-gray-500">Choose exam and schedule</p>
            </div>
        </div>
        <div class="flex items-center mt-4">
            <div class="flex items-center relative flex-1">
                <div id="step-indicator-2" class="flex items-center justify-center w-10 h-10 rounded-full border-2 {{ $selectedSchedule ? 'border-primary-600 bg-primary-600 text-white' : 'border-gray-300 bg-white text-gray-500' }} z-10">
                    <span class="text-sm font-semibold">2</span>
                </div>
                <div class="absolute top-5 left-10 right-0 h-0.5 bg-gray-300"></div>
            </div>
            <div class="flex-1 ml-4">
                <p class="text-sm font-medium {{ $selectedSchedule ? 'text-gray-900' : 'text-gray-500' }}">Enter Marks</p>
                <p class="text-xs {{ $selectedSchedule ? 'text-gray-500' : 'text-gray-400' }}">Bulk entry with auto-calculation</p>
            </div>
        </div>
        <div class="flex items-center mt-4">
            <div class="flex items-center">
                <div id="step-indicator-3" class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-gray-300 bg-white text-gray-500">
                    <span class="text-sm font-semibold">3</span>
                </div>
            </div>
            <div class="flex-1 ml-4">
                <p class="text-sm font-medium text-gray-500">Review & Save</p>
                <p class="text-xs text-gray-400">Validate and submit</p>
            </div>
        </div>
    </div>

    <!-- Step 1: Select Schedule -->
    <div id="step-1" class="step-content">
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Step 1: Select Exam Schedule</h2>

            <form method="GET" action="{{ url('/admin/examinations/results/quick-entry') }}" id="schedule-select-form">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div>
                        <label for="exam_id" class="block text-sm font-medium text-gray-700 mb-2">Exam *</label>
                        <select name="exam_id" id="exam_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" required>
                            <option value="">Select Exam</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ $selectedExam && $selectedExam->id == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->exam_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                        <select name="class_id" id="class_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <select name="subject_id" id="subject_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            Filter Schedules
                        </button>
                    </div>
                </div>
            </form>

            @if($selectedExam)
            <div class="mt-6">
                <h3 class="text-sm font-medium text-gray-900 mb-3">Available Schedules ({{ $schedules->count() }})</h3>

                @if($schedules->count() > 0)
                <div class="space-y-2 max-h-96 overflow-y-auto">
                    @foreach($schedules as $schedule)
                    <a href="{{ url('/admin/examinations/results/quick-entry?exam_id=' . $selectedExam->id . '&schedule_id=' . $schedule->id) }}"
                       class="block p-4 border-2 rounded-lg hover:border-primary-300 hover:bg-primary-50 transition-colors {{ $selectedSchedule && $selectedSchedule->id == $schedule->id ? 'border-primary-500 bg-primary-50' : 'border-gray-200' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $schedule->subject->subject_name ?? 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $schedule->schoolClass->class_name ?? 'N/A' }}
                                    @if($schedule->section)
                                        - {{ $schedule->section->section_name }}
                                    @endif
                                    • {{ $schedule->exam_date ? $schedule->exam_date->format('M d, Y') : 'N/A' }}
                                    @if($schedule->start_time)
                                        {{ $schedule->start_time->format('H:i') }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">Max: {{ $schedule->max_marks }}</p>
                                @if($schedule->passing_marks)
                                    <p class="text-xs text-gray-500">Pass: {{ $schedule->passing_marks }}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-500 text-center py-8">No schedules found. Please create schedules first.</p>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Step 2: Enter Marks -->
    @if($selectedSchedule)
    <div id="step-2" class="step-content">
        <form action="{{ url('/admin/examinations/results') }}" method="POST" id="results-form">
            @csrf
            <input type="hidden" name="exam_id" value="{{ $selectedExam->id }}">
            <input type="hidden" name="exam_schedule_id" value="{{ $selectedSchedule->id }}">

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-medium text-gray-900">Step 2: Enter Marks</h2>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $selectedSchedule->subject->subject_name ?? 'N/A' }} -
                            {{ $selectedSchedule->schoolClass->class_name ?? 'N/A' }}
                            @if($selectedSchedule->section)
                                - {{ $selectedSchedule->section->section_name }}
                            @endif
                            (Max: {{ $selectedSchedule->max_marks }}, Pass: {{ $selectedSchedule->passing_marks ?? 'N/A' }})
                        </p>
                    </div>
                    <div class="flex space-x-2">
                        <button type="button" onclick="setAllAbsent()" class="px-3 py-1.5 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Mark All Absent
                        </button>
                        <button type="button" onclick="clearAll()" class="px-3 py-1.5 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Clear All
                        </button>
                    </div>
                </div>

                @if($students->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Marks</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">%</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Absent</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="results-tbody">
                            @foreach($students as $index => $student)
                            @php
                                $existingResult = $existingResults[$student->id] ?? null;
                                $marksObtained = $existingResult ? $existingResult->marks_obtained : '';
                                $isAbsent = $existingResult ? $existingResult->is_absent : false;
                                $percentage = $existingResult ? $existingResult->percentage : null;
                                $grade = $existingResult ? $existingResult->grade : null;
                                $status = $existingResult ? $existingResult->status : null;
                            @endphp
                            <tr class="result-row" data-student-id="{{ $student->id }}" data-max-marks="{{ $selectedSchedule->max_marks }}" data-passing-marks="{{ $selectedSchedule->passing_marks ?? 0 }}">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $student->admission_number }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $student->currentEnrollment->roll_number ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <input type="number"
                                           name="results[{{ $index }}][marks_obtained]"
                                           value="{{ $marksObtained }}"
                                           step="0.01"
                                           min="0"
                                           max="{{ $selectedSchedule->max_marks }}"
                                           class="marks-input block w-20 mx-auto rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-center"
                                           data-student-id="{{ $student->id }}"
                                           onchange="calculateResult({{ $student->id }})"
                                           {{ $isAbsent ? 'disabled' : '' }}>
                                    <input type="hidden" name="results[{{ $index }}][student_id]" value="{{ $student->id }}">
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="percentage-display text-sm font-medium" data-student-id="{{ $student->id }}">
                                        {{ $percentage !== null ? number_format($percentage, 2) . '%' : '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="grade-display text-sm font-medium" data-student-id="{{ $student->id }}">
                                        {{ $grade ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <span class="status-display text-xs font-medium px-2 py-1 rounded" data-student-id="{{ $student->id }}">
                                        @if($status)
                                            @if($status == 'pass')
                                                <span class="bg-green-100 text-green-800">Pass</span>
                                            @elseif($status == 'fail')
                                                <span class="bg-red-100 text-red-800">Fail</span>
                                            @else
                                                <span class="bg-gray-100 text-gray-800">Absent</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-center">
                                    <input type="checkbox"
                                           name="results[{{ $index }}][is_absent]"
                                           value="1"
                                           class="absent-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                           data-student-id="{{ $student->id }}"
                                           {{ $isAbsent ? 'checked' : '' }}
                                           onchange="toggleAbsent({{ $student->id }})">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ url('/admin/examinations/results/quick-entry') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                        Save Results
                    </button>
                </div>
                @else
                <p class="text-sm text-gray-500 text-center py-8">No students found for this schedule.</p>
                @endif
            </div>
        </form>
    </div>
    @endif
</div>

<script>
const gradeScales = @json($gradeScales ?? []);
const maxMarks = {{ $selectedSchedule->max_marks ?? 0 }};
const passingMarks = {{ $selectedSchedule->passing_marks ?? 0 }};

function calculateResult(studentId) {
    const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
    const marksInput = row.querySelector('.marks-input');
    const absentCheckbox = row.querySelector('.absent-checkbox');
    const percentageDisplay = row.querySelector('.percentage-display');
    const gradeDisplay = row.querySelector('.grade-display');
    const statusDisplay = row.querySelector('.status-display');

    if (absentCheckbox.checked) {
        marksInput.disabled = true;
        marksInput.value = '';
        percentageDisplay.textContent = '-';
        gradeDisplay.textContent = '-';
        statusDisplay.innerHTML = '<span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-medium">Absent</span>';
        return;
    }

    marksInput.disabled = false;
    const marks = parseFloat(marksInput.value) || 0;
    const percentage = maxMarks > 0 ? ((marks / maxMarks) * 100).toFixed(2) : 0;

    percentageDisplay.textContent = percentage + '%';

    // Calculate grade
    let grade = '-';
    for (const scale of gradeScales) {
        if (parseFloat(percentage) >= scale.min_percentage && parseFloat(percentage) <= scale.max_percentage) {
            grade = scale.grade_name;
            break;
        }
    }
    gradeDisplay.textContent = grade;

    // Calculate status
    let status = 'pass';
    let statusClass = 'bg-green-100 text-green-800';
    if (passingMarks > 0 && marks < passingMarks) {
        status = 'fail';
        statusClass = 'bg-red-100 text-red-800';
    } else if (parseFloat(percentage) < 33) {
        status = 'fail';
        statusClass = 'bg-red-100 text-red-800';
    }

    statusDisplay.innerHTML = `<span class="${statusClass} px-2 py-1 rounded text-xs font-medium">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
}

function toggleAbsent(studentId) {
    const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
    const marksInput = row.querySelector('.marks-input');
    const absentCheckbox = row.querySelector('.absent-checkbox');

    if (absentCheckbox.checked) {
        marksInput.disabled = true;
        marksInput.value = '';
    } else {
        marksInput.disabled = false;
    }

    calculateResult(studentId);
}

function setAllAbsent() {
    if (confirm('Mark all students as absent?')) {
        document.querySelectorAll('.absent-checkbox').forEach(cb => {
            cb.checked = true;
            toggleAbsent(parseInt(cb.dataset.studentId));
        });
    }
}

function clearAll() {
    if (confirm('Clear all marks?')) {
        document.querySelectorAll('.marks-input').forEach(input => {
            input.value = '';
            if (!input.disabled) {
                calculateResult(parseInt(input.dataset.studentId));
            }
        });
        document.querySelectorAll('.absent-checkbox').forEach(cb => {
            cb.checked = false;
        });
    }
}

// Auto-calculate on page load for existing results
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.result-row').forEach(row => {
        const studentId = parseInt(row.dataset.studentId);
        const marksInput = row.querySelector('.marks-input');
        if (marksInput.value && !marksInput.disabled) {
            calculateResult(studentId);
        }
    });
});
</script>
@endsection
