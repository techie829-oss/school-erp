@extends('tenant.layouts.admin')

@section('title', 'Edit Route')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/transport/routes') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Routes</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Edit</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Route</h2>
        </div>
    </div>

    <form action="{{ url('/admin/transport/routes/' . $route->id) }}" method="POST" class="max-w-3xl">
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
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Route Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $route->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="route_number" class="block text-sm font-medium text-gray-700">Route Number</label>
                    <input type="text" name="route_number" id="route_number" value="{{ old('route_number', $route->route_number) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="active" {{ old('status', $route->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $route->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="start_location" class="block text-sm font-medium text-gray-700">Start Location <span class="text-red-500">*</span></label>
                    <textarea name="start_location" id="start_location" rows="2" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('start_location', $route->start_location) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label for="end_location" class="block text-sm font-medium text-gray-700">End Location <span class="text-red-500">*</span></label>
                    <textarea name="end_location" id="end_location" rows="2" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('end_location', $route->end_location) }}</textarea>
                </div>

                <div>
                    <label for="distance" class="block text-sm font-medium text-gray-700">Distance (km)</label>
                    <input type="number" name="distance" id="distance" value="{{ old('distance', $route->distance) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="base_fare" class="block text-sm font-medium text-gray-700">Base Fare (₹)</label>
                    <input type="number" name="base_fare" id="base_fare" value="{{ old('base_fare', $route->base_fare) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('description', $route->description) }}</textarea>
                </div>
            </div>

            <!-- Route Stops -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Route Stops</h3>
                    <button type="button" onclick="addStop()" class="px-3 py-1 text-sm font-medium text-primary-600 bg-primary-50 rounded-md hover:bg-primary-100">
                        + Add Stop
                    </button>
                </div>
                <div id="stops-container" class="space-y-4">
                    @foreach($route->stops as $index => $stop)
                    <div class="stop-item bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div class="flex justify-between items-start mb-3">
                            <h4 class="text-sm font-medium text-gray-700">Stop {{ $index + 1 }}</h4>
                            <button type="button" onclick="removeStop(this)" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Stop Name <span class="text-red-500">*</span></label>
                                <input type="text" name="stops[{{ $index }}][stop_name]" value="{{ $stop->stop_name }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Stop Order <span class="text-red-500">*</span></label>
                                <input type="number" name="stops[{{ $index }}][stop_order]" value="{{ $stop->stop_order }}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Fare from Start (₹)</label>
                                <input type="number" name="stops[{{ $index }}][fare_from_start]" step="0.01" min="0" value="{{ $stop->fare_from_start }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700">Stop Address</label>
                                <input type="text" name="stops[{{ $index }}][stop_address]" value="{{ $stop->stop_address }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/transport/routes') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Update Route</button>
        </div>
    </form>
</div>

<script>
let stopCount = {{ $route->stops->count() }};

function addStop() {
    stopCount++;
    const container = document.getElementById('stops-container');
    const stopHtml = `
        <div class="stop-item bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div class="flex justify-between items-start mb-3">
                <h4 class="text-sm font-medium text-gray-700">Stop ${stopCount}</h4>
                <button type="button" onclick="removeStop(this)" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700">Stop Name <span class="text-red-500">*</span></label>
                    <input type="text" name="stops[${stopCount}][stop_name]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Stop Order <span class="text-red-500">*</span></label>
                    <input type="number" name="stops[${stopCount}][stop_order]" value="${stopCount}" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Fare from Start (₹)</label>
                    <input type="number" name="stops[${stopCount}][fare_from_start]" step="0.01" min="0" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Stop Address</label>
                    <input type="text" name="stops[${stopCount}][stop_address]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', stopHtml);
}

function removeStop(button) {
    button.closest('.stop-item').remove();
}
</script>
@endsection

