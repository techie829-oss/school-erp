@extends('tenant.layouts.admin')

@section('title', 'Holiday Management')

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
                    <a href="{{ url('/admin/attendance/students') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">
                        Student Attendance
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Holiday Management</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Holiday Management
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Manage school holidays so they are excluded from attendance working days.
            </p>
        </div>
        <div class="mt-4 md:mt-0">
            <form method="GET" action="{{ url('/admin/attendance/holidays') }}" class="flex items-center space-x-2">
                <label for="year" class="text-sm text-gray-600">Year</label>
                <input type="number" id="year" name="year" value="{{ $year }}" class="block w-24 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    Go
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add / Edit Holiday -->
        <div class="lg:col-span-1">
            <div class="bg-white shadow rounded-lg p-5">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Add / Update Holiday</h3>
                <form method="POST" action="{{ url('/admin/attendance/holidays') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="holiday_date" class="block text-sm font-medium text-gray-700">Date *</label>
                        <input type="date" id="holiday_date" name="date" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="holiday_title" class="block text-sm font-medium text-gray-700">Title *</label>
                        <input type="text" id="holiday_title" name="title" required maxlength="255" placeholder="Independence Day" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="holiday_type" class="block text-sm font-medium text-gray-700">Scope / Type</label>
                        <select id="holiday_type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">Select type</option>
                            <option value="school">Whole School Holiday</option>
                            <option value="students_only">Students Only Holiday</option>
                            <option value="exam">Exam / Result Day</option>
                            <option value="event">Event</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="flex items-center">
                        <input id="holiday_full_day" name="is_full_day" type="checkbox" value="1" checked class="h-4 w-4 text-primary-600 border-gray-300 rounded">
                        <label for="holiday_full_day" class="ml-2 block text-sm text-gray-700">
                            Full day holiday (uncheck for half-day / partial)
                        </label>
                    </div>
                    <div>
                        <label for="holiday_notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea id="holiday_notes" name="notes" rows="2" maxlength="1000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Optional notes (e.g. instructions for staff/students)"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Specific Classes (optional)</label>
                        <select name="class_ids[]" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-xs">
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->class_name }} ({{ $class->class_numeric }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-[11px] text-gray-500">
                            Leave empty for all classes. Select one or more classes if the holiday is only for specific standards (e.g. Class 1–5).
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Specific Sections (optional)</label>
                        <select name="section_ids[]" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-xs">
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}">
                                    {{ $section->schoolClass->class_name ?? 'Class' }} - {{ $section->section_name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-[11px] text-gray-500">
                            Use this if only some sections of a class are on holiday. If both class and sections are selected, section rules are more specific.
                        </p>
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                            Save Holiday
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        If a holiday already exists for the selected date, it will be updated instead of creating a duplicate.
                    </p>
                </form>
            </div>
        </div>

        <!-- Holiday List -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow rounded-lg">
                <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-900">
                        Holidays for {{ $year }}
                    </h3>
                    <span class="text-xs text-gray-500">
                        Total: {{ $holidays->count() }}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Date</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Title</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Scope</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Day Type</th>
                                <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase tracking-wider text-xs">Applies To</th>
                                <th class="px-4 py-2 text-right font-medium text-gray-500 uppercase tracking-wider text-xs">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($holidays as $holiday)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-gray-900">
                                        {{ $holiday->date->format('d M Y (D)') }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-gray-900">
                                        {{ $holiday->title }}
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-gray-700">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-gray-100 text-gray-700">
                                            {{ $holiday->scope_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-gray-700">
                                        @if($holiday->is_full_day)
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-800">Full Day</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-800">Half Day</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-gray-700 text-xs">
                                        @php
                                            $classScope = $holiday->scopes->whereNotNull('class_id')->pluck('schoolClass.class_name')->unique()->values();
                                            $sectionScope = $holiday->scopes->whereNotNull('section_id')->map(function($s) {
                                                return ($s->schoolClass->class_name ?? 'Class') . ' - ' . $s->section->section_name;
                                            })->unique()->values();
                                        @endphp
                                        @if($holiday->scopes->isEmpty())
                                            All Classes / Sections
                                        @else
                                            @if($classScope->count())
                                                Classes: {{ $classScope->join(', ') }}@if($sectionScope->count()) ; @endif
                                            @endif
                                            @if($sectionScope->count())
                                                Sections: {{ $sectionScope->join(', ') }}
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="{{ url('/admin/attendance/holidays/' . $holiday->id) }}" method="POST" onsubmit="return confirm('Delete this holiday?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 text-xs">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-sm text-gray-500">
                                        No holidays found for this year.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


