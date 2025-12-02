@extends('tenant.layouts.admin')

@section('title', 'Allocate Student')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/hostel/allocations') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Allocations</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Allocate</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Allocate Student to Hostel</h2>
        </div>
    </div>

    <form action="{{ url('/admin/hostel/allocations') }}" method="POST" class="max-w-2xl">
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
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student <span class="text-red-500">*</span></label>
                    <select name="student_id" id="student_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->full_name }} ({{ $student->admission_number }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="hostel_id" class="block text-sm font-medium text-gray-700">Hostel <span class="text-red-500">*</span></label>
                    <select name="hostel_id" id="hostel_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Hostel</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}" {{ old('hostel_id', request('hostel_id')) == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }} ({{ $hostel->available_beds }} beds available)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="room_id" class="block text-sm font-medium text-gray-700">Room <span class="text-red-500">*</span></label>
                    <select name="room_id" id="room_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Hostel First</option>
                    </select>
                </div>

                <div>
                    <label for="bed_number" class="block text-sm font-medium text-gray-700">Bed Number</label>
                    <input type="number" name="bed_number" id="bed_number" value="{{ old('bed_number') }}" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Optional - leave blank for auto-assignment</p>
                </div>

                <div>
                    <label for="allocation_date" class="block text-sm font-medium text-gray-700">Allocation Date <span class="text-red-500">*</span></label>
                    <input type="date" name="allocation_date" id="allocation_date" value="{{ old('allocation_date', now()->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/hostel/allocations') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Allocate Student</button>
        </div>
    </form>
</div>

<script>
document.getElementById('hostel_id').addEventListener('change', function() {
    const hostelId = this.value;
    const roomSelect = document.getElementById('room_id');

    if (!hostelId) {
        roomSelect.innerHTML = '<option value="">Select Hostel First</option>';
        return;
    }

    fetch(`/admin/hostel/hostels/${hostelId}/rooms`)
        .then(response => response.json())
        .then(data => {
            roomSelect.innerHTML = '<option value="">Select Room</option>';
            data.forEach(room => {
                if (room.available_beds > 0) {
                    const option = document.createElement('option');
                    option.value = room.id;
                    option.textContent = `${room.room_number} (${room.room_type}, ${room.available_beds} available)`;
                    roomSelect.appendChild(option);
                }
            });
        })
        .catch(error => {
            console.error('Error loading rooms:', error);
        });
});
</script>
@endsection

