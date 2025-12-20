@extends('tenant.layouts.admin')

@section('title', 'Add Student')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
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
                    <a href="{{ url('/admin/students') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Students</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Add New</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Add New Student
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Enter student information to enroll
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/students') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
        </div>
    </div>

    <!-- Error Messages -->
    @if(session('error') || $errors->any())
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">{{ session('error') ?? 'There were errors with your submission' }}</h3>
                @if($errors->any())
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Form -->
    <form action="{{ url('/admin/students') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Admission Information -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Admission Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="admission_number" class="block text-sm font-medium text-gray-700">
                        Admission Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="admission_number" id="admission_number" value="{{ old('admission_number', $admissionNumber) }}" required readonly
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Auto-generated</p>
                </div>

                <div>
                    <label for="admission_date" class="block text-sm font-medium text-gray-700">
                        Admission Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="admission_date" id="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('admission_date') border-red-300 @enderror">
                </div>

                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700">
                        Academic Year <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year', date('Y') . '-' . (date('Y') + 1)) }}" required placeholder="2024-2025"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('academic_year') border-red-300 @enderror">
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('first_name') border-red-300 @enderror">
                </div>

                <div>
                    <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('last_name') border-red-300 @enderror">
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">
                        Date of Birth <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('date_of_birth') border-red-300 @enderror">
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">
                        Gender <span class="text-red-500">*</span>
                    </label>
                    <select name="gender" id="gender" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('gender') border-red-300 @enderror">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="blood_group" class="block text-sm font-medium text-gray-700">Blood Group</label>
                    <select name="blood_group" id="blood_group" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Blood Group</option>
                        <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                        <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                        <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                        <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                        <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                        <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                        <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                        <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                    </select>
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select name="category" id="category" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('category') border-red-300 @enderror">
                        <option value="general" {{ old('category', 'general') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="obc" {{ old('category') == 'obc' ? 'selected' : '' }}>OBC</option>
                        <option value="sc" {{ old('category') == 'sc' ? 'selected' : '' }}>SC</option>
                        <option value="st" {{ old('category') == 'st' ? 'selected' : '' }}>ST</option>
                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700">Religion</label>
                    <input type="text" name="religion" id="religion" value="{{ old('religion') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="nationality" class="block text-sm font-medium text-gray-700">Nationality</label>
                    <input type="text" name="nationality" id="nationality" value="{{ old('nationality', 'Indian') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="md:col-span-3">
                    <label for="photo" class="block text-sm font-medium text-gray-700">Student Photo</label>
                    <input type="file" name="photo" id="photo" accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="mt-1 text-xs text-gray-500">JPG, PNG up to 2MB. Recommended: passport size photo</p>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('email') border-red-300 @enderror">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('phone') border-red-300 @enderror">
                </div>

                <div class="md:col-span-2">
                    <label for="current_address" class="block text-sm font-medium text-gray-700">Current Address</label>
                    <textarea name="current_address" id="current_address" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('current_address') }}</textarea>
                </div>

                <div>
                    <label for="current_city" class="block text-sm font-medium text-gray-700">City</label>
                    <input type="text" name="current_city" id="current_city" value="{{ old('current_city') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="current_state" class="block text-sm font-medium text-gray-700">State</label>
                    <input type="text" name="current_state" id="current_state" value="{{ old('current_state') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="current_pincode" class="block text-sm font-medium text-gray-700">Pincode</label>
                    <input type="text" name="current_pincode" id="current_pincode" value="{{ old('current_pincode') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="md:col-span-3">
                    <div class="flex items-center">
                        <input type="checkbox" name="same_as_current" id="same_as_current" value="1" {{ old('same_as_current') ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <label for="same_as_current" class="ml-2 block text-sm text-gray-700">
                            Permanent address is same as current address
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parent/Guardian Information -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Parent/Guardian Information</h3>

            <!-- Father Details -->
            <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Father's Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="father_name" class="block text-sm font-medium text-gray-700">Father's Name</label>
                        <input type="text" name="father_name" id="father_name" value="{{ old('father_name') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="father_occupation" class="block text-sm font-medium text-gray-700">Occupation</label>
                        <input type="text" name="father_occupation" id="father_occupation" value="{{ old('father_occupation') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="father_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="father_phone" id="father_phone" value="{{ old('father_phone') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('father_phone') border-red-300 @enderror">
                    </div>
                    <div>
                        <label for="father_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="father_email" id="father_email" value="{{ old('father_email') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('father_email') border-red-300 @enderror">
                    </div>
                </div>
            </div>

            <!-- Mother Details -->
            <div>
                <h4 class="text-sm font-medium text-gray-700 mb-3">Mother's Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="mother_name" class="block text-sm font-medium text-gray-700">Mother's Name</label>
                        <input type="text" name="mother_name" id="mother_name" value="{{ old('mother_name') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="mother_occupation" class="block text-sm font-medium text-gray-700">Occupation</label>
                        <input type="text" name="mother_occupation" id="mother_occupation" value="{{ old('mother_occupation') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="mother_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="mother_phone" id="mother_phone" value="{{ old('mother_phone') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('mother_phone') border-red-300 @enderror">
                    </div>
                    <div>
                        <label for="mother_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="mother_email" id="mother_email" value="{{ old('mother_email') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('mother_email') border-red-300 @enderror">
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Academic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="current_class_id" class="block text-sm font-medium text-gray-700">
                        Class <span class="text-red-500">*</span>
                    </label>
                    <select name="current_class_id" id="current_class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('current_class_id') border-red-300 @enderror">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('current_class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="current_section_id" class="block text-sm font-medium text-gray-700">Section</label>
                    <select name="current_section_id" id="current_section_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Section</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ old('current_section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->schoolClass->class_name }} - {{ $section->section_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="roll_number" class="block text-sm font-medium text-gray-700">Roll Number</label>
                    <input type="text" name="roll_number" id="roll_number" value="{{ old('roll_number') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="md:col-span-3">
                    <label for="previous_school_name" class="block text-sm font-medium text-gray-700">Previous School Name</label>
                    <input type="text" name="previous_school_name" id="previous_school_name" value="{{ old('previous_school_name') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Subject Assignment -->
        <div class="bg-white shadow rounded-lg p-6 mb-6" id="subject-assignment-section" style="display: none;">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Subject Assignment</h3>

            <div id="subject-assignment-content">
                <p class="text-sm text-gray-500 mb-4">Please select a class first to see subject assignment options.</p>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-3">
            <a href="{{ url('/admin/students') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Add Student
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('current_class_id');
    const sectionSelect = document.getElementById('current_section_id');
    const subjectSection = document.getElementById('subject-assignment-section');
    const subjectContent = document.getElementById('subject-assignment-content');

    // Settings from backend
    const classSubjectMode = @json($classSubjectMode ?? 'class_wise');
    const sectionSubjectMode = @json($sectionSubjectMode ?? 'section_wise');
    const allSubjects = @json($allSubjects ?? []);
    const classes = @json($classes ?? []);
    const sections = @json($sections ?? []);

    function updateSubjectAssignment() {
        const classId = classSelect.value;
        const sectionId = sectionSelect.value;

        if (!classId) {
            subjectSection.style.display = 'none';
            return;
        }

        const selectedClass = classes.find(c => c.id == classId);
        if (!selectedClass) {
            subjectSection.style.display = 'none';
            return;
        }

        const hasSections = selectedClass.has_sections;
        const allowStudentWise = hasSections && sectionId
            ? (sectionSubjectMode === 'student_wise')
            : (classSubjectMode === 'student_wise');

        subjectSection.style.display = 'block';

        if (allowStudentWise) {
            // Show subject selection checkboxes
            let html = `
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Select Subjects for Academic Year: <strong id="academic-year-display">{{ old('academic_year', date('Y') . '-' . (date('Y') + 1)) }}</strong>
                </label>
                <p class="text-xs text-gray-500 mb-3">
                    You can assign individual subjects to this student. Subjects are assigned per academic year.
                </p>
                <div class="border border-gray-300 rounded-md p-4 max-h-64 overflow-y-auto bg-gray-50">
                    <div class="space-y-2">
            `;

            allSubjects.forEach(subject => {
                html += `
                    <label class="flex items-center p-2 hover:bg-white rounded cursor-pointer">
                        <input type="checkbox" name="subjects[]" value="${subject.id}"
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">
                            ${subject.subject_name}
                            ${subject.subject_code ? `<span class="text-gray-500">(${subject.subject_code})</span>` : ''}
                        </span>
                    </label>
                `;
            });

            html += `
                    </div>
                </div>
            `;

            subjectContent.innerHTML = html;
        } else {
            // Show read-only subjects from class/section
            const subjectsFrom = hasSections && sectionId ? 'section' : 'class';
            let html = `
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Subjects for Academic Year: <strong id="academic-year-display-2">{{ old('academic_year', date('Y') . '-' . (date('Y') + 1)) }}</strong>
                </label>
                <p class="text-xs text-gray-500 mb-3">
                    Subjects are assigned at the ${subjectsFrom} level.
                    All students in this ${subjectsFrom} will have the same subjects.
                </p>
                <div class="border border-gray-300 rounded-md p-4 bg-gray-50">
                    <p class="text-sm text-gray-500 text-center py-4">
                        <svg class="inline h-5 w-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        Subjects will be automatically assigned from the selected ${subjectsFrom}.
                        Please ensure subjects are assigned to the ${subjectsFrom} in ${subjectsFrom === 'section' ? 'section' : 'class'} settings.
                    </p>
                </div>
            `;

            subjectContent.innerHTML = html;
        }
    }

    const academicYearInput = document.getElementById('academic_year');

    classSelect.addEventListener('change', updateSubjectAssignment);
    sectionSelect.addEventListener('change', updateSubjectAssignment);
    academicYearInput.addEventListener('input', function() {
        const yearDisplays = document.querySelectorAll('#academic-year-display, #academic-year-display-2');
        yearDisplays.forEach(el => {
            if (el) el.textContent = academicYearInput.value || '{{ date('Y') . '-' . (date('Y') + 1) }}';
        });
    });

    // Initial load if class is pre-selected
    if (classSelect.value) {
        updateSubjectAssignment();
    }
});
</script>
@endsection

