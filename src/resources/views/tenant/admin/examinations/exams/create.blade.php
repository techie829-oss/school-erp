@extends('tenant.layouts.admin')

@section('title', 'Create Exam')

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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Create</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Create New Exam
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Set up a new examination or assessment
            </p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ url('/admin/examinations/exams') }}" method="POST" class="max-w-2xl">
        @csrf

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
                <input type="text" name="exam_name" id="exam_name" value="{{ old('exam_name') }}" required
                    placeholder="e.g., Annual Examination 2025, Mid-term Test"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <!-- Exam Type -->
            <div>
                <label for="exam_type" class="block text-sm font-medium text-gray-700">
                    Exam Type <span class="text-red-500">*</span>
                </label>
                <select name="exam_type" id="exam_type" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">Select Type</option>
                    <option value="unit_test" {{ old('exam_type') == 'unit_test' ? 'selected' : '' }}>Unit Test</option>
                    <option value="mid_term" {{ old('exam_type') == 'mid_term' ? 'selected' : '' }}>Mid-term</option>
                    <option value="final" {{ old('exam_type') == 'final' ? 'selected' : '' }}>Final</option>
                    <option value="quiz" {{ old('exam_type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                    <option value="assignment" {{ old('exam_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                    <option value="preliminary" {{ old('exam_type') == 'preliminary' ? 'selected' : '' }}>Preliminary</option>
                    <option value="practical" {{ old('exam_type') == 'practical' ? 'selected' : '' }}>Practical</option>
                    <option value="oral" {{ old('exam_type') == 'oral' ? 'selected' : '' }}>Oral</option>
                </select>
            </div>

            <!-- Academic Year -->
            <div>
                <label for="academic_year" class="block text-sm font-medium text-gray-700">
                    Academic Year
                </label>
                <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year', date('Y') . '-' . (date('Y') + 1)) }}"
                    placeholder="e.g., 2025-2026"
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
                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->class_name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Leave empty to apply to all classes</p>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">
                        Start Date
                    </label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">
                        End Date
                    </label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">
                    Description
                </label>
                <textarea name="description" id="description" rows="3"
                    placeholder="Additional details about this exam..."
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('description') }}</textarea>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">
                    Status
                </label>
                <select name="status" id="status"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                </select>
            </div>

            <!-- Exam Options -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Exam Options</h3>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input type="checkbox" name="admit_card_enabled" id="admit_card_enabled" value="1"
                            {{ old('admit_card_enabled', true) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="admit_card_enabled" class="ml-2 block text-sm text-gray-700">
                            Enable Admit Card
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 ml-6">Allow generation of admit cards for this exam</p>

                    <div class="flex items-center">
                        <input type="checkbox" name="result_enabled" id="result_enabled" value="1"
                            {{ old('result_enabled', true) ? 'checked' : '' }}
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <label for="result_enabled" class="ml-2 block text-sm text-gray-700">
                            Enable Result
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 ml-6">Allow result entry and publication for this exam</p>
                </div>
            </div>

            <!-- Scheduling Preferences (Collapsible) -->
            <div class="border-t border-gray-200 pt-6">
                <button type="button"
                        onclick="toggleSchedulingPreferences()"
                        class="flex items-center justify-between w-full text-left text-sm font-medium text-gray-900 hover:text-primary-600 focus:outline-none">
                    <span>Scheduling Preferences (Optional)</span>
                    <svg id="scheduling_toggle_icon" class="w-5 h-5 text-gray-500 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="scheduling_preferences_content" class="mt-4 hidden space-y-6">
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">Scheduling Preferences</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Maximum Exams Per Day -->
                            <div>
                                <label for="max_exams_per_day" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maximum Exams Per Day
                                </label>
                                <input type="number"
                                       id="max_exams_per_day"
                                       name="max_exams_per_day"
                                       value="{{ old('max_exams_per_day', 1) }}"
                                       min="1"
                                       max="5"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <p class="mt-1 text-xs text-gray-500">Number of exams per day for each class (1-5)</p>
                            </div>

                            <!-- Skip Weekends -->
                            <div>
                                <label class="flex items-center mt-6">
                                    <input type="checkbox"
                                           id="skip_weekends"
                                           name="skip_weekends"
                                           value="1"
                                           {{ old('skip_weekends', true) ? 'checked' : '' }}
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                    <span class="ml-2 text-sm text-gray-700">Skip Weekends</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500">Automatically skip Saturday and Sunday when scheduling</p>
                            </div>
                        </div>

                        <!-- Shift Selection Mode -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Shift Selection Mode</label>
                            <div class="flex space-x-6">
                                <label class="flex items-center">
                                    <input type="radio"
                                           name="shift_selection_mode"
                                           value="class_wise"
                                           id="shift_mode_class_wise"
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                                           {{ old('shift_selection_mode', 'class_wise') == 'class_wise' ? 'checked' : '' }}
                                           onchange="toggleDefaultShiftVisibility()">
                                    <span class="ml-2 text-sm text-gray-700">Class-wise</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio"
                                           name="shift_selection_mode"
                                           value="subject_wise"
                                           id="shift_mode_subject_wise"
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                                           {{ old('shift_selection_mode') == 'subject_wise' ? 'checked' : '' }}
                                           onchange="toggleDefaultShiftVisibility()">
                                    <span class="ml-2 text-sm text-gray-700">Subject-wise</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio"
                                           name="shift_selection_mode"
                                           value="both"
                                           id="shift_mode_both"
                                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300"
                                           {{ old('shift_selection_mode') == 'both' ? 'checked' : '' }}
                                           onchange="toggleDefaultShiftVisibility()">
                                    <span class="ml-2 text-sm text-gray-700">Both</span>
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                <span id="shift_mode_hint">Class-wise: One shift per class (all subjects use same shift)</span>
                            </p>
                        </div>

                        <!-- Default Shift (shown when class-wise or both) -->
                        <div id="default_shift_container" class="mt-4">
                            <label for="default_shift_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Default Shift (Optional)
                            </label>
                            <select id="default_shift_id"
                                    name="default_shift_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <option value="">No Shift (Custom Time)</option>
                                @if(isset($shifts))
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift->id }}"
                                                {{ old('default_shift_id') == $shift->id ? 'selected' : '' }}>
                                            {{ $shift->shift_name }} ({{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Selecting a shift will auto-fill time and duration</p>
                        </div>

                        <!-- Default Values -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <label for="default_max_marks" class="block text-sm font-medium text-gray-700 mb-2">
                                    Default Max Marks
                                </label>
                                <input type="number"
                                       id="default_max_marks"
                                       name="default_max_marks"
                                       value="{{ old('default_max_marks', 100) }}"
                                       min="1"
                                       step="1"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="default_passing_marks" class="block text-sm font-medium text-gray-700 mb-2">
                                    Default Passing Marks (Optional)
                                </label>
                                <input type="number"
                                       id="default_passing_marks"
                                       name="default_passing_marks"
                                       value="{{ old('default_passing_marks', 33) }}"
                                       min="0"
                                       step="1"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>

                            <div>
                                <label for="default_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Default Duration (minutes)
                                </label>
                                <input type="number"
                                       id="default_duration_minutes"
                                       name="default_duration_minutes"
                                       value="{{ old('default_duration_minutes', 90) }}"
                                       min="30"
                                       max="300"
                                       step="15"
                                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/examinations/exams') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Create Exam
            </button>
        </div>
    </form>
</div>

<script>
function toggleSchedulingPreferences() {
    const content = document.getElementById('scheduling_preferences_content');
    const icon = document.getElementById('scheduling_toggle_icon');

    if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.classList.add('rotate-180');
    } else {
        content.classList.add('hidden');
        icon.classList.remove('rotate-180');
    }
}

function toggleDefaultShiftVisibility() {
    const classWise = document.getElementById('shift_mode_class_wise').checked;
    const both = document.getElementById('shift_mode_both').checked;
    const shiftContainer = document.getElementById('default_shift_container');
    const hintElement = document.getElementById('shift_mode_hint');

    if (classWise || both) {
        shiftContainer.style.display = 'block';
        if (classWise) {
            hintElement.textContent = 'Class-wise: One shift per class (all subjects use same shift)';
        } else {
            hintElement.textContent = 'Both: Allow class-wise or subject-wise shift selection';
        }
    } else {
        shiftContainer.style.display = 'none';
        hintElement.textContent = 'Subject-wise: Different shifts can be assigned per subject';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleDefaultShiftVisibility();
});
</script>
@endsection

