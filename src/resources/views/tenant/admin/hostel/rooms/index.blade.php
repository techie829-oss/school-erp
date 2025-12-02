@extends('tenant.layouts.admin')

@section('title', 'Hostel Rooms')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Hostel</span></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Rooms</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Hostel Rooms</h2>
            <p class="mt-1 text-sm text-gray-500">Manage hostel rooms</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/hostel/rooms/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Room
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-md bg-red-50 p-4">
        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="hostel_id" class="block text-sm font-medium text-gray-700">Hostel</label>
                    <select name="hostel_id" id="hostel_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Hostels</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}" {{ request('hostel_id') == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
                <div>
                    <label for="room_type" class="block text-sm font-medium text-gray-700">Room Type</label>
                    <select name="room_type" id="room_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Types</option>
                        <option value="single" {{ request('room_type') == 'single' ? 'selected' : '' }}>Single</option>
                        <option value="double" {{ request('room_type') == 'double' ? 'selected' : '' }}>Double</option>
                        <option value="triple" {{ request('room_type') == 'triple' ? 'selected' : '' }}>Triple</option>
                        <option value="dormitory" {{ request('room_type') == 'dormitory' ? 'selected' : '' }}>Dormitory</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <a href="{{ url('/admin/hostel/rooms') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Clear</a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Apply Filters</button>
            </div>
        </form>
    </div>

    <!-- Rooms Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hostel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Capacity</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Occupied</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Available</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($rooms as $room)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $room->hostel->name }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $room->room_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ ucfirst($room->room_type) }}</td>
                        <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $room->capacity }}</td>
                        <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $room->active_allocations_count }}</td>
                        <td class="px-6 py-4 text-sm text-center text-green-600 font-medium">{{ $room->available_beds }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $room->status === 'available' ? 'bg-green-100 text-green-800' : ($room->status === 'occupied' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($room->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium space-x-3">
                            <a href="{{ url('/admin/hostel/rooms/' . $room->id) }}" class="text-primary-600 hover:text-primary-900">View</a>
                            <a href="{{ url('/admin/hostel/rooms/' . $room->id . '/edit') }}" class="text-primary-600 hover:text-primary-900">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <p class="text-sm text-gray-500">No rooms found. <a href="{{ url('/admin/hostel/rooms/create') }}" class="text-primary-600 hover:text-primary-900">Create one</a></p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rooms->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $rooms->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

