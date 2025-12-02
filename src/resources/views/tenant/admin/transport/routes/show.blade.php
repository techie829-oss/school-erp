@extends('tenant.layouts.admin')

@section('title', 'Route Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/transport/routes') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Routes</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Route Details</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $route->name }}</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/transport/routes/' . $route->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Edit
            </a>
            <a href="{{ url('/admin/transport/routes') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Route Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Route Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Route Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $route->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Route Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $route->route_number ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Distance</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $route->distance ? $route->distance . ' km' : '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Base Fare</dt>
                        <dd class="mt-1 text-sm text-gray-900">₹{{ number_format($route->base_fare, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $route->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($route->status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Stops</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $route->stops->count() }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Start Location</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $route->start_location }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">End Location</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $route->end_location }}</dd>
                    </div>
                    @if($route->description)
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Description</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $route->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Route Stops -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Route Stops</h3>
                @if($route->stops->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stop Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Fare from Start</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($route->stops->sortBy('stop_order') as $stop)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $stop->stop_order }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $stop->stop_name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $stop->stop_address ?? '-' }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900">₹{{ number_format($stop->fare_from_start, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No stops added to this route</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Stops</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $route->stops->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Active Assignments</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $route->activeAssignments->count() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Assigned Vehicles</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $route->vehicles->count() }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Assigned Vehicles -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Assigned Vehicles</h3>
                @if($route->vehicles->count() > 0)
                    <ul class="space-y-2">
                        @foreach($route->vehicles as $vehicle)
                        <li class="text-sm">
                            <a href="{{ url('/admin/transport/vehicles/' . $vehicle->id) }}" class="text-primary-600 hover:text-primary-900">
                                {{ $vehicle->vehicle_number }}
                            </a>
                            <span class="text-gray-500">({{ ucfirst($vehicle->vehicle_type) }})</span>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">No vehicles assigned</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

