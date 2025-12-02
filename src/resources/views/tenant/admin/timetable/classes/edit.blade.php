@extends('tenant.layouts.admin')

@section('title', 'Edit Timetable')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/timetable/classes') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Timetables</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Edit</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Timetable</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $timetable->schoolClass->class_name }} - {{ $timetable->section ? $timetable->section->section_name : 'All Sections' }}</p>
        </div>
    </div>

    <form action="{{ url('/admin/timetable/classes/' . $timetable->id) }}" method="POST" id="timetableForm">
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
                    <label for="class_id" class="block text-sm font-medium text-gray-700">Class <span class="text-red-500">*</span></label>
                    <select name="class_id" id="class_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $timetable->class_id) == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="section_id" class="block text-sm font-medium text-gray-700">Section</label>
                    <select name="section_id" id="section_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Sections</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ old('section_id', $timetable->section_id) == $section->id ? 'selected' : '' }}>{{ $section->section_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700">Academic Year <span class="text-red-500">*</span></label>
                    <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year', $timetable->academic_year) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="term" class="block text-sm font-medium text-gray-700">Term</label>
                    <input type="text" name="term" id="term" value="{{ old('term', $timetable->term) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="draft" {{ old('status', $timetable->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $timetable->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $timetable->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('notes', $timetable->notes) }}</textarea>
            </div>
        </div>

        <!-- Periods Section -->
        <div class="bg-white shadow rounded-lg p-6 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Timetable Periods</h3>
                <button type="button" onclick="addPeriodRow()" class="px-3 py-1 text-sm border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    + Add Period
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Start Time</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">End Time</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody id="periodsTableBody" class="bg-white divide-y divide-gray-200">
                        @foreach($timetable->periods as $index => $period)
                        <tr id="period-row-{{ $index }}">
                            <td class="px-3 py-2">
                                <select name="periods[{{ $index }}][day]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                        <option value="{{ $day }}" {{ $period->day == $day ? 'selected' : '' }}>{{ ucfirst($day) }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <select name="periods[{{ $index }}][period_number]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    @foreach($periods as $p)
                                        <option value="{{ $p->period_number }}" {{ $period->period_number == $p->period_number ? 'selected' : '' }}>Period {{ $p->period_number }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <input type="time" name="periods[{{ $index }}][start_time]" value="{{ \Carbon\Carbon::parse($period->start_time)->format('H:i') }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </td>
                            <td class="px-3 py-2">
                                <input type="time" name="periods[{{ $index }}][end_time]" value="{{ \Carbon\Carbon::parse($period->end_time)->format('H:i') }}" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </td>
                            <td class="px-3 py-2">
                                <select name="periods[{{ $index }}][subject_id]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ $period->subject_id == $subject->id ? 'selected' : '' }}>{{ $subject->subject_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <select name="periods[{{ $index }}][teacher_id]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                                    <option value="">Select Teacher</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ $period->teacher_id == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-3 py-2">
                                <input type="text" name="periods[{{ $index }}][room]" value="{{ $period->room }}" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Room">
                            </td>
                            <td class="px-3 py-2 text-right">
                                <button type="button" onclick="removePeriodRow({{ $index }})" class="text-red-600 hover:text-red-900 text-sm">Remove</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/timetable/classes') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Update Timetable</button>
        </div>
    </form>
</div>

<script>
let periodRowCount = {{ $timetable->periods->count() }};
const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
const subjects = @json($subjects);
const teachers = @json($teachers);
const periods = @json($periods);

document.getElementById('class_id').addEventListener('change', function() {
    const classId = this.value;
    const sectionSelect = document.getElementById('section_id');

    if (!classId) {
        sectionSelect.innerHTML = '<option value="">All Sections</option>';
        return;
    }

    fetch(`/admin/classes/${classId}/sections`)
        .then(response => response.json())
        .then(data => {
            sectionSelect.innerHTML = '<option value="">All Sections</option>';
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

function addPeriodRow() {
    const tbody = document.getElementById('periodsTableBody');
    const row = document.createElement('tr');
    row.id = `period-row-${periodRowCount}`;
    
    row.innerHTML = `
        <td class="px-3 py-2">
            <select name="periods[${periodRowCount}][day]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <option value="">Select Day</option>
                ${days.map(day => `<option value="${day}">${day.charAt(0).toUpperCase() + day.slice(1)}</option>`).join('')}
            </select>
        </td>
        <td class="px-3 py-2">
            <select name="periods[${periodRowCount}][period_number]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <option value="">Select Period</option>
                ${periods.map(p => `<option value="${p.period_number}">Period ${p.period_number}</option>`).join('')}
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="time" name="periods[${periodRowCount}][start_time]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
        </td>
        <td class="px-3 py-2">
            <input type="time" name="periods[${periodRowCount}][end_time]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
        </td>
        <td class="px-3 py-2">
            <select name="periods[${periodRowCount}][subject_id]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <option value="">Select Subject</option>
                ${subjects.map(s => `<option value="${s.id}">${s.subject_name}</option>`).join('')}
            </select>
        </td>
        <td class="px-3 py-2">
            <select name="periods[${periodRowCount}][teacher_id]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                <option value="">Select Teacher</option>
                ${teachers.map(t => `<option value="${t.id}">${t.full_name}</option>`).join('')}
            </select>
        </td>
        <td class="px-3 py-2">
            <input type="text" name="periods[${periodRowCount}][room]" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Room">
        </td>
        <td class="px-3 py-2 text-right">
            <button type="button" onclick="removePeriodRow(${periodRowCount})" class="text-red-600 hover:text-red-900 text-sm">Remove</button>
        </td>
    `;
    
    tbody.appendChild(row);
    periodRowCount++;
}

function removePeriodRow(rowId) {
    const row = document.getElementById(`period-row-${rowId}`);
    if (row) {
        row.remove();
    }
}
</script>
@endsection

