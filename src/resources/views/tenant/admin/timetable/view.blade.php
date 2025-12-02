@extends('tenant.layouts.admin')

@section('title', 'View Timetables')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Timetable</span></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">View</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">View Timetables</h2>
            <p class="mt-1 text-sm text-gray-500">View timetables by class, teacher, or room</p>
        </div>
    </div>

    <!-- View Type Selection -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex space-x-4 mb-6">
            <button onclick="showViewType('class')" class="px-4 py-2 rounded-md text-sm font-medium {{ $viewType == 'class' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Class-wise
            </button>
            <button onclick="showViewType('teacher')" class="px-4 py-2 rounded-md text-sm font-medium {{ $viewType == 'teacher' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Teacher-wise
            </button>
            <button onclick="showViewType('room')" class="px-4 py-2 rounded-md text-sm font-medium {{ $viewType == 'room' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Room-wise
            </button>
        </div>

        <!-- Class-wise View -->
        <div id="classView" style="display: {{ $viewType == 'class' ? 'block' : 'none' }};">
            <form method="GET" action="{{ url('/admin/timetable/view') }}" class="space-y-4">
                <input type="hidden" name="type" value="class">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                        <select name="class_id" id="class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="section_id" class="block text-sm font-medium text-gray-700">Section</label>
                        <select name="section_id" id="section_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">Select Section</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 w-full">View Timetable</button>
                    </div>
                </div>
            </form>

            @if($timetable)
            <div class="mt-6 overflow-x-auto">
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
                                    $periodData = $timetable->periodsByDay($day)->get()->firstWhere('period_number', $period);
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
            @endif
        </div>

        <!-- Teacher-wise View -->
        <div id="teacherView" style="display: {{ $viewType == 'teacher' ? 'block' : 'none' }};">
            <form method="GET" action="{{ url('/admin/timetable/view') }}" class="space-y-4">
                <input type="hidden" name="type" value="teacher">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="teacher_id" class="block text-sm font-medium text-gray-700">Teacher</label>
                        <select name="teacher_id" id="teacher_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 w-full">View Timetable</button>
                    </div>
                </div>
            </form>

            @if($teacherTimetable)
            <div class="mt-6 overflow-x-auto">
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
                            $maxPeriods = 8; // Adjust based on your needs
                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        @endphp
                        @for($period = 1; $period <= $maxPeriods; $period++)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">Period {{ $period }}</td>
                            @foreach($days as $day)
                            <td class="px-4 py-3 text-sm text-center">
                                @php
                                    $periodData = collect($teacherTimetable[$day] ?? [])->firstWhere('period_number', $period);
                                @endphp
                                @if($periodData)
                                <div class="space-y-1">
                                    <div class="font-medium text-gray-900">{{ $periodData['subject']->subject_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $periodData['class']->class_name }}</div>
                                    @if($periodData['section'])
                                    <div class="text-xs text-gray-500">{{ $periodData['section']->section_name }}</div>
                                    @endif
                                    @if($periodData['room'])
                                    <div class="text-xs text-gray-500">Room: {{ $periodData['room'] }}</div>
                                    @endif
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
            @endif
        </div>

        <!-- Room-wise View -->
        <div id="roomView" style="display: {{ $viewType == 'room' ? 'block' : 'none' }};">
            <form method="GET" action="{{ url('/admin/timetable/view') }}" class="space-y-4">
                <input type="hidden" name="type" value="room">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="room" class="block text-sm font-medium text-gray-700">Room</label>
                        <input type="text" name="room" id="room" value="{{ request('room') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Enter room number/name">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 w-full">View Timetable</button>
                    </div>
                </div>
            </form>

            @if($roomTimetable)
            <div class="mt-6 overflow-x-auto">
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
                            $maxPeriods = 8;
                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        @endphp
                        @for($period = 1; $period <= $maxPeriods; $period++)
                        <tr>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900">Period {{ $period }}</td>
                            @foreach($days as $day)
                            <td class="px-4 py-3 text-sm text-center">
                                @php
                                    $periodData = collect($roomTimetable[$day] ?? [])->firstWhere('period_number', $period);
                                @endphp
                                @if($periodData)
                                <div class="space-y-1">
                                    <div class="font-medium text-gray-900">{{ $periodData['subject']->subject_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $periodData['class']->class_name }}</div>
                                    @if($periodData['section'])
                                    <div class="text-xs text-gray-500">{{ $periodData['section']->section_name }}</div>
                                    @endif
                                    @if($periodData['teacher'])
                                    <div class="text-xs text-gray-500">{{ $periodData['teacher']->full_name }}</div>
                                    @endif
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
            @endif
        </div>
    </div>
</div>

<script>
function showViewType(type) {
    document.getElementById('classView').style.display = type === 'class' ? 'block' : 'none';
    document.getElementById('teacherView').style.display = type === 'teacher' ? 'block' : 'none';
    document.getElementById('roomView').style.display = type === 'room' ? 'block' : 'none';
    
    // Update URL
    const url = new URL(window.location.href);
    url.searchParams.set('type', type);
    window.history.pushState({}, '', url);
}

// Load sections when class is selected
document.getElementById('class_id')?.addEventListener('change', function() {
    const classId = this.value;
    const sectionSelect = document.getElementById('section_id');

    if (!classId) {
        sectionSelect.innerHTML = '<option value="">Select Section</option>';
        return;
    }

    fetch(`/admin/classes/${classId}/sections`)
        .then(response => response.json())
        .then(data => {
            sectionSelect.innerHTML = '<option value="">Select Section</option>';
            data.forEach(section => {
                const option = document.createElement('option');
                option.value = section.id;
                option.textContent = section.section_name;
                sectionSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading sections:', error);
        });
});
</script>
@endsection

