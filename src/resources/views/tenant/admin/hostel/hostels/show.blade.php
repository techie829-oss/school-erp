@extends('tenant.layouts.admin')

@section('title', 'Hostel Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/hostel/hostels') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Hostels</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $hostel->name }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $hostel->address ?? 'No address provided' }}</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/hostel/hostels/' . $hostel->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Edit
            </a>
            <a href="{{ url('/admin/hostel/hostels') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Hostel Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Hostel Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $hostel->name }}</dd>
                    </div>
                    @if($hostel->address)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $hostel->address }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Gender</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($hostel->gender) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $hostel->status === 'active' ? 'bg-green-100 text-green-800' : ($hostel->status === 'inactive' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($hostel->status) }}
                            </span>
                        </dd>
                    </div>
                    @if($hostel->warden)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Warden</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $hostel->warden->full_name }}</dd>
                    </div>
                    @endif
                    @if($hostel->contact_number)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Contact Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $hostel->contact_number }}</dd>
                    </div>
                    @endif
                    @if($hostel->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $hostel->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Rooms -->
            @if($hostel->rooms->count() > 0)
            <div class="bg-white shadow rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Rooms ({{ $hostel->rooms->count() }})</h3>
                    <a href="{{ url('/admin/hostel/rooms?hostel_id=' . $hostel->id) }}" class="text-sm text-primary-600 hover:text-primary-900">View All →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room Number</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Capacity</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Available</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($hostel->rooms->take(10) as $room)
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $room->room_number }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ ucfirst($room->room_type) }}</td>
                                <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $room->capacity }}</td>
                                <td class="px-4 py-3 text-sm text-center text-green-600 font-medium">{{ $room->available_beds }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $room->status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($room->status) }}
                                    </span>
                                </td>
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
            <!-- Summary -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Summary</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Total Capacity</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $hostel->capacity }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Occupied</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $hostel->active_allocations_count }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Available Beds</dt>
                        <dd class="text-sm font-medium text-green-600">{{ $hostel->available_beds }}</dd>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <dt class="text-sm font-bold text-gray-900">Occupancy Rate</dt>
                        <dd class="text-sm font-bold text-gray-900">{{ $hostel->occupancy_rate }}%</dd>
                    </div>
                </dl>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ url('/admin/hostel/rooms/create?hostel_id=' . $hostel->id) }}" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Add Room
                    </a>
                    <a href="{{ url('/admin/hostel/allocations/create?hostel_id=' . $hostel->id) }}" class="block w-full text-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                        Allocate Student
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

