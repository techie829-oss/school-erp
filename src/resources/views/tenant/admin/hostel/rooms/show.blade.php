@extends('tenant.layouts.admin')

@section('title', 'Room Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/hostel/rooms') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Rooms</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Room {{ $room->room_number }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $room->hostel->name }}</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/hostel/rooms/' . $room->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Edit
            </a>
            <a href="{{ url('/admin/hostel/rooms') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Room Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Hostel</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $room->hostel->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Room Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $room->room_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Room Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($room->room_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Floor</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $room->floor ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Capacity</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $room->capacity }} beds</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $room->status === 'available' ? 'bg-green-100 text-green-800' : ($room->status === 'occupied' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($room->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Allocated Students -->
            @if($room->activeAllocations->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Allocated Students ({{ $room->activeAllocations->count() }})</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bed Number</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Allocation Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($room->activeAllocations as $allocation)
                            <tr>
                                <td class="px-4 py-3 text-sm">
                                    <div class="font-medium text-gray-900">{{ $allocation->student->full_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $allocation->student->admission_number }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $allocation->bed_number ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $allocation->allocation_date->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Summary</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Capacity</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $room->capacity }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Occupied</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $room->active_allocations_count }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Available</dt>
                        <dd class="text-sm font-medium text-green-600">{{ $room->available_beds }}</dd>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <dt class="text-sm font-bold text-gray-900">Availability</dt>
                        <dd class="text-sm font-bold {{ $room->is_full ? 'text-red-600' : 'text-green-600' }}">
                            {{ $room->is_full ? 'Full' : 'Available' }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

