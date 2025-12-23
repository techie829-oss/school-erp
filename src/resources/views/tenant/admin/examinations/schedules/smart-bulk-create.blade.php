@extends('tenant.layouts.admin')

@section('title', 'Smart Bulk Create Schedules - ' . $exam->exam_name)

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
                    <a href="{{ url('/admin/examinations/exams') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Exams</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ url('/admin/examinations/exams/' . $exam->id) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">{{ $exam->exam_name }}</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ url('/admin/examinations/schedules?exam_id=' . $exam->id) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Schedules</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Smart Bulk Create</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Smart Bulk Schedule Creator</h1>
        <p class="mt-1 text-sm text-gray-500">Create all schedules for {{ $exam->exam_name }} in one go</p>
    </div>

    @if ($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4">
        <div class="text-sm text-red-700">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Single Page Form -->
    <form action="{{ url('/admin/examinations/schedules/smart-bulk') }}" method="POST" id="smartBulkForm">
        @csrf
        <input type="hidden" name="exam_id" value="{{ $exam->id }}">

        <!-- Section 1: Compact Quick Selection -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Quick Selection</h2>
            <p class="text-sm text-gray-500 mb-4">Select classes, sections, and subjects. Assigned subjects are pre-selected.</p>

            @php
                $classSubjectMode = $classSubjectMode ?? 'class_wise';
                $sectionSubjectMode = $sectionSubjectMode ?? 'section_wise';
            @endphp

            @if($classSubjectMode === 'student_wise' || $sectionSubjectMode === 'student_wise')
            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                <div class="flex">
                    <svg class="h-5 w-5 text-blue-400 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-sm text-blue-800 font-medium mb-1">Student-wise Subject Assignment Enabled</p>
                        <p class="text-xs text-blue-700 mb-2">
                            Subjects shown are aggregated from individual student assignments.
                            @if($classSubjectMode === 'student_wise')
                                Classes show all subjects assigned to students in that class.
                            @endif
                            @if($sectionSubjectMode === 'student_wise')
                                Sections show all subjects assigned to students in that section.
                            @endif
                        </p>
                        <p class="text-xs text-amber-700 bg-amber-50 p-2 rounded border border-amber-200">
                            <strong>Note:</strong> If students don't have subjects assigned for the current academic year,
                            all available subjects will be shown to allow you to select and assign them.
                            Once students have subjects assigned, only those subjects will be shown.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Compact Table Layout -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                <input type="checkbox" id="select-all-classes" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded" onchange="toggleAllClasses()">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sections</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subjects</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($classes as $class)
                        <tr class="class-row hover:bg-gray-50" data-class-id="{{ $class->id }}">
                            <td class="px-3 py-3 whitespace-nowrap">
                                <input type="checkbox"
                                       name="class_ids[]"
                                       value="{{ $class->id }}"
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded class-checkbox"
                                       data-class-id="{{ $class->id }}"
                                       data-has-sections="{{ $class->has_sections ? '1' : '0' }}"
                                       onchange="handleClassToggle({{ $class->id }})">
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $class->class_name }}</div>
                                <div class="text-xs text-gray-500">{{ $class->student_count }} students</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($class->has_sections && $class->sections->count() > 0)
                                <div class="relative">
                                    <button type="button"
                                            onclick="toggleSectionDropdown({{ $class->id }})"
                                            class="section-select-btn w-full max-w-xs text-left px-3 py-2 text-xs rounded-md border border-gray-300 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                            data-class-id="{{ $class->id }}"
                                            disabled>
                                        <span class="section-count-text">Select sections...</span>
                                        <svg class="inline-block ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div id="section-dropdown-{{ $class->id }}"
                                         class="section-dropdown absolute z-50 mt-1 w-64 max-h-60 overflow-auto bg-white border border-gray-300 rounded-md shadow-lg"
                                         style="display: none;">
                                        <div class="p-2 space-y-1">
                                            @foreach($class->sections as $section)
                                            <label class="flex items-center px-2 py-1.5 hover:bg-gray-50 rounded cursor-pointer">
                                                <input type="checkbox"
                                                       name="section_ids[]"
                                                       value="{{ $section->id }}"
                                                       class="section-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                                                       data-class-id="{{ $class->id }}"
                                                       checked
                                                       onchange="handleSectionChange({{ $class->id }})">
                                                <span class="ml-2 text-xs text-gray-700">
                                                    {{ $section->section_name }} <span class="text-gray-500">({{ $section->student_count ?? 0 }})</span>
                                                </span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @else
                                <span class="text-xs text-gray-400">No sections</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="relative">
                                    <button type="button"
                                            onclick="toggleSubjectDropdown({{ $class->id }})"
                                            class="subject-select-btn w-full max-w-xs text-left px-3 py-2 text-xs rounded-md border border-gray-300 bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:bg-gray-100 disabled:cursor-not-allowed"
                                            data-class-id="{{ $class->id }}"
                                            disabled>
                                        <span class="subject-count-text">Select subjects...</span>
                                        <svg class="inline-block ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div id="subject-dropdown-{{ $class->id }}"
                                         class="subject-dropdown absolute z-50 mt-1 w-72 max-h-80 overflow-auto bg-white border border-gray-300 rounded-md shadow-lg"
                                         style="display: none;">
                                        <div class="p-2 space-y-1 subject-checkbox-container" data-class-id="{{ $class->id }}">
                                            <!-- Subjects loaded dynamically -->
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <select class="class-shift-select block w-full max-w-xs rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs"
                                        data-class-id="{{ $class->id }}"
                                        onchange="updateClassTimeFromShift({{ $class->id }})">
                                    <option value="">Use Default</option>
                                    @foreach($shifts as $shift)
                                        <option value="{{ $shift['id'] }}"
                                                data-start-time="{{ $shift['start_time'] }}"
                                                data-end-time="{{ $shift['end_time'] }}"
                                                data-duration="{{ $shift['duration_minutes'] }}"
                                                {{ $exam->default_shift_id == $shift['id'] ? 'selected' : '' }}>
                                            {{ $shift['shift_name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" class="class-time-method" name="time_method_class_{{ $class->id }}" value="shift" data-class-id="{{ $class->id }}">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 2: Global Schedule Settings -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Global Schedule Settings</h2>
            <p class="text-sm text-gray-500 mb-4">Configure default values that apply to all schedules. Each class can have its own shift/time setting above.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="default_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Default Exam Date
                    </label>
                    <input type="date"
                           id="default_date"
                           name="default_date"
                           value="{{ $exam->start_date ? $exam->start_date->format('Y-m-d') : '' }}"
                           min="{{ now()->format('Y-m-d') }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                           onchange="generatePreviewOnChange()">
                    <p class="mt-1 text-xs text-gray-500">Default date for all schedules</p>
                </div>

                <div>
                    <label for="max_exams_per_day_display" class="block text-sm font-medium text-gray-700 mb-2">
                        Max Exams Per Day
                    </label>
                    <input type="number"
                           id="max_exams_per_day_display"
                           value="{{ $exam->max_exams_per_day ?? 1 }}"
                           min="1"
                           max="5"
                           readonly
                           class="block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm"
                           title="Set during exam creation">
                    <p class="mt-1 text-xs text-gray-500">From exam settings: {{ $exam->max_exams_per_day ?? 1 }} exam(s) per day</p>
                </div>

                <div>
                    <label for="default_max_marks" class="block text-sm font-medium text-gray-700 mb-2">
                        Default Max Marks
                    </label>
                    <input type="number"
                           id="default_max_marks"
                           name="default_max_marks"
                           value="{{ $exam->default_max_marks ?? 100 }}"
                           min="1"
                           step="1"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                           onchange="generatePreviewOnChange()">
                    <p class="mt-1 text-xs text-gray-500">Default maximum marks</p>
                </div>

                <div>
                    <label for="default_passing_marks" class="block text-sm font-medium text-gray-700 mb-2">
                        Default Passing Marks (Optional)
                    </label>
                    <input type="number"
                           id="default_passing_marks"
                           name="default_passing_marks"
                           value="{{ $exam->default_passing_marks ?? 33 }}"
                           min="0"
                           step="1"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                           onchange="generatePreviewOnChange()">
                    <p class="mt-1 text-xs text-gray-500">Default passing marks</p>
                </div>
            </div>
        </div>

        <!-- Section 3: Preview & Review -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Review Schedules</h2>
            <p class="text-sm text-gray-500 mb-4">Review all schedules that will be created. Adjust dates/times if needed.</p>

            <!-- Filter Controls -->
            <div class="mb-4 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-medium text-gray-700">Filter Schedules</h3>
                    <button type="button" onclick="clearFilters()" class="text-xs text-primary-600 hover:text-primary-800">Clear All</button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Class Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Filter by Class</label>
                        <select id="filter-class" onchange="applyFilters()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                            <option value="">All Classes</option>
                        </select>
                    </div>
                    <!-- Date Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Filter by Date</label>
                        <input type="date" id="filter-date" onchange="applyFilters()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                    </div>
                    <!-- Subject Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Filter by Subject</label>
                        <select id="filter-subject" onchange="applyFilters()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                            <option value="">All Subjects</option>
                        </select>
                    </div>
                    <!-- Shift Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Filter by Shift</label>
                        <select id="filter-shift" onchange="applyFilters()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                            <option value="">All Shifts</option>
                        </select>
                    </div>
                </div>
            </div>

            <div id="review-container" class="space-y-4">
                <p class="text-sm text-gray-500 text-center py-8">Select classes and subjects to see preview</p>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end mb-6">
            <button type="submit" class="px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                Create All Schedules
            </button>
        </div>
    </form>
</div>

<script>
// Class-subject and section-subject mappings from server
const classSubjects = @json($classSubjects ?? []);
const sectionSubjects = @json($sectionSubjects ?? []);
const subjects = @json($subjects ?? []);
const classesData = @json($classes ?? []);
const shifts = @json($shifts ?? []);

let selectedClasses = [];
let selectedSections = [];
let selectedSubjects = [];
let generatedSchedules = [];

// Load subjects for a specific class into checkbox container
function loadClassSubjects(classId) {
    const classRow = document.querySelector(`tr.class-row[data-class-id="${classId}"]`);
    if (!classRow) return;

    const subjectContainer = classRow.querySelector(`.subject-checkbox-container[data-class-id="${classId}"]`);
    if (!subjectContainer) return;

    // Get selected sections for this class
    const sectionCheckboxes = classRow.querySelectorAll(`.section-checkbox[data-class-id="${classId}"]:checked`);
    const classSelectedSections = [];
    sectionCheckboxes.forEach(cb => {
        classSelectedSections.push(parseInt(cb.value));
    });

    // Get subjects for this class
    let classSubjectIds = new Set();

    // Add common class subjects
    if (classSubjects[classId]) {
        classSubjects[classId].forEach(subId => classSubjectIds.add(subId));
    }

    // Add section-specific subjects if sections are selected
    const hasSections = classSelectedSections.length > 0;
    if (hasSections) {
        classSelectedSections.forEach(sectionId => {
            if (sectionSubjects[sectionId]) {
                sectionSubjects[sectionId].forEach(subId => classSubjectIds.add(subId));
            }
        });
    }

    // Preserve currently selected subjects
    const previouslySelected = new Set();
    const existingCheckboxes = subjectContainer.querySelectorAll('.subject-checkbox');
    const isFirstLoad = existingCheckboxes.length === 0;

    if (!isFirstLoad) {
        existingCheckboxes.forEach(cb => {
            if (cb.checked) {
                previouslySelected.add(parseInt(cb.value));
            }
        });
    }

    // Clear and rebuild checkboxes
    subjectContainer.innerHTML = '';

    // Only show subjects that are assigned to this class (common + section-specific)
    // Filter subjects to only those assigned to this class
    const assignedSubjects = subjects.filter(subject => classSubjectIds.has(subject.id));

    if (assignedSubjects.length === 0) {
        const noSubjectsMsg = document.createElement('p');
        noSubjectsMsg.className = 'text-xs text-gray-500 text-center py-2 px-2';
        noSubjectsMsg.textContent = 'No subjects assigned to this class';
        subjectContainer.appendChild(noSubjectsMsg);
        updateSubjectCountText(classId);
        return;
    }

    assignedSubjects.forEach(subject => {
        const wasPreviouslySelected = previouslySelected.has(subject.id);

        // Auto-select if: previously selected OR first load (all assigned subjects)
        const shouldBeSelected = wasPreviouslySelected || isFirstLoad;

        const label = document.createElement('label');
        label.className = 'flex items-center px-2 py-1.5 hover:bg-gray-50 rounded cursor-pointer';
        label.style.backgroundColor = '#f0fdf4'; // All shown subjects are assigned

        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.name = 'subject_ids[]';
        checkbox.value = subject.id;
        checkbox.className = 'subject-checkbox h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded';
        checkbox.setAttribute('data-class-id', classId);
        checkbox.checked = shouldBeSelected;
        checkbox.onchange = () => handleSubjectChange(classId);

        const span = document.createElement('span');
        span.className = 'ml-2 text-xs text-gray-700';
        span.style.fontWeight = '500'; // All shown subjects are assigned
        span.textContent = subject.subject_name + (subject.subject_code ? ` (${subject.subject_code})` : '') + ' ✓';

        label.appendChild(checkbox);
        label.appendChild(span);
        subjectContainer.appendChild(label);
    });

    // Update subject count text
    updateSubjectCountText(classId);
}

// Toggle all classes
function toggleAllClasses() {
    const selectAll = document.getElementById('select-all-classes');
    const checkboxes = document.querySelectorAll('.class-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = selectAll.checked;
        if (selectAll.checked) {
            handleClassToggle(parseInt(cb.dataset.classId));
        }
    });
}

// Update section count text
function updateSectionCountText(classId) {
    const classRow = document.querySelector(`tr.class-row[data-class-id="${classId}"]`);
    if (!classRow) return;

    const sectionBtn = classRow.querySelector(`.section-select-btn[data-class-id="${classId}"]`);
    const countText = classRow.querySelector(`.section-count-text`);
    if (!sectionBtn || !countText) return;

    const checkedSections = classRow.querySelectorAll(`.section-checkbox[data-class-id="${classId}"]:checked`);
    const count = checkedSections.length;

    if (count === 0) {
        countText.textContent = 'Select sections...';
    } else {
        countText.textContent = `${count} section${count !== 1 ? 's' : ''} selected`;
    }
}

// Update subject count text
function updateSubjectCountText(classId) {
    const classRow = document.querySelector(`tr.class-row[data-class-id="${classId}"]`);
    if (!classRow) return;

    const subjectBtn = classRow.querySelector(`.subject-select-btn[data-class-id="${classId}"]`);
    const countText = classRow.querySelector(`.subject-count-text`);
    if (!subjectBtn || !countText) return;

    const checkedSubjects = classRow.querySelectorAll(`.subject-checkbox[data-class-id="${classId}"]:checked`);
    const count = checkedSubjects.length;

    if (count === 0) {
        countText.textContent = 'Select subjects...';
    } else {
        countText.textContent = `${count} subject${count !== 1 ? 's' : ''} selected`;
    }
}

// Handle class toggle
function handleClassToggle(classId) {
    const classRow = document.querySelector(`tr.class-row[data-class-id="${classId}"]`);
    if (!classRow) return;

    const classCheckbox = classRow.querySelector(`.class-checkbox[data-class-id="${classId}"]`);
    const sectionBtn = classRow.querySelector(`.section-select-btn[data-class-id="${classId}"]`);
    const subjectBtn = classRow.querySelector(`.subject-select-btn[data-class-id="${classId}"]`);
    const sectionCheckboxes = classRow.querySelectorAll(`.section-checkbox[data-class-id="${classId}"]`);
    const subjectCheckboxes = classRow.querySelectorAll(`.subject-checkbox[data-class-id="${classId}"]`);

    if (classCheckbox.checked) {
        // Enable buttons
        if (sectionBtn) sectionBtn.disabled = false;
        if (subjectBtn) {
            subjectBtn.disabled = false;
            loadClassSubjects(classId);
        }
    } else {
        // Disable buttons and uncheck all
        if (sectionBtn) sectionBtn.disabled = true;
        if (subjectBtn) subjectBtn.disabled = true;
        sectionCheckboxes.forEach(cb => cb.checked = false);
        subjectCheckboxes.forEach(cb => cb.checked = false);
        updateSectionCountText(classId);
        updateSubjectCountText(classId);
    }
    generatePreviewOnChange();
}

// Handle section change
function handleSectionChange(classId) {
    updateSectionCountText(classId);
    loadClassSubjects(classId);
    generatePreviewOnChange();
}

// Handle subject change
function handleSubjectChange(classId) {
    updateSubjectCountText(classId);
    generatePreviewOnChange();
}

// Auto-generate preview when any selection changes
function generatePreviewOnChange() {
    // Debounce to avoid too many updates
    clearTimeout(window.previewTimeout);
    window.previewTimeout = setTimeout(() => {
        generatePreview();
    }, 300);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load subjects for all classes initially (disabled state)
    document.querySelectorAll('.class-row').forEach(row => {
        const classId = parseInt(row.dataset.classId);
        const subjectBtn = row.querySelector(`.subject-select-btn[data-class-id="${classId}"]`);
        const sectionBtn = row.querySelector(`.section-select-btn[data-class-id="${classId}"]`);

        if (subjectBtn) {
            // Load subjects immediately so they're available
            loadClassSubjects(classId);
            subjectBtn.disabled = true; // Disabled until class is checked
        }
        if (sectionBtn) {
            sectionBtn.disabled = true; // Disabled until class is checked
            updateSectionCountText(classId);
        }
    });

    // Initial preview generation
    generatePreviewOnChange();
});

// Generate preview
function generatePreview() {
    // Get current selections from DOM (from table multi-selects)
    selectedClasses = Array.from(document.querySelectorAll('.class-checkbox:checked')).map(cb => parseInt(cb.value));

    // Get sections from checkboxes
    selectedSections = [];
    document.querySelectorAll('.section-checkbox:checked').forEach(checkbox => {
        const classId = parseInt(checkbox.dataset.classId);
        const classRow = document.querySelector(`tr.class-row[data-class-id="${classId}"]`);
        const classCheckbox = classRow?.querySelector(`.class-checkbox[data-class-id="${classId}"]`);
        if (classCheckbox?.checked) {
            selectedSections.push(parseInt(checkbox.value));
        }
    });

    // Get subjects from checkboxes - collect per class
    selectedSubjects = []; // Keep for backward compatibility, but also use per-class
    const selectedSubjectsByClass = {}; // Store subjects per class

    selectedClasses.forEach(classId => {
        selectedSubjectsByClass[classId] = [];
    });

    document.querySelectorAll('.subject-checkbox:checked').forEach(checkbox => {
        const classId = parseInt(checkbox.dataset.classId);
        const classRow = document.querySelector(`tr.class-row[data-class-id="${classId}"]`);
        const classCheckbox = classRow?.querySelector(`.class-checkbox[data-class-id="${classId}"]`);
        if (classCheckbox?.checked) {
            const subjectId = parseInt(checkbox.value);
            selectedSubjects.push(subjectId);
            if (selectedSubjectsByClass[classId]) {
                selectedSubjectsByClass[classId].push(subjectId);
            }
        }
    });

    // Check if at least one class has subjects selected
    const hasAnySubjects = Object.values(selectedSubjectsByClass).some(subjects => subjects.length > 0);

    if (selectedClasses.length === 0 || !hasAnySubjects) {
        document.getElementById('review-container').innerHTML = '<p class="text-sm text-red-500 text-center py-8">Please complete previous steps. Make sure you have selected at least one class and one subject.</p>';
        return;
    }

    // Get default values (global)
    const defaultDate = document.getElementById('default_date').value;
    const defaultMaxMarks = parseFloat(document.getElementById('default_max_marks').value) || 100;
    const defaultPassingMarks = parseFloat(document.getElementById('default_passing_marks').value) || null;

    // Helper function to get class-specific time settings
    function getClassTimeSettings(classId) {
        const classRow = document.querySelector(`tr.class-row[data-class-id="${classId}"]`);
        if (!classRow) {
            return { shiftId: null, startTime: '09:00', duration: 90 };
        }

        const shiftSelect = classRow.querySelector(`.class-shift-select[data-class-id="${classId}"]`);
        if (shiftSelect && shiftSelect.value) {
            const selectedOption = shiftSelect.options[shiftSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.startTime) {
                return {
                    shiftId: shiftSelect.value,
                    startTime: selectedOption.dataset.startTime,
                    duration: parseInt(selectedOption.dataset.duration) || 90
                };
            }
        }

        // Use exam default shift if available
        const examDefaultShiftId = {{ $exam->default_shift_id ? $exam->default_shift_id : 'null' }};
        if (examDefaultShiftId && shifts && Array.isArray(shifts) && shifts.length > 0) {
            const defaultShift = shifts.find(s => parseInt(s.id) === parseInt(examDefaultShiftId));
            if (defaultShift) {
                // Parse time format (could be "09:00:00" or "09:00")
                const timeStr = defaultShift.start_time || '09:00';
                const timeParts = timeStr.split(':');
                const formattedTime = `${timeParts[0]}:${timeParts[1]}`;

                return {
                    shiftId: examDefaultShiftId,
                    startTime: formattedTime,
                    duration: parseInt(defaultShift.duration_minutes) || 90
                };
            }
        }

        // Fallback to exam default duration or 90
        return { shiftId: null, startTime: '09:00', duration: {{ $exam->default_duration_minutes ?? 90 }} };
    }

    // Validate required fields
    if (!defaultDate) {
        document.getElementById('review-container').innerHTML = '<p class="text-sm text-yellow-600 text-center py-8">Please set a default exam date to generate preview.</p>';
        return;
    }

    // Get exam start date
    const examStartDateStr = '{{ $exam->start_date ? $exam->start_date->format("Y-m-d") : "" }}';
    const examStartDate = examStartDateStr ? new Date(examStartDateStr) : null;
    const startDate = new Date(defaultDate);

    // Get exam preferences
    const maxExamsPerDay = {{ $exam->max_exams_per_day ?? 1 }};
    const skipWeekends = {{ $exam->skip_weekends ?? true ? 'true' : 'false' }};


    // Helper function to get next working day (skip weekends if enabled)
    function getNextWorkingDay(date) {
        const nextDay = new Date(date);
        nextDay.setDate(nextDay.getDate() + 1);
        // Skip weekends if enabled (Saturday = 6, Sunday = 0)
        if (skipWeekends) {
            while (nextDay.getDay() === 0 || nextDay.getDay() === 6) {
                nextDay.setDate(nextDay.getDate() + 1);
            }
        }
        return nextDay;
    }

    // Helper function to format date as YYYY-MM-DD
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    /**
     * PAPER LEAKAGE PREVENTION ALGORITHM
     *
     * This helper function finds a date that works for ALL sections that need to take
     * the SAME subject. This ensures that if multiple sections have the same subject,
     * they take the exam on the SAME day, preventing paper leakage.
     *
     * @param {number} classId - The class ID
     * @param {Array} sectionsToSchedule - Array of section IDs that need to take this subject
     * @param {Date} startDate - Starting date to check from
     * @param {Object} sectionDateExamCounts - Map of sectionId -> {dateKey: count} for exam counts per day
     * @param {number} maxExamsPerDay - Maximum exams allowed per day for a section
     * @returns {Date} - A date that works for ALL sections
     */
    function findCommonDateForSections(classId, sectionsToSchedule, startDate, sectionDateExamCounts, maxExamsPerDay) {
        let candidateDate = new Date(startDate);
        let attempts = 0;
        const maxAttempts = 100; // Prevent infinite loops

        while (attempts < maxAttempts) {
            const dateKey = formatDate(candidateDate);
            let allSectionsCanUseThisDate = true;

            // Check if ALL sections can use this date
            for (const sectionId of sectionsToSchedule) {
                const sectionDayCount = (sectionDateExamCounts[sectionId] && sectionDateExamCounts[sectionId][dateKey]) || 0;

                // If any section has reached max exams per day, this date won't work
                if (sectionDayCount >= maxExamsPerDay) {
                    allSectionsCanUseThisDate = false;
                    break;
                }
            }

            // If all sections can use this date, return it
            if (allSectionsCanUseThisDate) {
                return candidateDate;
            }

            // Move to next working day and try again
            candidateDate = getNextWorkingDay(candidateDate);
            attempts++;
        }

        // If we couldn't find a common date after max attempts, return the last calculated date
        return candidateDate;
    }

    // Track current date and exam count per class per day
    const classCurrentDates = {};
    const classDateExamCounts = {}; // Track how many exams scheduled per day per class

    // GLOBAL tracking for paper leakage prevention
    // Maps sectionId -> Set of dates where that section has exams
    const globalSectionScheduledDates = {};

    // Generate schedules - group by class for class-wise selection
    generatedSchedules = [];
    let scheduleIndex = 0;
    let schedulesByClass = {}; // Group schedules by class for easier management

    selectedClasses.forEach(classId => {
        // Get class-specific time settings
        const classTimeSettings = getClassTimeSettings(classId);
        const classShiftId = classTimeSettings.shiftId;
        const classStartTime = classTimeSettings.startTime;
        const classDuration = classTimeSettings.duration;

        // Calculate end time for this class
        const startTime = classStartTime.split(':');
        const startMinutes = parseInt(startTime[0]) * 60 + parseInt(startTime[1]);
        const endMinutes = startMinutes + classDuration;
        const endHours = Math.floor(endMinutes / 60);
        const endMins = endMinutes % 60;
        const classEndTime = `${String(endHours).padStart(2, '0')}:${String(endMins).padStart(2, '0')}`;
        const classData = classesData.find(c => c.id == classId);
        if (!classData) return;

        schedulesByClass[classId] = [];

        // Initialize date tracker for this class (start from default date)
        if (!classCurrentDates[classId]) {
            let classDate = new Date(startDate);
            // Skip weekends if enabled and start date is weekend
            if (skipWeekends) {
                while (classDate.getDay() === 0 || classDate.getDay() === 6) {
                    classDate = getNextWorkingDay(classDate);
                }
            }
            classCurrentDates[classId] = classDate;
            classDateExamCounts[classId] = {}; // Initialize exam count tracker for this class
        }

        // Get sections for this class (from checkboxes)
        const classRow = document.querySelector(`tr.class-row[data-class-id="${classId}"]`);
        const classSections = [];
        if (classRow) {
            const sectionCheckboxes = classRow.querySelectorAll(`.section-checkbox[data-class-id="${classId}"]:checked`);
            sectionCheckboxes.forEach(checkbox => {
                classSections.push(parseInt(checkbox.value));
            });
        }

        if (classData.has_sections && classSections.length > 0) {
            // Get subjects selected for THIS class only (not all classes)
            const classSelectedSubjects = selectedSubjectsByClass[classId] || [];

            // Split subjects into common (shared by all sections) and section-specific
            const commonSubjects = [];
            const sectionSpecificSubjects = [];

            classSelectedSubjects.forEach(subjectId => {
                // Check if subject is a common subject (assigned to class)
                const isCommonSubject = classSubjects[classId] && classSubjects[classId].includes(subjectId);

                if (isCommonSubject) {
                    commonSubjects.push(subjectId);
                } else {
                    // Check if subject is section-specific (assigned to any selected section)
                    let isSectionSpecific = false;
                    classSections.forEach(sectionId => {
                        if (sectionSubjects[sectionId] && sectionSubjects[sectionId].includes(subjectId)) {
                            isSectionSpecific = true;
                }
                    });
                    if (isSectionSpecific) {
                        sectionSpecificSubjects.push(subjectId);
                    }
                }
            });

            // Helper function to get shift and time settings
            function getShiftAndTimeSettings() {
                let scheduleShiftId = classShiftId;
                let scheduleStartTime = classStartTime;
                let scheduleEndTime = classEndTime;

                        // If no shift selected for class, try to auto-match based on class numeric
                        if (!scheduleShiftId && classData.class_numeric !== undefined && shifts && shifts.length > 0) {
                            const matchingShift = shifts.find(shift => {
                                if (!shift.class_ranges || shift.class_ranges.length === 0) return false;
                                return shift.class_ranges.some(range => {
                                    const min = range.min ?? 0;
                                    const max = range.max ?? 20;
                                    return classData.class_numeric >= min && classData.class_numeric <= max;
                                });
                            });

                            if (matchingShift) {
                                scheduleShiftId = matchingShift.id;
                                scheduleStartTime = matchingShift.start_time;
                                const shiftStart = matchingShift.start_time.split(':');
                                const shiftStartMinutes = parseInt(shiftStart[0]) * 60 + parseInt(shiftStart[1]);
                                const shiftEndMinutes = shiftStartMinutes + (matchingShift.duration_minutes || 90);
                                const shiftEndHours = Math.floor(shiftEndMinutes / 60);
                                const shiftEndMins = shiftEndMinutes % 60;
                                scheduleEndTime = `${String(shiftEndHours).padStart(2, '0')}:${String(shiftEndMins).padStart(2, '0')}`;
                            }
                        }

                return { scheduleShiftId, scheduleStartTime, scheduleEndTime };
            }

            // 1. Schedule COMMON subjects: ONE schedule per class (section_id = null)
            // All sections take the exam together on the same day and shift
            commonSubjects.forEach(subjectId => {
                // Get current date for this class
                let scheduleDate = new Date(classCurrentDates[classId]);
                const dateKey = formatDate(scheduleDate);

                // Check if current date has reached max exams per day
                const currentDayCount = classDateExamCounts[classId][dateKey] || 0;
                if (currentDayCount >= maxExamsPerDay) {
                    // Move to next working day
                    scheduleDate = getNextWorkingDay(scheduleDate);
                    classCurrentDates[classId] = scheduleDate;
                }

                // Update exam count for this date
                const newDateKey = formatDate(scheduleDate);
                if (!classDateExamCounts[classId][newDateKey]) {
                    classDateExamCounts[classId][newDateKey] = 0;
                }
                classDateExamCounts[classId][newDateKey]++;

                const { scheduleShiftId, scheduleStartTime, scheduleEndTime } = getShiftAndTimeSettings();

                // Create ONE schedule per class for common subject (section_id = null)
                    const schedule = {
                        class_id: classId,
                    section_id: null, // Common subjects apply to all sections
                        subject_id: subjectId,
                        shift_id: scheduleShiftId,
                        exam_date: formatDate(scheduleDate),
                        start_time: scheduleStartTime,
                        end_time: scheduleEndTime,
                        max_marks: defaultMaxMarks,
                        passing_marks: defaultPassingMarks,
                        index: scheduleIndex++
                    };
                    schedulesByClass[classId].push(schedule);
                    generatedSchedules.push(schedule);

                // Move to next day only if we've reached max exams per day for current date
                const finalDateKey = formatDate(scheduleDate);
                if (classDateExamCounts[classId][finalDateKey] >= maxExamsPerDay) {
                    classCurrentDates[classId] = getNextWorkingDay(scheduleDate);
                }
            });

            // 2. Schedule SECTION-SPECIFIC subjects: ONE schedule per section per subject
            // CRITICAL: If multiple sections of the same class have the SAME subject,
            // they MUST be scheduled on the SAME day to prevent paper leakage.
            // If sections take the same paper on different days, students can share questions.
            // Track exam count per section per day
            const sectionDateExamCounts = {}; // Track exams per section per day
            const sectionCurrentDates = {}; // Track current date per section

            // Initialize tracking for all sections of this class
            classSections.forEach(sectionId => {
                if (!sectionDateExamCounts[sectionId]) {
                    sectionDateExamCounts[sectionId] = {};
                }
                if (!sectionCurrentDates[sectionId]) {
                    let sectionDate = new Date(classCurrentDates[classId]);
                    // Skip weekends if enabled
                    if (skipWeekends) {
                        while (sectionDate.getDay() === 0 || sectionDate.getDay() === 6) {
                            sectionDate = getNextWorkingDay(sectionDate);
                        }
                    }
                    sectionCurrentDates[sectionId] = sectionDate;
                }
            });

            // Group subjects by subject ID to handle same-subject scheduling
            const subjectToSectionsMap = {}; // Map: subjectId -> [sectionIds]

            sectionSpecificSubjects.forEach(subjectId => {
                if (!subjectToSectionsMap[subjectId]) {
                    subjectToSectionsMap[subjectId] = [];
                }
                // Find which sections have this subject
                classSections.forEach(sectionId => {
                    if (sectionSubjects[sectionId] && sectionSubjects[sectionId].includes(subjectId)) {
                        subjectToSectionsMap[subjectId].push(sectionId);
                    }
                });
            });

            // Schedule each subject
            Object.keys(subjectToSectionsMap).forEach(subjectId => {
                const sectionsWithThisSubject = subjectToSectionsMap[subjectId];

                if (sectionsWithThisSubject.length === 0) return;

                // Find the earliest current date among all sections that need this subject
                let earliestDate = null;
                sectionsWithThisSubject.forEach(sectionId => {
                    const sectionDate = sectionCurrentDates[sectionId];
                    if (!earliestDate || sectionDate < earliestDate) {
                        earliestDate = new Date(sectionDate);
                    }
                });

                // Use PAPER LEAKAGE PREVENTION ALGORITHM to find a common date
                // that works for ALL sections with this subject
                const commonDate = findCommonDateForSections(
                    classId,
                    sectionsWithThisSubject,
                    earliestDate,
                    sectionDateExamCounts,
                    maxExamsPerDay
                );

                const dateKey = formatDate(commonDate);
                const { scheduleShiftId, scheduleStartTime, scheduleEndTime } = getShiftAndTimeSettings();

                // Schedule ALL sections with this subject on the SAME day (prevent paper leakage)
                sectionsWithThisSubject.forEach(sectionId => {
                    // Mark this date as used by this section
                    if (!globalSectionScheduledDates[sectionId]) {
                        globalSectionScheduledDates[sectionId] = new Set();
                    }
                    globalSectionScheduledDates[sectionId].add(dateKey);

                    // Update exam count for this section and date
                    if (!sectionDateExamCounts[sectionId][dateKey]) {
                        sectionDateExamCounts[sectionId][dateKey] = 0;
                    }
                    sectionDateExamCounts[sectionId][dateKey]++;

                    // Update class-level exam count
                    if (!classDateExamCounts[classId][dateKey]) {
                        classDateExamCounts[classId][dateKey] = 0;
                    }
                    classDateExamCounts[classId][dateKey]++;

                    // Create ONE schedule per section for section-specific subject
                    const schedule = {
                        class_id: classId,
                        section_id: sectionId,
                        subject_id: parseInt(subjectId),
                        shift_id: scheduleShiftId,
                        exam_date: dateKey,
                        start_time: scheduleStartTime,
                        end_time: scheduleEndTime,
                        max_marks: defaultMaxMarks,
                        passing_marks: defaultPassingMarks,
                        index: scheduleIndex++
                    };
                    schedulesByClass[classId].push(schedule);
                    generatedSchedules.push(schedule);

                    // Update section's current date for next subject
                    // If we've reached max exams per day on this date, move to next day
                    if (sectionDateExamCounts[sectionId][dateKey] >= maxExamsPerDay) {
                        sectionCurrentDates[sectionId] = getNextWorkingDay(commonDate);
                    } else {
                        // Keep the same date for next subject (if not at max)
                        sectionCurrentDates[sectionId] = commonDate;
                    }
                });
            });
        } else {
            // Create schedule for class (no sections) - use only subjects selected for THIS class
            const classSelectedSubjects = selectedSubjectsByClass[classId] || [];

            classSelectedSubjects.forEach(subjectId => {
                // Only use subjects that are assigned to this class
                const isAssigned = classSubjects[classId] && classSubjects[classId].includes(subjectId);
                if (isAssigned) { // Only use assigned subjects
                    // Get current date for this class
                    let scheduleDate = new Date(classCurrentDates[classId]);
                    const dateKey = formatDate(scheduleDate);

                    // Check if current date has reached max exams per day
                    const currentDayCount = classDateExamCounts[classId][dateKey] || 0;
                    if (currentDayCount >= maxExamsPerDay) {
                        // Move to next working day
                        scheduleDate = getNextWorkingDay(scheduleDate);
                        classCurrentDates[classId] = scheduleDate;
                    }

                    // Update exam count for this date
                    const newDateKey = formatDate(scheduleDate);
                    if (!classDateExamCounts[classId][newDateKey]) {
                        classDateExamCounts[classId][newDateKey] = 0;
                    }
                    classDateExamCounts[classId][newDateKey]++;

                    // Use class-specific time settings (already calculated above)
                    let scheduleShiftId = classShiftId;
                    let scheduleStartTime = classStartTime;
                    let scheduleEndTime = classEndTime;

                        // If no shift selected for class, try to auto-match based on class numeric
                        if (!scheduleShiftId && classData.class_numeric !== undefined && shifts && shifts.length > 0) {
                            const matchingShift = shifts.find(shift => {
                                if (!shift.class_ranges || shift.class_ranges.length === 0) return false;
                                return shift.class_ranges.some(range => {
                                    const min = range.min ?? 0;
                                    const max = range.max ?? 20;
                                    return classData.class_numeric >= min && classData.class_numeric <= max;
                                });
                            });

                            if (matchingShift) {
                                scheduleShiftId = matchingShift.id;
                                scheduleStartTime = matchingShift.start_time;
                                const shiftStart = matchingShift.start_time.split(':');
                                const shiftStartMinutes = parseInt(shiftStart[0]) * 60 + parseInt(shiftStart[1]);
                                const shiftEndMinutes = shiftStartMinutes + (matchingShift.duration_minutes || 90);
                                const shiftEndHours = Math.floor(shiftEndMinutes / 60);
                                const shiftEndMins = shiftEndMinutes % 60;
                                scheduleEndTime = `${String(shiftEndHours).padStart(2, '0')}:${String(shiftEndMins).padStart(2, '0')}`;
                            }
                        }

                    const schedule = {
                        class_id: classId,
                        section_id: null,
                        subject_id: subjectId,
                        shift_id: scheduleShiftId,
                        exam_date: formatDate(scheduleDate),
                        start_time: scheduleStartTime,
                        end_time: scheduleEndTime,
                        max_marks: defaultMaxMarks,
                        passing_marks: defaultPassingMarks,
                        index: scheduleIndex++
                    };
                    schedulesByClass[classId].push(schedule);
                    generatedSchedules.push(schedule);

                    // Move to next day only if we've reached max exams per day for current date
                    const finalDateKey = formatDate(scheduleDate);
                    if (classDateExamCounts[classId][finalDateKey] >= maxExamsPerDay) {
                        classCurrentDates[classId] = getNextWorkingDay(scheduleDate);
                    }
                }
            });
        }
    });

    // Store schedulesByClass for class-wise filtering
    window.schedulesByClass = schedulesByClass;

    // Render preview
    renderPreview();
}

function renderPreview() {
    const container = document.getElementById('review-container');

    if (generatedSchedules.length === 0) {
        container.innerHTML = '<p class="text-sm text-red-500 text-center py-8">No schedules to generate. Please check your selections.</p>';
        return;
    }

    // Get default values for rendering
    const defaultMaxMarks = parseFloat(document.getElementById('default_max_marks')?.value) || 100;

    // Get filter values
    const filterClass = document.getElementById('filter-class')?.value || '';
    const filterDate = document.getElementById('filter-date')?.value || '';
    const filterSubject = document.getElementById('filter-subject')?.value || '';
    const filterShift = document.getElementById('filter-shift')?.value || '';

    // Filter schedules
    let filteredSchedules = generatedSchedules.filter(schedule => {
        if (filterClass && parseInt(schedule.class_id) !== parseInt(filterClass)) return false;
        if (filterDate && schedule.exam_date !== filterDate) return false;
        if (filterSubject && parseInt(schedule.subject_id) !== parseInt(filterSubject)) return false;
        if (filterShift && schedule.shift_id && parseInt(schedule.shift_id) !== parseInt(filterShift)) return false;
        if (filterShift && !schedule.shift_id) return false; // If filtering by shift but schedule has no shift, exclude it
        return true;
    });

    // Populate filter dropdowns on first render
    populateFilterDropdowns();

    // Group filtered schedules by class
    const schedulesByClass = {};
    filteredSchedules.forEach(schedule => {
        if (!schedulesByClass[schedule.class_id]) {
            schedulesByClass[schedule.class_id] = [];
        }
        schedulesByClass[schedule.class_id].push(schedule);
    });

    // Check if no results after filtering
    if (filteredSchedules.length === 0) {
        container.innerHTML = `
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <p class="text-sm font-medium text-yellow-900 mb-2">No schedules match the current filters</p>
                <p class="text-xs text-yellow-700">Try adjusting your filter criteria or <button type="button" onclick="clearFilters()" class="text-primary-600 hover:text-primary-800 underline">clear all filters</button></p>
            </div>
        `;
        return;
    }

    let html = `
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <p class="text-sm font-medium text-blue-900 mb-2">
                Total Schedules: <strong id="total-schedules-count">${filteredSchedules.length}</strong>
                ${filteredSchedules.length !== generatedSchedules.length ? ` (${generatedSchedules.length} total, ${generatedSchedules.length - filteredSchedules.length} filtered out)` : ''}
            </p>
            <p class="text-xs text-blue-700">Select which classes to include in schedule creation</p>
        </div>
        <div class="space-y-4 max-h-96 overflow-y-auto">
    `;

    let globalScheduleIndex = 0;

    // Render each class group with checkbox
    Object.keys(schedulesByClass).forEach(classId => {
        const classSchedules = schedulesByClass[classId];
        const classData = classesData.find(c => c.id == parseInt(classId));
        if (!classData) return;

        const classScheduleCount = classSchedules.length;

        html += `
            <div class="border-2 border-gray-300 rounded-lg p-4 class-schedule-group" data-class-id="${classId}">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-200">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox"
                               class="class-schedule-checkbox h-5 w-5 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                               data-class-id="${classId}"
                               checked
                               onchange="toggleClassSchedules(${classId})">
                        <span class="ml-3 text-base font-semibold text-gray-900">${classData.class_name}</span>
                    </label>
                    <span class="text-sm text-gray-600">${classScheduleCount} schedule${classScheduleCount !== 1 ? 's' : ''}</span>
                </div>
                <div class="class-schedules-container space-y-3">
        `;

        classSchedules.forEach((schedule, localIdx) => {
            const section = schedule.section_id ? classesData.flatMap(c => c.sections || []).find(s => s && s.id == schedule.section_id) : null;
            const subject = subjects.find(s => s.id == schedule.subject_id);

            // Ensure max_marks has a value - always use a number, never empty
            const scheduleMaxMarks = schedule.max_marks ? parseFloat(schedule.max_marks) : (defaultMaxMarks ? parseFloat(defaultMaxMarks) : 100);

            html += `
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50 class-schedule-item"
                     data-class-id="${classId}"
                     data-section-id="${schedule.section_id || ''}"
                     data-subject-id="${schedule.subject_id}"
                     data-shift-id="${schedule.shift_id || ''}"
                     data-exam-date="${schedule.exam_date}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <input type="hidden" name="schedules[${globalScheduleIndex}][class_id]" value="${schedule.class_id}" class="schedule-input">
                        <input type="hidden" name="schedules[${globalScheduleIndex}][section_id]" value="${schedule.section_id || ''}" class="schedule-input">
                        <input type="hidden" name="schedules[${globalScheduleIndex}][subject_id]" value="${schedule.subject_id}" class="schedule-input">
                        <input type="hidden" name="schedules[${globalScheduleIndex}][shift_id]" value="${schedule.shift_id || ''}" class="schedule-input">

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Class / Section</label>
                            <p class="text-sm font-medium text-gray-900">${classData.class_name}${section ? ' - ' + section.section_name : ''}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Subject</label>
                            <p class="text-sm font-medium text-gray-900">${subject?.subject_name || 'N/A'}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
                            <input type="date"
                                   name="schedules[${globalScheduleIndex}][exam_date]"
                                   value="${schedule.exam_date}"
                                   class="schedule-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Start</label>
                                <input type="time"
                                       name="schedules[${globalScheduleIndex}][start_time]"
                                       value="${schedule.start_time}"
                                       class="schedule-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">End</label>
                                <input type="time"
                                       name="schedules[${globalScheduleIndex}][end_time]"
                                       value="${schedule.end_time}"
                                       class="schedule-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Max Marks <span class="text-red-500">*</span></label>
                            <input type="number"
                                   name="schedules[${globalScheduleIndex}][max_marks]"
                                   value="${scheduleMaxMarks}"
                                   min="1"
                                   step="1"
                                   required
                                   data-required="true"
                                   class="schedule-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs"
                                   onchange="if(!this.value || parseFloat(this.value) < 1) { this.value = ${defaultMaxMarks || 100}; }">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Passing Marks</label>
                            <input type="number"
                                   name="schedules[${globalScheduleIndex}][passing_marks]"
                                   value="${schedule.passing_marks || ''}"
                                   min="0"
                                   step="1"
                                   class="schedule-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Room Number (Optional)</label>
                            <input type="text"
                                   name="schedules[${globalScheduleIndex}][room_number]"
                                   placeholder="e.g., 101"
                                   class="schedule-input block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                        </div>
                    </div>
                </div>
            `;
            globalScheduleIndex++;
        });

        html += `
                </div>
            </div>
        `;
    });

    html += '</div>';
    container.innerHTML = html;
    updateTotalScheduleCount();
}

// Toggle schedules for a specific class
function toggleClassSchedules(classId) {
    const checkbox = document.querySelector(`.class-schedule-checkbox[data-class-id="${classId}"]`);
    const scheduleItems = document.querySelectorAll(`.class-schedule-item[data-class-id="${classId}"]`);
    const isChecked = checkbox.checked;

    scheduleItems.forEach(item => {
        if (isChecked) {
            // Show and enable inputs
            item.style.opacity = '1';
            item.style.pointerEvents = 'auto';
            item.style.display = ''; // Ensure visible
            item.querySelectorAll('.schedule-input').forEach(input => {
                input.disabled = false;
                input.removeAttribute('disabled');
                // Ensure required fields have values
                if (input.name && input.name.includes('max_marks') && !input.value) {
                    const defaultMaxMarks = document.getElementById('default_max_marks')?.value || 100;
                    input.value = defaultMaxMarks;
                }
            });
        } else {
            // Hide inputs (they'll be removed on form submit)
            item.style.opacity = '0.5';
            item.style.pointerEvents = 'none';
            item.querySelectorAll('.schedule-input').forEach(input => {
                input.disabled = true;
            });
        }
    });

    updateTotalScheduleCount();
}

// Update total schedule count
function updateTotalScheduleCount() {
    const checkedClasses = document.querySelectorAll('.class-schedule-checkbox:checked');
    let totalCount = 0;

    checkedClasses.forEach(checkbox => {
        const classId = parseInt(checkbox.dataset.classId);
        const scheduleItems = document.querySelectorAll(`.class-schedule-item[data-class-id="${classId}"]`);
        totalCount += scheduleItems.length;
    });

    const countElement = document.getElementById('total-schedules-count');
    if (countElement) {
        countElement.textContent = totalCount;
    }
}

// Before form submission, remove schedules for unchecked classes and re-index
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('smartBulkForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Always prevent default first

            // STEP 0: First, identify which items to keep BEFORE removing anything
            // Get all schedule items
            const allScheduleItems = Array.from(document.querySelectorAll('.class-schedule-item'));

            // Filter to get remaining items (visible and checked)
            const remainingItems = allScheduleItems.filter(item => {
                // Check if item is visible (not hidden by filters or opacity)
                const isVisible = item.offsetParent !== null &&
                                 item.style.opacity !== '0.5' &&
                                 item.style.display !== 'none' &&
                                 !item.dataset.excluded;

                // Check if parent class checkbox is checked
                const classId = parseInt(item.dataset.classId);
                const classCheckbox = document.querySelector(`.class-schedule-checkbox[data-class-id="${classId}"]`);
                const isClassChecked = classCheckbox && classCheckbox.checked;

                // Also check that the item has required inputs (not all disabled)
                const hasRequiredInputs = item.querySelector('input[name*="[class_id]"], input[name*="[subject_id]"]');

                return isVisible && isClassChecked && hasRequiredInputs;
            });

            if (remainingItems.length === 0) {
                alert('Please select at least one class to create schedules for.');
                return false;
            }

            // STEP 0.5: Collect schedule data FROM remainingItems BEFORE removing anything
            const scheduleData = [];
            const defaultMaxMarks = parseFloat(document.getElementById('default_max_marks')?.value) || 100;
            const requiredFields = ['class_id', 'subject_id', 'exam_date', 'start_time', 'end_time', 'max_marks'];


            remainingItems.forEach((item, itemIndex) => {
                const schedule = {};
                const inputs = item.querySelectorAll('input, select, textarea');

                // First, collect all fields from inputs
                inputs.forEach(input => {
                    if (input.name && input.name.includes('schedules[')) {
                        const match = input.name.match(/schedules\[\d+\]\[(.+)\]/);
                        if (match) {
                            const fieldName = match[1];
                            let value = input.value;

                            // Skip disabled inputs (they won't be submitted)
                            if (input.disabled) {
                                return;
                            }

                            // Ensure max_marks always has a value
                            if (fieldName === 'max_marks') {
                                value = value && value.toString().trim() !== '' ? parseFloat(value) : defaultMaxMarks;
                            }

                            // Convert empty strings to null for optional fields
                            if ((fieldName === 'section_id' || fieldName === 'shift_id') && (value === '' || value === 'null')) {
                                value = null;
                            }

                            schedule[fieldName] = value;
                        }
                    }
                });

                // Ensure all required fields are present - check BEFORE adding to scheduleData
                requiredFields.forEach(fieldName => {
                    if (!schedule.hasOwnProperty(fieldName) || schedule[fieldName] === null || schedule[fieldName] === undefined || schedule[fieldName] === '') {
                        if (fieldName === 'max_marks') {
                            schedule[fieldName] = defaultMaxMarks;
                        } else {
                            // Try to get value from the DOM element directly
                            const fieldInput = item.querySelector(`input[name*="[${fieldName}]"], select[name*="[${fieldName}]"]`);
                            if (fieldInput && !fieldInput.disabled && fieldInput.value) {
                                schedule[fieldName] = fieldInput.value;
                            } else {
                                console.error(`Missing required field: ${fieldName}`, schedule, item);
                            }
                        }
                    }
                });

                // Triple-check max_marks is a valid number (must be > 0)
                if (!schedule.max_marks || isNaN(parseFloat(schedule.max_marks)) || parseFloat(schedule.max_marks) <= 0) {
                    schedule.max_marks = defaultMaxMarks;
                }

                // Final validation: ensure max_marks is a positive number
                schedule.max_marks = Math.max(1, parseFloat(schedule.max_marks) || defaultMaxMarks);

                scheduleData.push(schedule);
            });

            // STEP 0.6: Validate collected schedule data before proceeding
            const collectionErrors = [];
            scheduleData.forEach((schedule, idx) => {
                requiredFields.forEach(fieldName => {
                    if (!schedule.hasOwnProperty(fieldName) || schedule[fieldName] === null || schedule[fieldName] === undefined || schedule[fieldName] === '') {
                        collectionErrors.push(`Schedule ${idx}: missing ${fieldName}`);
                        // Auto-fix max_marks
                        if (fieldName === 'max_marks') {
                            schedule[fieldName] = defaultMaxMarks;
                        }
                    }
                });
                // Final check for max_marks validity
                if (!schedule.max_marks || isNaN(parseFloat(schedule.max_marks)) || parseFloat(schedule.max_marks) <= 0) {
                    schedule.max_marks = defaultMaxMarks;
                }
            });

            if (collectionErrors.length > 0) {
                // Fixed issues in collected schedule data
            }

            // STEP 1: NOW remove all schedule inputs from the entire form (we have the data)
            const allScheduleInputsGlobal = form.querySelectorAll('input[name^="schedules["], select[name^="schedules["], textarea[name^="schedules["]');
            allScheduleInputsGlobal.forEach(input => {
                if (input.name && input.name.match(/^schedules\[/)) {
                    input.remove();
                }
            });

            // STEP 1.5: Remove all schedule items for unchecked classes completely
            document.querySelectorAll('.class-schedule-checkbox:not(:checked)').forEach(checkbox => {
                const classId = parseInt(checkbox.dataset.classId);
                const scheduleItems = document.querySelectorAll(`.class-schedule-item[data-class-id="${classId}"]`);
                scheduleItems.forEach(item => {
                    // Remove all inputs from form first
                    item.querySelectorAll('input, select, textarea').forEach(input => {
                        if (input.name && input.name.match(/^schedules\[/)) {
                            input.remove();
                        }
                    });
                    // Mark item as excluded
                    item.dataset.excluded = 'true';
                });
            });

            // STEP 2: Validate collected schedule data
            let hasErrors = false;
            let errorMessages = [];

            scheduleData.forEach((schedule, index) => {
                requiredFields.forEach(fieldName => {
                    if (!schedule.hasOwnProperty(fieldName) || schedule[fieldName] === null || schedule[fieldName] === undefined || schedule[fieldName] === '') {
                        hasErrors = true;
                        errorMessages.push(`Schedule ${index + 1} is missing field: ${fieldName}`);
                    }
                });
            });

            if (hasErrors) {
                alert('Please fix the following errors:\n\n' + errorMessages.join('\n'));
                return false;
            }

            // STEP 3: Remove ALL old schedule inputs from form (we already have the data)
            // Do multiple passes to ensure everything is removed
            for (let pass = 0; pass < 3; pass++) {
                const verifyClean = form.querySelectorAll('input[name^="schedules["], select[name^="schedules["], textarea[name^="schedules["]');
                const oldInputIndices = new Set();
                verifyClean.forEach(input => {
                    const match = input.name.match(/schedules\[(\d+)\]/);
                    if (match) {
                        oldInputIndices.add(parseInt(match[1]));
                    }
                });


                if (verifyClean.length > 0) {
                    verifyClean.forEach(input => {
                        input.remove();
                    });
                } else {
                    break; // No more inputs to remove
                }
            }

            // Final verification - should be zero
            const finalVerify = form.querySelectorAll('input[name^="schedules["], select[name^="schedules["], textarea[name^="schedules["]');
            if (finalVerify.length > 0) {
                // Force remove them
                finalVerify.forEach(input => input.remove());
            }

            // STEP 4: Create fresh inputs with sequential indices (0, 1, 2, ...)

            scheduleData.forEach((schedule, index) => {
                // Ensure max_marks is always a valid number (must be > 0)
                let scheduleMaxMarks = defaultMaxMarks;
                if (schedule.max_marks) {
                    const parsed = parseFloat(schedule.max_marks);
                    if (!isNaN(parsed) && parsed > 0) {
                        scheduleMaxMarks = parsed;
                    }
                }

                // Create all required fields as hidden inputs
                const fields = [
                    { name: 'class_id', value: schedule.class_id || '', type: 'hidden' },
                    { name: 'subject_id', value: schedule.subject_id || '', type: 'hidden' },
                    { name: 'exam_date', value: schedule.exam_date || '', type: 'hidden' },
                    { name: 'start_time', value: schedule.start_time || '', type: 'hidden' },
                    { name: 'end_time', value: schedule.end_time || '', type: 'hidden' },
                    { name: 'max_marks', value: scheduleMaxMarks, type: 'hidden' },
                ];

                // Optional fields - handle null/empty properly
                // section_id: send only if it has a valid value (not null, not empty string)
                if (schedule.section_id && schedule.section_id !== '' && schedule.section_id !== 'null') {
                    fields.push({ name: 'section_id', value: schedule.section_id, type: 'hidden' });
                }
                // shift_id: send only if it has a valid value (not null, not empty string)
                if (schedule.shift_id && schedule.shift_id !== '' && schedule.shift_id !== 'null') {
                    fields.push({ name: 'shift_id', value: schedule.shift_id, type: 'hidden' });
                }
                if (schedule.passing_marks) {
                    fields.push({ name: 'passing_marks', value: schedule.passing_marks, type: 'hidden' });
                }
                if (schedule.room_number) {
                    fields.push({ name: 'room_number', value: schedule.room_number, type: 'hidden' });
                }

                // Create inputs
                fields.forEach(field => {
                    const input = document.createElement('input');
                    input.type = field.type || 'hidden';
                    input.name = `schedules[${index}][${field.name}]`;
                    // Ensure max_marks always has a valid numeric value (> 0)
                    if (field.name === 'max_marks') {
                        const parsedValue = field.value && !isNaN(parseFloat(field.value)) ? parseFloat(field.value) : defaultMaxMarks;
                        input.value = Math.max(1, parsedValue); // Ensure at least 1
                    } else {
                        input.value = field.value || '';
                    }
                    form.appendChild(input);
                });
            });


            // STEP 5: Final validation - verify all schedules have all required fields
            const finalInputs = form.querySelectorAll('input[name^="schedules["]');
            const scheduleIndices = new Set();
            finalInputs.forEach(input => {
                const match = input.name.match(/schedules\[(\d+)\]/);
                if (match) {
                    scheduleIndices.add(parseInt(match[1]));
                }
            });


            // Verify each schedule has all required fields
            let finalHasErrors = false;
            const sortedIndices = Array.from(scheduleIndices).sort((a, b) => a - b);
            const missingFields = [];

            sortedIndices.forEach(scheduleIndex => {
                requiredFields.forEach(fieldName => {
                    // Use querySelectorAll to get ALL inputs with this name (in case of duplicates)
                    const inputs = form.querySelectorAll(`input[name="schedules[${scheduleIndex}][${fieldName}]"]`);
                    const validInput = Array.from(inputs).find(inp => inp.value && inp.value.toString().trim() !== '');

                    if (!validInput) {
                        finalHasErrors = true;
                        missingFields.push({scheduleIndex, fieldName});

                        // Create the missing field
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = `schedules[${scheduleIndex}][${fieldName}]`;
                        hiddenInput.value = fieldName === 'max_marks' ? defaultMaxMarks : '';
                        form.appendChild(hiddenInput);
                    }
                });
            });


            // STEP 5.5: Double-check - remove any orphaned schedule inputs (not in our scheduleData)
            // This catches any inputs that might have been missed
            const validIndices = new Set();
            scheduleData.forEach((_, index) => validIndices.add(index));


            // Get all schedule inputs again after creation and validation fixes
            const allScheduleInputsAfter = form.querySelectorAll('input[name^="schedules["], select[name^="schedules["], textarea[name^="schedules["]');
            const foundIndices = new Set();
            const orphanedInputs = [];
            const duplicateInputs = [];

            // Check for duplicates and orphaned inputs
            const inputsByIndex = {};
            allScheduleInputsAfter.forEach(input => {
                const match = input.name.match(/schedules\[(\d+)\]\[(.+)\]/);
                if (match) {
                    const index = parseInt(match[1]);
                    const fieldName = match[2];
                    foundIndices.add(index);

                    if (!inputsByIndex[index]) {
                        inputsByIndex[index] = {};
                    }
                    if (!inputsByIndex[index][fieldName]) {
                        inputsByIndex[index][fieldName] = [];
                    }
                    inputsByIndex[index][fieldName].push(input);

                    // Check for duplicates
                    if (inputsByIndex[index][fieldName].length > 1) {
                        duplicateInputs.push({index, fieldName, count: inputsByIndex[index][fieldName].length});
                    }

                    if (!validIndices.has(index)) {
                        orphanedInputs.push({name: input.name, index});
                    }
                }
            });


            // Remove duplicate inputs - keep only the first one with a value, or the first one if all are empty
            let duplicatesRemoved = 0;
            Object.keys(inputsByIndex).forEach(indexStr => {
                const index = parseInt(indexStr);
                Object.keys(inputsByIndex[index]).forEach(fieldName => {
                    const inputs = inputsByIndex[index][fieldName];
                    if (inputs.length > 1) {
                        // Find the first input with a value
                        const validInput = inputs.find(inp => inp.value && inp.value.toString().trim() !== '');
                        const inputToKeep = validInput || inputs[0];

                        // Remove all others
                        inputs.forEach(inp => {
                            if (inp !== inputToKeep) {
                                inp.remove();
                                duplicatesRemoved++;
                            }
                        });
                    }
                });
            });


            // Re-query after duplicate removal to get fresh NodeList
            const inputsAfterDupRemoval = form.querySelectorAll('input[name^="schedules["], select[name^="schedules["], textarea[name^="schedules["]');

            // Remove orphaned inputs
            inputsAfterDupRemoval.forEach(input => {
                const match = input.name.match(/schedules\[(\d+)\]/);
                if (match) {
                    const index = parseInt(match[1]);
                    if (!validIndices.has(index)) {
                        input.remove();
                    }
                }
            });

            // One more pass - remove any schedule inputs with indices >= scheduleData.length
            // Re-query again after orphaned removal
            const finalRemainingInputs = form.querySelectorAll('input[name^="schedules["], select[name^="schedules["], textarea[name^="schedules["]');
            finalRemainingInputs.forEach(input => {
                const match = input.name.match(/schedules\[(\d+)\]/);
                if (match) {
                    const index = parseInt(match[1]);
                    if (index >= scheduleData.length) {
                        input.remove();
                    }
                }
            });


            if (finalHasErrors) {
                // Some required fields were missing and have been auto-filled
            }

            // STEP 6: One final check - count schedules and verify, ensure all have max_marks
            const finalScheduleCount = new Set();
            form.querySelectorAll('input[name^="schedules["]').forEach(input => {
                const match = input.name.match(/schedules\[(\d+)\]/);
                if (match) {
                    finalScheduleCount.add(parseInt(match[1]));
                }
            });

            // Final safety check: ensure ALL schedules have max_marks
            Array.from(finalScheduleCount).forEach(scheduleIndex => {
                const maxMarksInputs = form.querySelectorAll(`input[name="schedules[${scheduleIndex}][max_marks]"]`);
                const hasValidMaxMarks = Array.from(maxMarksInputs).some(inp => {
                    const val = inp.value;
                    return val && val.toString().trim() !== '' && !isNaN(parseFloat(val)) && parseFloat(val) > 0;
                });

                if (!hasValidMaxMarks) {
                    // Create max_marks input if missing or invalid
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `schedules[${scheduleIndex}][max_marks]`;
                    hiddenInput.value = defaultMaxMarks;
                    form.appendChild(hiddenInput);
                }
            });


            // STEP 7: Final validation - ensure ALL schedules have max_marks before submission
            const finalValidationErrors = [];
            Array.from(finalScheduleCount).sort((a, b) => a - b).forEach(scheduleIndex => {
                const maxMarksInputs = form.querySelectorAll(`input[name="schedules[${scheduleIndex}][max_marks]"]`);
                const hasValidMaxMarks = Array.from(maxMarksInputs).some(inp => {
                    const val = inp.value;
                    return val && val.toString().trim() !== '' && !isNaN(parseFloat(val)) && parseFloat(val) > 0;
                });

                if (!hasValidMaxMarks) {
                    finalValidationErrors.push(`Schedule ${scheduleIndex} is missing valid max_marks`);
                    // Create max_marks input if missing or invalid
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `schedules[${scheduleIndex}][max_marks]`;
                    hiddenInput.value = defaultMaxMarks;
                    form.appendChild(hiddenInput);
                }
            });

            if (finalValidationErrors.length > 0) {
                // Fixed missing max_marks for schedules
            }

            // STEP 8: Submit the form
            form.submit();
        });
    }
});

// Toggle section dropdown
function toggleSectionDropdown(classId) {
    const dropdown = document.getElementById(`section-dropdown-${classId}`);
    if (dropdown) {
        const isVisible = dropdown.style.display !== 'none';
        // Close all other dropdowns first
        document.querySelectorAll('.section-dropdown').forEach(d => {
            if (d.id !== `section-dropdown-${classId}`) {
                d.style.display = 'none';
            }
        });
        document.querySelectorAll('.subject-dropdown').forEach(d => {
            d.style.display = 'none';
        });
        // Toggle current dropdown
        dropdown.style.display = isVisible ? 'none' : 'block';
    }
}

// Toggle subject dropdown
function toggleSubjectDropdown(classId) {
    const dropdown = document.getElementById(`subject-dropdown-${classId}`);
    if (dropdown) {
        const isVisible = dropdown.style.display !== 'none';
        // Close all other dropdowns first
        document.querySelectorAll('.subject-dropdown').forEach(d => {
            if (d.id !== `subject-dropdown-${classId}`) {
                d.style.display = 'none';
            }
        });
        document.querySelectorAll('.section-dropdown').forEach(d => {
            d.style.display = 'none';
        });
        // Toggle current dropdown
        dropdown.style.display = isVisible ? 'none' : 'block';
    }
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative')) {
        document.querySelectorAll('.section-dropdown, .subject-dropdown').forEach(dropdown => {
            dropdown.style.display = 'none';
        });
    }
});

// Populate filter dropdowns with available options
function populateFilterDropdowns() {
    if (generatedSchedules.length === 0) return;

    // Populate class filter
    const classFilter = document.getElementById('filter-class');
    if (classFilter && classFilter.options.length <= 1) {
        const existingClasses = new Set();
        generatedSchedules.forEach(s => existingClasses.add(s.class_id));
        const sortedClasses = Array.from(existingClasses).map(id => {
            const classData = classesData.find(c => c.id == parseInt(id));
            return { id, name: classData?.class_name || `Class ${id}`, numeric: classData?.class_numeric || 0 };
        }).sort((a, b) => a.numeric - b.numeric);

        sortedClasses.forEach(({ id, name }) => {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = name;
            classFilter.appendChild(option);
        });
    }

    // Populate subject filter
    const subjectFilter = document.getElementById('filter-subject');
    if (subjectFilter && subjectFilter.options.length <= 1) {
        const existingSubjects = new Set();
        generatedSchedules.forEach(s => existingSubjects.add(s.subject_id));
        const sortedSubjects = Array.from(existingSubjects).map(id => {
            const subject = subjects.find(s => s.id == id);
            return { id, name: subject?.subject_name || `Subject ${id}`, code: subject?.subject_code || '' };
        }).sort((a, b) => a.name.localeCompare(b.name));

        sortedSubjects.forEach(({ id, name, code }) => {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = name + (code ? ` (${code})` : '');
            subjectFilter.appendChild(option);
        });
    }

    // Populate shift filter
    const shiftFilter = document.getElementById('filter-shift');
    if (shiftFilter && shiftFilter.options.length <= 1 && shifts) {
        const existingShifts = new Set();
        generatedSchedules.forEach(s => {
            if (s.shift_id) existingShifts.add(s.shift_id);
        });
        const sortedShifts = Array.from(existingShifts).map(id => {
            const shift = shifts.find(s => s.id == id);
            return { id, name: shift?.shift_name || `Shift ${id}` };
        }).sort((a, b) => a.name.localeCompare(b.name));

        sortedShifts.forEach(({ id, name }) => {
            const option = document.createElement('option');
            option.value = id;
            option.textContent = name;
            shiftFilter.appendChild(option);
        });
    }
}

// Apply filters and re-render preview
function applyFilters() {
    renderPreview();
}

// Clear all filters
function clearFilters() {
    document.getElementById('filter-class').value = '';
    document.getElementById('filter-date').value = '';
    document.getElementById('filter-subject').value = '';
    document.getElementById('filter-shift').value = '';
    renderPreview();
}

// Update time from shift for a specific class
function updateClassTimeFromShift(classId) {
    // Auto-generate preview when shift changes
    generatePreviewOnChange();
}
</script>
@endsection
