@extends('tenant.layouts.admin')

@section('title', 'Book Transport')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/transport/assignments') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Assignments</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Book Transport</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Book Transport</h2>
        </div>
    </div>

    <form action="{{ url('/admin/transport/assignments') }}" method="POST" class="max-w-3xl">
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
                    <label for="student_id" class="block text-sm font-medium text-gray-700">Student <span class="text-red-500">*</span></label>
                    <select name="student_id" id="student_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }} ({{ $student->admission_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="route_id" class="block text-sm font-medium text-gray-700">Route <span class="text-red-500">*</span></label>
                    <select name="route_id" id="route_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Route</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}" data-stops="{{ json_encode($route->stops) }}" {{ old('route_id') == $route->id ? 'selected' : '' }}>
                                {{ $route->name }} - {{ Str::limit($route->start_location, 30) }} to {{ Str::limit($route->end_location, 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="pickup_stop_id" class="block text-sm font-medium text-gray-700">Pickup Stop</label>
                    <select name="pickup_stop_id" id="pickup_stop_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Pickup Stop</option>
                    </select>
                </div>

                <div>
                    <label for="drop_stop_id" class="block text-sm font-medium text-gray-700">Drop Stop</label>
                    <select name="drop_stop_id" id="drop_stop_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Drop Stop</option>
                    </select>
                </div>

                <div>
                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Vehicle</label>
                    <select name="vehicle_id" id="vehicle_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Vehicle (Optional)</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->vehicle_number }} ({{ $vehicle->available_seats }} seats available)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="monthly_fare" class="block text-sm font-medium text-gray-700">Monthly Fare (₹) <span class="text-red-500">*</span></label>
                    <input type="number" name="monthly_fare" id="monthly_fare" value="{{ old('monthly_fare', 0) }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="booking_date" class="block text-sm font-medium text-gray-700">Booking Date</label>
                    <input type="date" name="booking_date" id="booking_date" value="{{ old('booking_date', date('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="booking_status" class="block text-sm font-medium text-gray-700">Booking Status <span class="text-red-500">*</span></label>
                    <select name="booking_status" id="booking_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="pending" {{ old('booking_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ old('booking_status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="active" {{ old('booking_status') == 'active' ? 'selected' : '' }}>Active</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/transport/assignments') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Book Transport</button>
        </div>
    </form>
</div>

<script>
document.getElementById('route_id').addEventListener('change', function() {
    const routeSelect = this;
    const selectedOption = routeSelect.options[routeSelect.selectedIndex];
    const stops = JSON.parse(selectedOption.getAttribute('data-stops') || '[]');

    const pickupSelect = document.getElementById('pickup_stop_id');
    const dropSelect = document.getElementById('drop_stop_id');

    // Clear existing options
    pickupSelect.innerHTML = '<option value="">Select Pickup Stop</option>';
    dropSelect.innerHTML = '<option value="">Select Drop Stop</option>';

    // Add stops
    stops.forEach(stop => {
        const option1 = new Option(stop.stop_name + ' (Order: ' + stop.stop_order + ')', stop.id);
        const option2 = new Option(stop.stop_name + ' (Order: ' + stop.stop_order + ')', stop.id);
        pickupSelect.add(option1);
        dropSelect.add(option2);
    });
});
</script>
@endsection

