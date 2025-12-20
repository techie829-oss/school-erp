@extends('tenant.layouts.admin')

@section('title', 'Edit Exam Shift')

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
                    <a href="{{ url('/admin/examinations/shifts') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Shifts</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Edit Exam Shift
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Update shift: {{ $shift->shift_name }}
            </p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ url('/admin/examinations/shifts/' . $shift->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="bg-white shadow rounded-lg p-6">
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="shift_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Shift Name *
                    </label>
                    <input type="text"
                           id="shift_name"
                           name="shift_name"
                           value="{{ old('shift_name', $shift->shift_name) }}"
                           required
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="shift_code" class="block text-sm font-medium text-gray-700 mb-2">
                        Shift Code
                    </label>
                    <input type="text"
                           id="shift_code"
                           name="shift_code"
                           value="{{ old('shift_code', $shift->shift_code) }}"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Start Time *
                    </label>
                    <input type="time"
                           id="start_time"
                           name="start_time"
                           value="{{ old('start_time', $shift->start_time->format('H:i')) }}"
                           required
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                        End Time *
                    </label>
                    <input type="time"
                           id="end_time"
                           name="end_time"
                           value="{{ old('end_time', $shift->end_time->format('H:i')) }}"
                           required
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Class Ranges (Optional)
                    </label>
                    <div id="class-ranges-container" class="space-y-2">
                        @if($shift->class_ranges && count($shift->class_ranges) > 0)
                            @foreach($shift->class_ranges as $index => $range)
                            <div class="flex items-center gap-2 class-range-row">
                                <input type="number"
                                       name="class_ranges[{{ $index }}][min]"
                                       min="0"
                                       max="20"
                                       value="{{ $range['min'] ?? 0 }}"
                                       placeholder="Min (0 for pre-primary)"
                                       class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <span class="text-gray-500">to</span>
                                <input type="number"
                                       name="class_ranges[{{ $index }}][max]"
                                       min="0"
                                       max="20"
                                       value="{{ $range['max'] ?? 20 }}"
                                       placeholder="Max"
                                       class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <button type="button" onclick="removeClassRange(this)" class="text-red-600 hover:text-red-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            @endforeach
                        @else
                            <div class="flex items-center gap-2 class-range-row">
                                <input type="number"
                                       name="class_ranges[0][min]"
                                       min="0"
                                       max="20"
                                       placeholder="Min (0 for pre-primary)"
                                       class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <span class="text-gray-500">to</span>
                                <input type="number"
                                       name="class_ranges[0][max]"
                                       min="0"
                                       max="20"
                                       placeholder="Max"
                                       class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                <button type="button" onclick="removeClassRange(this)" class="text-red-600 hover:text-red-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @endif
                    </div>
                    <button type="button" onclick="addClassRange()" class="mt-2 text-sm text-primary-600 hover:text-primary-700">
                        + Add Class Range
                    </button>
                </div>

                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">
                        Display Order
                    </label>
                    <input type="number"
                           id="display_order"
                           name="display_order"
                           value="{{ old('display_order', $shift->display_order) }}"
                           min="0"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $shift->is_active) ? 'checked' : '' }}
                               class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Description
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('description', $shift->description) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ url('/admin/examinations/shifts') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    Update Shift
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let rangeIndex = {{ $shift->class_ranges ? count($shift->class_ranges) : 1 }};

function addClassRange() {
    const container = document.getElementById('class-ranges-container');
    const newRow = document.createElement('div');
    newRow.className = 'flex items-center gap-2 class-range-row';
    newRow.innerHTML = `
        <input type="number"
               name="class_ranges[${rangeIndex}][min]"
               min="0"
               max="20"
                                       placeholder="Min (0 for pre-primary)"
               class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
        <span class="text-gray-500">to</span>
        <input type="number"
               name="class_ranges[${rangeIndex}][max]"
               min="0"
               max="20"
               placeholder="Max"
               class="block w-32 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
        <button type="button" onclick="removeClassRange(this)" class="text-red-600 hover:text-red-800">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    container.appendChild(newRow);
    rangeIndex++;
}

function removeClassRange(button) {
    button.closest('.class-range-row').remove();
}
</script>
@endsection


