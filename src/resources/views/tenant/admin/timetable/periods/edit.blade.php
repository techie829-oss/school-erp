@extends('tenant.layouts.admin')

@section('title', 'Edit Period')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/timetable/periods') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Periods</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Edit</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Period</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $period->name ?? 'Period ' . $period->period_number }}</p>
        </div>
    </div>

    <form action="{{ url('/admin/timetable/periods/' . $period->id) }}" method="POST" class="max-w-2xl">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4">
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="period_number" class="block text-sm font-medium text-gray-700">Period Number <span class="text-red-500">*</span></label>
                    <input type="number" name="period_number" id="period_number" value="{{ old('period_number', $period->period_number) }}" min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $period->name) }}" placeholder="e.g., Period 1, Lunch Break" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', \Carbon\Carbon::parse($period->start_time)->format('H:i')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700">End Time <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', \Carbon\Carbon::parse($period->end_time)->format('H:i')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700">Duration (Minutes) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $period->duration_minutes) }}" min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="break_type" class="block text-sm font-medium text-gray-700">Break Type <span class="text-red-500">*</span></label>
                    <select name="break_type" id="break_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="none" {{ old('break_type', $period->break_type) == 'none' ? 'selected' : '' }}>None</option>
                        <option value="short_break" {{ old('break_type', $period->break_type) == 'short_break' ? 'selected' : '' }}>Short Break</option>
                        <option value="lunch_break" {{ old('break_type', $period->break_type) == 'lunch_break' ? 'selected' : '' }}>Lunch Break</option>
                        <option value="assembly" {{ old('break_type', $period->break_type) == 'assembly' ? 'selected' : '' }}>Assembly</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('description', $period->description) }}</textarea>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $period->is_active) ? 'checked' : '' }} class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/timetable/periods') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Update Period</button>
        </div>
    </form>
</div>
@endsection

