@extends('tenant.layouts.admin')

@section('title', 'Edit Room')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/hostel/rooms') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Rooms</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Edit</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Room</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $room->room_number }} - {{ $room->hostel->name }}</p>
        </div>
    </div>

    <form action="{{ url('/admin/hostel/rooms/' . $room->id) }}" method="POST" class="max-w-2xl">
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
                    <label for="hostel_id" class="block text-sm font-medium text-gray-700">Hostel <span class="text-red-500">*</span></label>
                    <select name="hostel_id" id="hostel_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Hostel</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}" {{ old('hostel_id', $room->hostel_id) == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="room_number" class="block text-sm font-medium text-gray-700">Room Number <span class="text-red-500">*</span></label>
                    <input type="text" name="room_number" id="room_number" value="{{ old('room_number', $room->room_number) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="room_type" class="block text-sm font-medium text-gray-700">Room Type <span class="text-red-500">*</span></label>
                    <select name="room_type" id="room_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="single" {{ old('room_type', $room->room_type) == 'single' ? 'selected' : '' }}>Single</option>
                        <option value="double" {{ old('room_type', $room->room_type) == 'double' ? 'selected' : '' }}>Double</option>
                        <option value="triple" {{ old('room_type', $room->room_type) == 'triple' ? 'selected' : '' }}>Triple</option>
                        <option value="dormitory" {{ old('room_type', $room->room_type) == 'dormitory' ? 'selected' : '' }}>Dormitory</option>
                    </select>
                </div>

                <div>
                    <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity <span class="text-red-500">*</span></label>
                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $room->capacity) }}" min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    <p class="mt-1 text-xs text-gray-500">Current: {{ $room->capacity }}, Available: {{ $room->available_beds }}</p>
                </div>

                <div>
                    <label for="floor" class="block text-sm font-medium text-gray-700">Floor</label>
                    <input type="text" name="floor" id="floor" value="{{ old('floor', $room->floor) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="available" {{ old('status', $room->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ old('status', $room->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="maintenance" {{ old('status', $room->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="reserved" {{ old('status', $room->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="facilities" class="block text-sm font-medium text-gray-700">Facilities</label>
                    <textarea name="facilities" id="facilities" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('facilities', $room->facilities) }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/hostel/rooms') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Update Room</button>
        </div>
    </form>
</div>
@endsection

