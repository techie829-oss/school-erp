@extends('tenant.layouts.admin')

@section('title', 'Add New Section')

@section('content')
{{-- @var $tenant \App\Models\Tenant --}}
{{-- @var $classes \Illuminate\Support\Collection<\App\Models\SchoolClass> --}}
{{-- @var $teachers \Illuminate\Support\Collection<\App\Models\User> --}}
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Add New Section</h1>
        <p class="mt-1 text-sm text-gray-500">Create a new section for a class</p>
    </div>

    <form action="{{ url('/admin/sections') }}" method="POST" class="max-w-2xl">
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
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700">Class <span class="text-red-500">*</span></label>
                <select name="class_id" id="class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id', request('class_id')) == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="section_name" class="block text-sm font-medium text-gray-700">Section Name <span class="text-red-500">*</span></label>
                <input type="text" name="section_name" id="section_name" value="{{ old('section_name') }}" required placeholder="e.g., A, B, C"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <div>
                <label for="room_number" class="block text-sm font-medium text-gray-700">Room Number</label>
                <input type="text" name="room_number" id="room_number" value="{{ old('room_number') }}" placeholder="e.g., 101, A-25"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <div>
                <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
                <input type="number" name="capacity" id="capacity" value="{{ old('capacity') }}" min="1" max="200" placeholder="e.g., 40"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            </div>

            <div>
                <label for="class_teacher_id" class="block text-sm font-medium text-gray-700">Class Teacher</label>
                <select name="class_teacher_id" id="class_teacher_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">No Class Teacher</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('class_teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">Active</label>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/sections') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Create Section
            </button>
        </div>
    </form>
</div>
@endsection

