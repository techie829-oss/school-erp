@extends('tenant.layouts.admin')

@section('title', 'Add New Class')

@section('content')
{{-- @var $tenant \App\Models\Tenant --}}
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
                    <a href="{{ url('/admin/classes') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Classes</a>
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

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Add New Class</h1>
        <p class="mt-1 text-sm text-gray-500">Create a new class/grade for your institution</p>
    </div>

    <!-- Form -->
    <form action="{{ url('/admin/classes') }}" method="POST" class="max-w-2xl">
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

        <div class="bg-white shadow rounded-lg p-4 sm:p-6 space-y-4 sm:space-y-6">
            <!-- Class Name -->
            <div>
                <label for="class_name" class="block text-sm font-medium text-gray-700">
                    Class Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="class_name" id="class_name" value="{{ old('class_name') }}" required
                    placeholder="e.g., Class 1, Nursery, 11th Science"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">The display name of the class</p>
            </div>

            <!-- Class Numeric -->
            <div>
                <label for="class_numeric" class="block text-sm font-medium text-gray-700">
                    Class Number <span class="text-red-500">*</span>
                </label>
                <input type="number" name="class_numeric" id="class_numeric" value="{{ old('class_numeric') }}" required min="0" max="20"
                    placeholder="e.g., 0, 1, 2, 10, 12"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Numeric value for sorting (0-20). Use 0 for pre-primary classes.</p>
            </div>

            <!-- Class Type -->
            <div>
                <label for="class_type" class="block text-sm font-medium text-gray-700">
                    Class Type <span class="text-red-500">*</span>
                </label>
                <select name="class_type" id="class_type" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">Select Type</option>
                    <option value="school" {{ old('class_type') == 'school' ? 'selected' : '' }}>School (K-10)</option>
                    <option value="college" {{ old('class_type') == 'college' ? 'selected' : '' }}>College (11-12)</option>
                    <option value="both" {{ old('class_type') == 'both' ? 'selected' : '' }}>Both</option>
                </select>
            </div>

            <!-- Info Box for Class Settings -->
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Note:</strong> The fields below (Capacity, Room Number, Class Teacher) are <strong>optional</strong> and are only useful if this class <strong>does not have multiple sections</strong>. If you plan to create sections for this class, these settings should be configured at the section level instead.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Capacity -->
            <div>
                <label for="capacity" class="block text-sm font-medium text-gray-700">
                    Capacity <span class="text-gray-500 text-xs font-normal">(Optional)</span>
                </label>
                <input type="number" name="capacity" id="capacity" value="{{ old('capacity') }}" min="1" max="200"
                    placeholder="e.g., 30, 40, 50"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Maximum number of students for classes without sections</p>
            </div>

            <!-- Room Number -->
            <div>
                <label for="room_number" class="block text-sm font-medium text-gray-700">
                    Room Number <span class="text-gray-500 text-xs font-normal">(Optional)</span>
                </label>
                <input type="text" name="room_number" id="room_number" value="{{ old('room_number') }}" maxlength="50"
                    placeholder="e.g., Room 101, A-12"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Classroom location for classes without sections</p>
            </div>

            <!-- Class Teacher -->
            <div>
                <label for="class_teacher_id" class="block text-sm font-medium text-gray-700">
                    Class Teacher <span class="text-gray-500 text-xs font-normal">(Optional)</span>
                </label>
                <select name="class_teacher_id" id="class_teacher_id"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">No Class Teacher</option>
                    @foreach($teachers ?? [] as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('class_teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500">Assign a teacher as class teacher for classes without sections</p>
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">
                    Active (students can be enrolled)
                </label>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="mt-6 flex flex-col sm:flex-row justify-end gap-3">
            <a href="{{ url('/admin/classes') }}" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Create Class
            </button>
        </div>
    </form>
</div>
@endsection

