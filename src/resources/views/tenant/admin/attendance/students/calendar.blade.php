@extends('tenant.layouts.admin')

@section('title', 'Class Attendance Calendar')

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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Class Calendar</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Class Attendance Calendar
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Visual monthly calendar for a class / section (Option B)
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/attendance/students') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Overview
            </a>
            <a href="{{ url('/admin/attendance/students/mark') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                Mark Attendance
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4 md:p-6">
        <form method="GET" action="{{ url('/admin/attendance/students/calendar') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                <select name="class_id" id="class_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ (string)$classId === (string)$class->id ? 'selected' : '' }}>
                            {{ $class->class_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="section_id" class="block text-sm font-medium text-gray-700">Section</label>
                <select name="section_id" id="section_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" data-class-id="{{ $section->class_id }}" {{ (string)$sectionId === (string)$section->id ? 'selected' : '' }}>
                            {{ $section->schoolClass->class_name ?? '' }} - {{ $section->section_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                <select name="month" id="month" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ (int)$month === $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create(null, $m, 1)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <div class="flex-1">
                    <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                    <input type="number" name="year" id="year" value="{{ $year }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    Apply
                </button>
            </div>
        </form>
    </div>

    <!-- Calendar -->
    <div class="bg-white shadow rounded-lg p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ $currentMonth->format('F Y') }}
                </h3>
                <p class="text-sm text-gray-500">
                    {{ $classId ? 'Class selected' : 'All classes' }}
                    @if($sectionId)
                        · Section selected
                    @endif
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ url('/admin/attendance/students/calendar') . '?' . http_build_query(['class_id' => $classId, 'section_id' => $sectionId, 'month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
                   class="inline-flex items-center p-2 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <a href="{{ url('/admin/attendance/students/calendar') . '?' . http_build_query(['class_id' => $classId, 'section_id' => $sectionId, 'month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
                   class="inline-flex items-center p-2 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <div class="border border-gray-200 rounded-lg overflow-hidden">
            <table class="w-full border-collapse">
                <thead class="bg-gray-50">
                    <tr class="divide-x divide-gray-200">
                        <th class="py-2 text-center text-xs font-semibold text-gray-500">Sun</th>
                        <th class="py-2 text-center text-xs font-semibold text-gray-500">Mon</th>
                        <th class="py-2 text-center text-xs font-semibold text-gray-500">Tue</th>
                        <th class="py-2 text-center text-xs font-semibold text-gray-500">Wed</th>
                        <th class="py-2 text-center text-xs font-semibold text-gray-500">Thu</th>
                        <th class="py-2 text-center text-xs font-semibold text-gray-500">Fri</th>
                        <th class="py-2 text-center text-xs font-semibold text-gray-500">Sat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($calendarDays as $week)
                        <tr class="divide-x divide-gray-200">
                            @foreach($week as $cell)
                                @php
                                    $data = $cell['data'] ?? null;
                                    $bgClass = 'bg-white';
                                    $badgeClass = 'bg-gray-100 text-gray-600';
                                    $label = '—';

                                    if ($data) {
                                        if (!empty($data['is_holiday'])) {
                                            // Different colors for full vs half-day holidays
                                            if (!empty($data['holiday_full_day'])) {
                                                $bgClass = 'bg-blue-50';
                                                $badgeClass = 'bg-blue-100 text-blue-800';
                                                $label = 'Holiday';
                                            } else {
                                                $bgClass = 'bg-orange-50';
                                                $badgeClass = 'bg-orange-100 text-orange-800';
                                                $label = 'Half Day';
                                            }
                                        } else {
                                            if ($data['percentage'] >= 90) {
                                                $bgClass = 'bg-green-50';
                                                $badgeClass = 'bg-green-100 text-green-800';
                                            } elseif ($data['percentage'] >= 75) {
                                                $bgClass = 'bg-yellow-50';
                                                $badgeClass = 'bg-yellow-100 text-yellow-800';
                                            } else {
                                                $bgClass = 'bg-red-50';
                                                $badgeClass = 'bg-red-100 text-red-800';
                                            }
                                            $label = $data['percentage'] . '%';
                                        }
                                    }
                                @endphp
                                <td class="w-1/7 h-20 align-top border-t border-gray-200 text-center {{ $bgClass }}">
                                    @if($cell['day'])
                                        <div class="flex flex-col h-full p-1.5 items-center justify-start">
                                            <span class="text-[11px] font-semibold text-gray-800">
                                                {{ $cell['day'] }}
                                            </span>
                                            <span class="mt-1 inline-flex px-1.5 py-0.5 rounded-full text-[11px] {{ $badgeClass }}">
                                                {{ $label }}
                                            </span>
                                            @if($data)
                                                <div class="mt-1 text-[10px] text-gray-600">
                                                    @if(!empty($data['is_holiday']) && $data['holiday_title'])
                                                        {{ $data['holiday_title'] }}
                                                        @if(!empty($data['holiday_type']))
                                                            ·
                                                            @if($data['holiday_type'] === 'school')
                                                                Whole School
                                                            @elseif($data['holiday_type'] === 'students_only')
                                                                Students Only
                                                            @elseif($data['holiday_type'] === 'staff_only')
                                                                Staff Only
                                                            @else
                                                                {{ ucfirst(str_replace('_', ' ', $data['holiday_type'])) }}
                                                            @endif
                                                        @endif
                                                    @else
                                                        P: {{ $data['present'] }} / {{ $data['total'] }},
                                                        A: {{ $data['absent'] }}
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <p class="mt-2 text-[11px] text-gray-500">
            Note: Percentage is calculated as (Present + Late + Half-day) over total records for the day in this class/section.
        </p>
    </div>
</div>

@push('scripts')
<script>
    // Simple dependent section dropdown based on class
    (function () {
        const classSelect = document.getElementById('class_id');
        const sectionSelect = document.getElementById('section_id');
        if (!classSelect || !sectionSelect) return;

        const allOptions = Array.from(sectionSelect.options);

        function filterSections() {
            const classId = classSelect.value;
            sectionSelect.innerHTML = '';

            allOptions.forEach(option => {
                if (!option.value) {
                    sectionSelect.appendChild(option);
                    return;
                }
                const optionClassId = option.getAttribute('data-class-id');
                if (!classId || optionClassId === classId) {
                    sectionSelect.appendChild(option);
                }
            });
        }

        classSelect.addEventListener('change', filterSections);
        filterSections();
    })();
</script>
@endpush

@endsection


