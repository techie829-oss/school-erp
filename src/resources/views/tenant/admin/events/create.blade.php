@extends('tenant.layouts.admin')

@section('title', 'Create Event')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/events') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Events</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Create</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Create Event</h2>
            <p class="mt-1 text-sm text-gray-500">Add a new event to the calendar</p>
        </div>
    </div>

    <form action="{{ url('/admin/events') }}" method="POST" class="max-w-4xl">
        @csrf

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
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="event_type" class="block text-sm font-medium text-gray-700">Event Type <span class="text-red-500">*</span></label>
                    <select name="event_type" id="event_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="general" {{ old('event_type') == 'general' ? 'selected' : '' }}>General</option>
                        <option value="academic" {{ old('event_type') == 'academic' ? 'selected' : '' }}>Academic</option>
                        <option value="sports" {{ old('event_type') == 'sports' ? 'selected' : '' }}>Sports</option>
                        <option value="cultural" {{ old('event_type') == 'cultural' ? 'selected' : '' }}>Cultural</option>
                        <option value="meeting" {{ old('event_type') == 'meeting' ? 'selected' : '' }}>Meeting</option>
                        <option value="holiday" {{ old('event_type') == 'holiday' ? 'selected' : '' }}>Holiday</option>
                    </select>
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" id="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">No Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Leave empty for single-day events</p>
                </div>

                <div>
                    <label class="flex items-center mt-6">
                        <input type="checkbox" name="is_all_day" id="is_all_day" value="1" {{ old('is_all_day') ? 'checked' : '' }} class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">All Day Event</span>
                    </label>
                </div>

                <div id="time-fields" class="{{ old('is_all_day') ? 'hidden' : '' }}">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Start Time</label>
                            <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700">End Time</label>
                            <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        </div>
                    </div>
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}" placeholder="Event location..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="5" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('description') }}</textarea>
            </div>

            <!-- Participants Section -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Participants</label>
                <div id="participants-container" class="space-y-2">
                    <div class="participant-row flex gap-2">
                        <select name="participants[0][type]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            <option value="all">All</option>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="class">Class</option>
                            <option value="section">Section</option>
                            <option value="department">Department</option>
                        </select>
                        <input type="hidden" name="participants[0][id]" value="">
                        <button type="button" onclick="removeParticipant(this)" class="px-3 py-2 text-sm text-red-600 hover:text-red-900">Remove</button>
                    </div>
                </div>
                <button type="button" onclick="addParticipant()" class="mt-2 px-3 py-1 text-sm text-primary-600 hover:text-primary-900">+ Add Participant</button>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/events') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Create Event</button>
        </div>
    </form>
</div>

<script>
document.getElementById('is_all_day').addEventListener('change', function() {
    document.getElementById('time-fields').classList.toggle('hidden', this.checked);
});

let participantCount = 1;
function addParticipant() {
    const container = document.getElementById('participants-container');
    const row = document.createElement('div');
    row.className = 'participant-row flex gap-2';
    row.innerHTML = `
        <select name="participants[${participantCount}][type]" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
            <option value="all">All</option>
            <option value="student">Student</option>
            <option value="teacher">Teacher</option>
            <option value="class">Class</option>
            <option value="section">Section</option>
            <option value="department">Department</option>
        </select>
        <input type="hidden" name="participants[${participantCount}][id]" value="">
        <button type="button" onclick="removeParticipant(this)" class="px-3 py-2 text-sm text-red-600 hover:text-red-900">Remove</button>
    `;
    container.appendChild(row);
    participantCount++;
}

function removeParticipant(button) {
    button.closest('.participant-row').remove();
}
</script>
@endsection

