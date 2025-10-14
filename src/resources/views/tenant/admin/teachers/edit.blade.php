@extends('tenant.layouts.admin')

@section('title', 'Edit Teacher')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Edit Teacher - {{ $teacher->full_name }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $teacher->employee_id }}
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/teachers/' . $teacher->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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
    <form action="{{ url('/admin/teachers/' . $teacher->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Employee Information -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700">Employee ID</label>
                    <input type="text" name="employee_id" id="employee_id" value="{{ $teacher->employee_id }}" readonly
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm">
                </div>

                <div>
                    <label for="date_of_joining" class="block text-sm font-medium text-gray-700">
                        Date of Joining <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_of_joining" id="date_of_joining" value="{{ old('date_of_joining', $teacher->date_of_joining?->format('Y-m-d')) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('date_of_joining') border-red-300 @enderror">
                </div>

                <div>
                    <label for="date_of_leaving" class="block text-sm font-medium text-gray-700">Date of Leaving</label>
                    <input type="date" name="date_of_leaving" id="date_of_leaving" value="{{ old('date_of_leaving', $teacher->date_of_leaving?->format('Y-m-d')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="active" {{ old('status', $teacher->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="on_leave" {{ old('status', $teacher->status) == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="resigned" {{ old('status', $teacher->status) == 'resigned' ? 'selected' : '' }}>Resigned</option>
                        <option value="retired" {{ old('status', $teacher->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                        <option value="terminated" {{ old('status', $teacher->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
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
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name', $teacher->first_name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('first_name') border-red-300 @enderror">
                </div>

                <div>
                    <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                    <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name', $teacher->middle_name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name', $teacher->last_name) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('last_name') border-red-300 @enderror">
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">
                        Date of Birth <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $teacher->date_of_birth?->format('Y-m-d')) }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('date_of_birth') border-red-300 @enderror">
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700">
                        Gender <span class="text-red-500">*</span>
                    </label>
                    <select name="gender" id="gender" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('gender') border-red-300 @enderror">
                        <option value="male" {{ old('gender', $teacher->gender) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $teacher->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender', $teacher->gender) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="blood_group" class="block text-sm font-medium text-gray-700">Blood Group</label>
                    <select name="blood_group" id="blood_group" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Blood Group</option>
                        @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                            <option value="{{ $bg }}" {{ old('blood_group', $teacher->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" id="category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Category</option>
                        @foreach(['general', 'obc', 'sc', 'st', 'other'] as $cat)
                            <option value="{{ $cat }}" {{ old('category', $teacher->category) == $cat ? 'selected' : '' }}>{{ strtoupper($cat) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="nationality" class="block text-sm font-medium text-gray-700">Nationality</label>
                    <input type="text" name="nationality" id="nationality" value="{{ old('nationality', $teacher->nationality) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700">Religion</label>
                    <input type="text" name="religion" id="religion" value="{{ old('religion', $teacher->religion) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700">Current Photo</label>
                    @if($teacher->photo)
                        <div class="mt-2 flex items-center space-x-4">
                            <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->full_name }}" class="h-20 w-20 rounded-full object-cover">
                            <div>
                                <p class="text-sm text-gray-500">Upload a new photo to replace</p>
                            </div>
                        </div>
                    @endif
                    <input type="file" name="photo" id="photo" accept="image/*" class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                    <p class="mt-1 text-xs text-gray-500">JPEG, PNG, JPG (Max: 2MB)</p>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $teacher->email) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('email') border-red-300 @enderror">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="tel" name="phone" id="phone" value="{{ old('phone', $teacher->phone) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="md:col-span-2">
                    <label for="alternate_phone" class="block text-sm font-medium text-gray-700">Alternate Phone</label>
                    <input type="tel" name="alternate_phone" id="alternate_phone" value="{{ old('alternate_phone', $teacher->alternate_phone) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="md:col-span-2">
                    <h4 class="text-sm font-medium text-gray-900 mb-3 mt-4">Emergency Contact</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name', $teacher->emergency_contact_name) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="emergency_contact_phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="tel" name="emergency_contact_phone" id="emergency_contact_phone" value="{{ old('emergency_contact_phone', $teacher->emergency_contact_phone) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>

                        <div>
                            <label for="emergency_contact_relation" class="block text-sm font-medium text-gray-700">Relation</label>
                            <input type="text" name="emergency_contact_relation" id="emergency_contact_relation" value="{{ old('emergency_contact_relation', $teacher->emergency_contact_relation) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>

            <h4 class="text-sm font-medium text-gray-700 mb-3">Current Address</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label for="current_address" class="block text-sm font-medium text-gray-700">Street Address</label>
                    <textarea name="current_address" id="current_address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('current_address', $teacher->current_address['address'] ?? '') }}</textarea>
                </div>

                <div>
                    <label for="current_city" class="block text-sm font-medium text-gray-700">City</label>
                    <input type="text" name="current_city" id="current_city" value="{{ old('current_city', $teacher->current_address['city'] ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="current_state" class="block text-sm font-medium text-gray-700">State</label>
                    <input type="text" name="current_state" id="current_state" value="{{ old('current_state', $teacher->current_address['state'] ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="current_pincode" class="block text-sm font-medium text-gray-700">Pincode</label>
                    <input type="text" name="current_pincode" id="current_pincode" value="{{ old('current_pincode', $teacher->current_address['pincode'] ?? '') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>

            <div class="flex items-center mb-4">
                <input type="checkbox" name="same_as_current" id="same_as_current" {{ old('same_as_current') ? 'checked' : '' }}
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="same_as_current" class="ml-2 block text-sm text-gray-700">
                    Permanent address same as current address
                </label>
            </div>

            <div id="permanent_address_fields">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Permanent Address</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="permanent_address" class="block text-sm font-medium text-gray-700">Street Address</label>
                        <textarea name="permanent_address" id="permanent_address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('permanent_address', $teacher->permanent_address['address'] ?? '') }}</textarea>
                    </div>

                    <div>
                        <label for="permanent_city" class="block text-sm font-medium text-gray-700">City</label>
                        <input type="text" name="permanent_city" id="permanent_city" value="{{ old('permanent_city', $teacher->permanent_address['city'] ?? '') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="permanent_state" class="block text-sm font-medium text-gray-700">State</label>
                        <input type="text" name="permanent_state" id="permanent_state" value="{{ old('permanent_state', $teacher->permanent_address['state'] ?? '') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>

                    <div>
                        <label for="permanent_pincode" class="block text-sm font-medium text-gray-700">Pincode</label>
                        <input type="text" name="permanent_pincode" id="permanent_pincode" value="{{ old('permanent_pincode', $teacher->permanent_address['pincode'] ?? '') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Details -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Employment Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                    <select name="department_id" id="department_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $teacher->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->department_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="designation" class="block text-sm font-medium text-gray-700">Designation</label>
                    <input type="text" name="designation" id="designation" value="{{ old('designation', $teacher->designation) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="employment_type" class="block text-sm font-medium text-gray-700">
                        Employment Type <span class="text-red-500">*</span>
                    </label>
                    <select name="employment_type" id="employment_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('employment_type') border-red-300 @enderror">
                        @foreach(['permanent', 'contract', 'temporary', 'visiting'] as $type)
                            <option value="{{ $type }}" {{ old('employment_type', $teacher->employment_type) == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="highest_qualification" class="block text-sm font-medium text-gray-700">Highest Qualification</label>
                    <input type="text" name="highest_qualification" id="highest_qualification" value="{{ old('highest_qualification', $teacher->highest_qualification) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="experience_years" class="block text-sm font-medium text-gray-700">Total Experience (Years)</label>
                    <input type="number" name="experience_years" id="experience_years" value="{{ old('experience_years', $teacher->experience_years) }}" step="0.1" min="0"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="salary_amount" class="block text-sm font-medium text-gray-700">Salary Amount</label>
                    <input type="number" name="salary_amount" id="salary_amount" value="{{ old('salary_amount', $teacher->salary_amount) }}" step="0.01" min="0"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subjects</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($subjects as $subject)
                            <div class="flex items-center">
                                <input type="checkbox" name="subjects[]" id="subject_{{ $subject->id }}" value="{{ $subject->id }}"
                                    {{ in_array($subject->id, old('subjects', $teacher->subjects->pluck('id')->toArray())) ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <label for="subject_{{ $subject->id }}" class="ml-2 block text-sm text-gray-700">
                                    {{ $subject->subject_name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Details -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Financial Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="bank_name" class="block text-sm font-medium text-gray-700">Bank Name</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $teacher->bank_name) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="bank_account_number" class="block text-sm font-medium text-gray-700">Account Number</label>
                    <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number', $teacher->bank_account_number) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="bank_ifsc_code" class="block text-sm font-medium text-gray-700">IFSC Code</label>
                    <input type="text" name="bank_ifsc_code" id="bank_ifsc_code" value="{{ old('bank_ifsc_code', $teacher->bank_ifsc_code) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="pan_number" class="block text-sm font-medium text-gray-700">PAN Number</label>
                    <input type="text" name="pan_number" id="pan_number" value="{{ old('pan_number', $teacher->pan_number) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="aadhar_number" class="block text-sm font-medium text-gray-700">Aadhar Number</label>
                    <input type="text" name="aadhar_number" id="aadhar_number" value="{{ old('aadhar_number', $teacher->aadhar_number) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Notes</h3>
            <textarea name="notes" id="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('notes', $teacher->notes) }}</textarea>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3">
            <a href="{{ url('/admin/teachers/' . $teacher->id) }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Cancel
            </a>
            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Update Teacher
            </button>
        </div>
    </form>
</div>

<script>
    // Toggle permanent address fields
    document.getElementById('same_as_current').addEventListener('change', function() {
        const permanentFields = document.getElementById('permanent_address_fields');
        if (this.checked) {
            permanentFields.style.display = 'none';
        } else {
            permanentFields.style.display = 'block';
        }
    });

    // Initialize on page load
    if (document.getElementById('same_as_current').checked) {
        document.getElementById('permanent_address_fields').style.display = 'none';
    }
</script>
@endsection

