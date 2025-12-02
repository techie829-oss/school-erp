@extends('tenant.layouts.admin')

@section('title', 'Vehicle Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/transport/vehicles') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Vehicles</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Vehicle Details</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $vehicle->vehicle_number }}</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/transport/vehicles/' . $vehicle->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Edit
            </a>
            <a href="{{ url('/admin/transport/vehicles') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Vehicle Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicle Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Vehicle Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->vehicle_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Registration Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->registration_number ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Vehicle Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($vehicle->vehicle_type) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Capacity</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->capacity }} seats</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Make</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->make ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Model</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->model ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Color</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->color ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Manufacturing Year</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->manufacturing_year ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $vehicle->status === 'active' ? 'bg-green-100 text-green-800' : ($vehicle->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($vehicle->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Driver & Route -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Assignment</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Driver</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($vehicle->driver)
                                <a href="{{ url('/admin/transport/drivers/' . $vehicle->driver->id) }}" class="text-primary-600 hover:text-primary-900">
                                    {{ $vehicle->driver->name }}
                                </a>
                            @else
                                <span class="text-gray-500">Not assigned</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Route</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($vehicle->route)
                                <a href="{{ url('/admin/transport/routes/' . $vehicle->route->id) }}" class="text-primary-600 hover:text-primary-900">
                                    {{ $vehicle->route->name }}
                                </a>
                            @else
                                <span class="text-gray-500">Not assigned</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Documents & Expiry -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Documents & Expiry Dates</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Registration Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $vehicle->registration_date?->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Insurance Expiry</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($vehicle->insurance_expiry)
                                {{ $vehicle->insurance_expiry->format('d M Y') }}
                                @if($vehicle->is_insurance_expired)
                                    <span class="ml-2 text-red-600 text-xs">(Expired)</span>
                                @elseif($vehicle->insurance_expiry < now()->addDays(30))
                                    <span class="ml-2 text-yellow-600 text-xs">(Expiring Soon)</span>
                                @endif
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Permit Expiry</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($vehicle->permit_expiry)
                                {{ $vehicle->permit_expiry->format('d M Y') }}
                                @if($vehicle->is_permit_expired)
                                    <span class="ml-2 text-red-600 text-xs">(Expired)</span>
                                @elseif($vehicle->permit_expiry < now()->addDays(30))
                                    <span class="ml-2 text-yellow-600 text-xs">(Expiring Soon)</span>
                                @endif
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fitness Expiry</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($vehicle->fitness_expiry)
                                {{ $vehicle->fitness_expiry->format('d M Y') }}
                                @if($vehicle->is_fitness_expired)
                                    <span class="ml-2 text-red-600 text-xs">(Expired)</span>
                                @elseif($vehicle->fitness_expiry < now()->addDays(30))
                                    <span class="ml-2 text-yellow-600 text-xs">(Expiring Soon)</span>
                                @endif
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            @if($vehicle->notes)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                <p class="text-sm text-gray-900">{{ $vehicle->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Capacity</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Total Capacity</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $vehicle->capacity }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Occupied</dt>
                        <dd class="mt-1 text-2xl font-semibold text-gray-900">{{ $vehicle->active_assignments_count }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Available</dt>
                        <dd class="mt-1 text-2xl font-semibold text-primary-600">{{ $vehicle->available_seats }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Active Assignments -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Active Assignments</h3>
                @if($vehicle->activeAssignments->count() > 0)
                    <ul class="space-y-2">
                        @foreach($vehicle->activeAssignments->take(5) as $assignment)
                        <li class="text-sm">
                            <a href="{{ url('/admin/transport/assignments/' . $assignment->id) }}" class="text-primary-600 hover:text-primary-900">
                                {{ $assignment->student->full_name }}
                            </a>
                            <span class="text-gray-500">- {{ $assignment->route->name }}</span>
                        </li>
                        @endforeach
                        @if($vehicle->activeAssignments->count() > 5)
                            <li class="text-sm text-gray-500">+ {{ $vehicle->activeAssignments->count() - 5 }} more</li>
                        @endif
                    </ul>
                @else
                    <p class="text-sm text-gray-500">No active assignments</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

