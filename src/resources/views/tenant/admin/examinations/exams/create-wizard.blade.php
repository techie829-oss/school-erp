@extends('tenant.layouts.admin')

@section('title', 'Exam Setup Wizard')

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
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Setup Wizard</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Exam Setup Wizard</h1>
        <p class="mt-1 text-sm text-gray-500">Create exam and schedules in one guided flow</p>
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

    <!-- Wizard Form -->
    <form action="{{ url('/admin/examinations/exams/wizard') }}" method="POST" id="examWizardForm">
        @csrf

        <!-- Step Indicator -->
        <div class="mb-8">
            <div class="flex items-center">
                <div class="flex items-center relative flex-1">
                    <div id="step-indicator-1" class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-primary-600 bg-primary-600 text-white z-10">
                        <span class="text-sm font-semibold">1</span>
                    </div>
                    <div class="absolute top-5 left-10 right-0 h-0.5 bg-gray-300"></div>
                </div>
                <div class="flex-1 ml-4">
                    <p class="text-sm font-medium text-gray-900">Basic Info</p>
                    <p class="text-xs text-gray-500">Exam name, type, dates</p>
                </div>
            </div>
            <div class="flex items-center mt-4">
                <div class="flex items-center relative flex-1">
                    <div id="step-indicator-2" class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-gray-300 bg-white text-gray-500 z-10">
                        <span class="text-sm font-semibold">2</span>
                    </div>
                    <div class="absolute top-5 left-10 right-0 h-0.5 bg-gray-300"></div>
                </div>
                <div class="flex-1 ml-4">
                    <p class="text-sm font-medium text-gray-500">Classes & Subjects</p>
                    <p class="text-xs text-gray-400">Select classes and subjects</p>
                </div>
            </div>
            <div class="flex items-center mt-4">
                <div class="flex items-center relative flex-1">
                    <div id="step-indicator-3" class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-gray-300 bg-white text-gray-500 z-10">
                        <span class="text-sm font-semibold">3</span>
                    </div>
                    <div class="absolute top-5 left-10 right-0 h-0.5 bg-gray-300"></div>
                </div>
                <div class="flex-1 ml-4">
                    <p class="text-sm font-medium text-gray-500">Schedule Setup</p>
                    <p class="text-xs text-gray-400">Configure schedule template</p>
                </div>
            </div>
            <div class="flex items-center mt-4">
                <div class="flex items-center">
                    <div id="step-indicator-4" class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-gray-300 bg-white text-gray-500">
                        <span class="text-sm font-semibold">4</span>
                    </div>
                </div>
                <div class="flex-1 ml-4">
                    <p class="text-sm font-medium text-gray-500">Review & Create</p>
                    <p class="text-xs text-gray-400">Summary and finalize</p>
                </div>
            </div>
        </div>

        <!-- Step 1: Basic Info -->
        <div id="step-1" class="step-content">
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Step 1: Basic Exam Information</h2>
                <p class="text-sm text-gray-500 mb-4">Enter the basic details for your exam.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="exam_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Exam Name *
                        </label>
                        <input type="text"
                               id="exam_name"
                               name="exam_name"
                               required
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="exam_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Exam Type *
                        </label>
                        <select id="exam_type"
                                name="exam_type"
                                required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">Select Type</option>
                            <option value="unit_test">Unit Test</option>
                            <option value="mid_term">Mid Term</option>
                            <option value="final">Final</option>
                            <option value="quiz">Quiz</option>
                            <option value="assignment">Assignment</option>
                            <option value="preliminary">Preliminary</option>
                        </select>
                    </div>

                    <div>
                        <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">
                            Academic Year
                        </label>
                        <input type="text"
                               id="academic_year"
                               name="academic_year"
                               placeholder="e.g., 2024-2025"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select id="status"
                                name="status"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="draft">Draft</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="ongoing">Ongoing</option>
                        </select>
                    </div>

                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Start Date
                        </label>
                        <input type="date"
                               id="start_date"
                               name="start_date"
                               min="{{ now()->format('Y-m-d') }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                            End Date
                        </label>
                        <input type="date"
                               id="end_date"
                               name="end_date"
                               min="{{ now()->format('Y-m-d') }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="3"
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" onclick="goToStep(2)" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                        Next: Select Classes & Subjects
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 2: Classes & Subjects -->
        <div id="step-2" class="step-content hidden">
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Step 2: Select Classes & Subjects</h2>
                <p class="text-sm text-gray-500 mb-4">Select the classes, sections, and subjects for this exam.</p>

                <div class="space-y-4 mb-6">
                    @foreach($classes as $class)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <label class="flex items-start">
                            <input type="checkbox"
                                   name="class_ids[]"
                                   value="{{ $class->id }}"
                                   class="mt-1 h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded class-checkbox"
                                   data-class-id="{{ $class->id }}"
                                   data-has-sections="{{ $class->has_sections ? '1' : '0' }}">
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $class->class_name }}</p>
                                        @if($class->has_sections)
                                            <p class="text-xs text-gray-500 mt-1">{{ $class->sections->count() }} section{{ $class->sections->count() != 1 ? 's' : '' }}</p>
                                        @endif
                                    </div>
                                </div>

                                @if($class->has_sections && $class->sections->count() > 0)
                                <div class="mt-3 ml-6 space-y-2 sections-container" style="display: none;">
                                    <p class="text-xs font-medium text-gray-700">Select Sections:</p>
                                    @foreach($class->sections as $section)
                                    <label class="flex items-center">
                                        <input type="checkbox"
                                               name="section_ids[]"
                                               value="{{ $section->id }}"
                                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded section-checkbox"
                                               data-class-id="{{ $class->id }}">
                                        <span class="ml-2 text-sm text-gray-700">{{ $section->section_name }}</span>
                                    </label>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </label>
                    </div>
                    @endforeach
                </div>

                <div id="subjects-container" class="border border-gray-200 rounded-lg p-4">
                    <p class="text-sm text-gray-500 text-center py-8">Please select classes/sections first</p>
                </div>

                <div class="mt-6 flex justify-between">
                    <button type="button" onclick="goToStep(1)" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Back
                    </button>
                    <button type="button" onclick="goToStep(3)" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                        Next: Schedule Setup
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 3: Schedule Setup -->
        <div id="step-3" class="step-content hidden">
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Step 3: Schedule Setup</h2>
                <p class="text-sm text-gray-500 mb-4">Configure default values for all schedules. You can adjust individual schedules in the review step.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="default_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Default Exam Date
                        </label>
                        <input type="date"
                               id="default_date"
                               name="default_date"
                               min="{{ now()->format('Y-m-d') }}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="default_duration" class="block text-sm font-medium text-gray-700 mb-2">
                            Default Duration (minutes)
                        </label>
                        <input type="number"
                               id="default_duration"
                               name="default_duration"
                               value="90"
                               min="30"
                               max="300"
                               step="15"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="default_start_time" class="block text-sm font-medium text-gray-700 mb-2">
                            Default Start Time
                        </label>
                        <input type="time"
                               id="default_start_time"
                               name="default_start_time"
                               value="09:00"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="default_max_marks" class="block text-sm font-medium text-gray-700 mb-2">
                            Default Max Marks
                        </label>
                        <input type="number"
                               id="default_max_marks"
                               name="default_max_marks"
                               value="100"
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
                               value="33"
                               min="0"
                               step="1"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                </div>

                <div class="mt-6 flex justify-between">
                    <button type="button" onclick="goToStep(2)" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Back
                    </button>
                    <button type="button" onclick="goToStep(4)" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                        Next: Review & Create
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 4: Review & Create -->
        <div id="step-4" class="step-content hidden">
            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Step 4: Review & Create</h2>
                <p class="text-sm text-gray-500 mb-4">Review all information before creating the exam and schedules.</p>

                <div id="review-container" class="space-y-4">
                    <p class="text-sm text-gray-500 text-center py-8">Generating preview...</p>
                </div>

                <div class="mt-6 flex justify-between">
                    <button type="button" onclick="goToStep(3)" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Back
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        Create Exam & Schedules
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const classSubjects = @json($classSubjects ?? []);
const sectionSubjects = @json($sectionSubjects ?? []);
const subjects = @json($subjects ?? []);
const classesData = @json($classes ?? []);

let currentStep = 1;
let selectedClasses = [];
let selectedSections = [];
let selectedSubjects = [];
let generatedSchedules = [];

function goToStep(step) {
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('step-' + step).classList.remove('hidden');
    updateStepIndicators(step);
    currentStep = step;

    if (step === 2) {
        loadSubjects();
    } else if (step === 4) {
        generatePreview();
    }
}

function updateStepIndicators(activeStep) {
    for (let i = 1; i <= 4; i++) {
        const indicator = document.getElementById(`step-indicator-${i}`);
        if (indicator) {
            indicator.classList.remove('border-gray-300', 'bg-white', 'text-gray-500', 'border-green-600', 'bg-green-600', 'border-primary-600', 'bg-primary-600', 'text-white');

            if (i < activeStep) {
                indicator.classList.add('border-green-600', 'bg-green-600', 'text-white');
            } else if (i === activeStep) {
                indicator.classList.add('border-primary-600', 'bg-primary-600', 'text-white');
            } else {
                indicator.classList.add('border-gray-300', 'bg-white', 'text-gray-500');
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.class-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const classId = this.dataset.classId;
            const sectionsContainer = this.closest('.border').querySelector('.sections-container');

            if (this.checked) {
                if (sectionsContainer) {
                    sectionsContainer.style.display = 'block';
                }
            } else {
                if (sectionsContainer) {
                    sectionsContainer.style.display = 'none';
                    sectionsContainer.querySelectorAll('.section-checkbox').forEach(cb => cb.checked = false);
                }
            }
        });
    });
});

function loadSubjects() {
    selectedClasses = Array.from(document.querySelectorAll('.class-checkbox:checked')).map(cb => parseInt(cb.value));
    selectedSections = Array.from(document.querySelectorAll('.section-checkbox:checked')).map(cb => parseInt(cb.value));

    const container = document.getElementById('subjects-container');

    if (selectedClasses.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 text-center py-8">Please select at least one class</p>';
        return;
    }

    let availableSubjectIds = new Set();

    selectedClasses.forEach(classId => {
        if (classSubjects[classId]) {
            classSubjects[classId].forEach(subId => availableSubjectIds.add(subId));
        }
    });

    selectedSections.forEach(sectionId => {
        if (sectionSubjects[sectionId]) {
            sectionSubjects[sectionId].forEach(subId => availableSubjectIds.add(subId));
        }
    });

    if (availableSubjectIds.size === 0) {
        container.innerHTML = '<p class="text-sm text-gray-500 text-center py-8">No subjects found for selected classes/sections.</p>';
        return;
    }

    let html = '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">';
    subjects.forEach(subject => {
        if (availableSubjectIds.has(subject.id)) {
            html += `
                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                    <input type="checkbox"
                           name="subject_ids[]"
                           value="${subject.id}"
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded subject-checkbox">
                    <span class="ml-3 text-sm text-gray-700">${subject.subject_name}</span>
                    ${subject.subject_code ? `<span class="ml-2 text-xs text-gray-500">(${subject.subject_code})</span>` : ''}
                </label>
            `;
        }
    });
    html += '</div>';

    container.innerHTML = html;
}

function generatePreview() {
    selectedSubjects = Array.from(document.querySelectorAll('.subject-checkbox:checked')).map(cb => parseInt(cb.value));

    if (selectedClasses.length === 0 || selectedSubjects.length === 0) {
        document.getElementById('review-container').innerHTML = '<p class="text-sm text-red-500 text-center py-8">Please complete previous steps</p>';
        return;
    }

    const examName = document.getElementById('exam_name').value || 'N/A';
    const examType = document.getElementById('exam_type').value || 'N/A';
    const startDate = document.getElementById('start_date').value || 'N/A';
    const endDate = document.getElementById('end_date').value || 'N/A';

    const defaultDate = document.getElementById('default_date').value || startDate;
    const defaultStartTime = document.getElementById('default_start_time').value || '09:00';
    const defaultDuration = parseInt(document.getElementById('default_duration').value) || 90;
    const defaultMaxMarks = parseFloat(document.getElementById('default_max_marks').value) || 100;
    const defaultPassingMarks = parseFloat(document.getElementById('default_passing_marks').value) || null;

    const startTime = defaultStartTime.split(':');
    const startMinutes = parseInt(startTime[0]) * 60 + parseInt(startTime[1]);
    const endMinutes = startMinutes + defaultDuration;
    const endHours = Math.floor(endMinutes / 60);
    const endMins = endMinutes % 60;
    const defaultEndTime = `${String(endHours).padStart(2, '0')}:${String(endMins).padStart(2, '0')}`;

    generatedSchedules = [];
    let scheduleIndex = 0;

    selectedClasses.forEach(classId => {
        const classData = classesData.find(c => c.id == classId);
        if (!classData) return;

        const classSections = selectedSections.filter(sectionId => {
            const section = classesData.flatMap(c => c.sections || []).find(s => s && s.id == sectionId);
            return section && section.class_id == classId;
        });

        if (classData.has_sections && classSections.length > 0) {
            classSections.forEach(sectionId => {
                selectedSubjects.forEach(subjectId => {
                    const hasSubject = (sectionSubjects[sectionId] && sectionSubjects[sectionId].includes(subjectId)) ||
                                      (classSubjects[classId] && classSubjects[classId].includes(subjectId));

                    if (hasSubject) {
                        generatedSchedules.push({
                            class_id: classId,
                            section_id: sectionId,
                            subject_id: subjectId,
                            exam_date: defaultDate,
                            start_time: defaultStartTime,
                            end_time: defaultEndTime,
                            max_marks: defaultMaxMarks,
                            passing_marks: defaultPassingMarks,
                            index: scheduleIndex++
                        });
                    }
                });
            });
        } else {
            selectedSubjects.forEach(subjectId => {
                if (classSubjects[classId] && classSubjects[classId].includes(subjectId)) {
                    generatedSchedules.push({
                        class_id: classId,
                        section_id: null,
                        subject_id: subjectId,
                        exam_date: defaultDate,
                        start_time: defaultStartTime,
                        end_time: defaultEndTime,
                        max_marks: defaultMaxMarks,
                        passing_marks: defaultPassingMarks,
                        index: scheduleIndex++
                    });
                }
            });
        }
    });

    renderPreview(examName, examType, startDate, endDate);
}

function renderPreview(examName, examType, startDate, endDate) {
    const container = document.getElementById('review-container');

    if (generatedSchedules.length === 0) {
        container.innerHTML = '<p class="text-sm text-red-500 text-center py-8">No schedules to generate. Please check your selections.</p>';
        return;
    }

    let html = `
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="text-sm font-medium text-blue-900 mb-2">Exam Summary</h3>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div><span class="font-medium">Name:</span> ${examName}</div>
                <div><span class="font-medium">Type:</span> ${examType}</div>
                <div><span class="font-medium">Start Date:</span> ${startDate}</div>
                <div><span class="font-medium">End Date:</span> ${endDate}</div>
            </div>
        </div>
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
            <p class="text-sm font-medium text-green-900">Total Schedules to Create: <strong>${generatedSchedules.length}</strong></p>
        </div>
        <div class="space-y-3 max-h-96 overflow-y-auto">
    `;

    generatedSchedules.forEach((schedule, idx) => {
        const classData = classesData.find(c => c.id == schedule.class_id);
        const section = schedule.section_id ? classesData.flatMap(c => c.sections || []).find(s => s && s.id == schedule.section_id) : null;
        const subject = subjects.find(s => s.id == schedule.subject_id);

        html += `
            <div class="border border-gray-200 rounded-lg p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <input type="hidden" name="schedules[${idx}][class_id]" value="${schedule.class_id}">
                    <input type="hidden" name="schedules[${idx}][section_id]" value="${schedule.section_id || ''}">
                    <input type="hidden" name="schedules[${idx}][subject_id]" value="${schedule.subject_id}">

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Class / Section</label>
                        <p class="text-sm font-medium text-gray-900">${classData ? classData.class_name : 'N/A'}${section ? ' - ' + section.section_name : ''}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Subject</label>
                        <p class="text-sm font-medium text-gray-900">${subject?.subject_name || 'N/A'}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
                        <input type="date"
                               name="schedules[${idx}][exam_date]"
                               value="${schedule.exam_date}"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Start</label>
                            <input type="time"
                                   name="schedules[${idx}][start_time]"
                                   value="${schedule.start_time}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">End</label>
                            <input type="time"
                                   name="schedules[${idx}][end_time]"
                                   value="${schedule.end_time}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Max Marks</label>
                        <input type="number"
                               name="schedules[${idx}][max_marks]"
                               value="${schedule.max_marks}"
                               min="1"
                               step="1"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Passing Marks</label>
                        <input type="number"
                               name="schedules[${idx}][passing_marks]"
                               value="${schedule.passing_marks || ''}"
                               min="0"
                               step="1"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Room Number (Optional)</label>
                        <input type="text"
                               name="schedules[${idx}][room_number]"
                               placeholder="e.g., 101"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm text-xs">
                    </div>
                </div>
            </div>
        `;
    });

    html += '</div>';
    container.innerHTML = html;
}
</script>
@endsection
