@extends('tenant.layouts.admin')

@section('title', 'Timetable Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/timetable/classes') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Timetables</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Timetable Details</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $timetable->schoolClass->class_name }} - {{ $timetable->section ? $timetable->section->section_name : 'All Sections' }}</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/timetable/classes/' . $timetable->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Edit
            </a>
            <a href="{{ url('/admin/timetable/classes') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <!-- Timetable Info -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Timetable Information</h3>
        <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">Class</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $timetable->schoolClass->class_name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Section</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $timetable->section ? $timetable->section->section_name : 'All Sections' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $timetable->academic_year }}</dd>
            </div>
            @if($timetable->term)
            <div>
                <dt class="text-sm font-medium text-gray-500">Term</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $timetable->term }}</dd>
            </div>
            @endif
            <div>
                <dt class="text-sm font-medium text-gray-500">Status</dt>
                <dd class="mt-1">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $timetable->status === 'active' ? 'bg-green-100 text-green-800' : ($timetable->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($timetable->status) }}
                    </span>
                </dd>
            </div>
        </dl>
    </div>

    <!-- Timetable Grid -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Weekly Timetable</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $maxPeriods = $timetable->periods->max('period_number') ?? 0;
                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                    @endphp
                    @for($period = 1; $period <= $maxPeriods; $period++)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900">Period {{ $period }}</td>
                        @foreach($days as $day)
                        <td class="px-4 py-3 text-sm text-center">
                            @php
                                $periodData = $periodsByDay[$day]->firstWhere('period_number', $period);
                            @endphp
                            @if($periodData)
                            <div class="space-y-1">
                                <div class="font-medium text-gray-900">{{ $periodData->subject->subject_name }}</div>
                                @if($periodData->teacher)
                                <div class="text-xs text-gray-500">{{ $periodData->teacher->full_name }}</div>
                                @endif
                                @if($periodData->room)
                                <div class="text-xs text-gray-500">Room: {{ $periodData->room }}</div>
                                @endif
                                <div class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($periodData->start_time)->format('h:i A') }} - 
                                    {{ \Carbon\Carbon::parse($periodData->end_time)->format('h:i A') }}
                                </div>
                            </div>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

