@extends('tenant.layouts.admin')

@section('title', 'Add New Class')

@section('content')
{{-- @var $tenant \App\Models\Tenant --}}
<div class="px-4 sm:px-6 lg:px-8 py-8">
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

        <div class="bg-white shadow rounded-lg p-6 space-y-6">
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
                <input type="number" name="class_numeric" id="class_numeric" value="{{ old('class_numeric') }}" required min="1" max="20"
                    placeholder="e.g., 1, 2, 10, 12"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Numeric value for sorting (1-20). Use 0 for pre-primary classes.</p>
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
        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/classes') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Create Class
            </button>
        </div>
    </form>
</div>
@endsection

