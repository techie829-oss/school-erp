@extends('tenant.layouts.admin')

@section('title', 'Driver Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/transport/drivers') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Drivers</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Driver Details</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $driver->name }}</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/transport/drivers/' . $driver->id . '/edit') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Edit
            </a>
            <a href="{{ url('/admin/transport/drivers') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Personal Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $driver->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $driver->phone ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $driver->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Gender</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($driver->gender ?? '-') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $driver->date_of_birth?->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $driver->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($driver->status) }}
                            </span>
                        </dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $driver->address ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- License Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">License Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">License Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $driver->license_number ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">License Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $driver->license_type ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Issue Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $driver->license_issue_date?->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Expiry Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($driver->license_expiry_date)
                                {{ $driver->license_expiry_date->format('d M Y') }}
                                @if($driver->license_expiry_date < now())
                                    <span class="ml-2 text-red-600 text-xs">(Expired)</span>
                                @elseif($driver->license_expiry_date < now()->addDays(30))
                                    <span class="ml-2 text-yellow-600 text-xs">(Expiring Soon)</span>
                                @endif
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- Employment Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Employment Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Joining Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $driver->joining_date?->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Salary</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $driver->salary ? '₹' . number_format($driver->salary, 2) : '-' }}</dd>
                    </div>
                </dl>
            </div>

            @if($driver->notes)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                <p class="text-sm text-gray-900">{{ $driver->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Emergency Contact -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Emergency Contact</h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Contact Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $driver->emergency_contact_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Contact Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $driver->emergency_contact_phone ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Assigned Vehicles -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Assigned Vehicles</h3>
                @if($driver->vehicles->count() > 0)
                    <ul class="space-y-2">
                        @foreach($driver->vehicles as $vehicle)
                        <li class="text-sm">
                            <a href="{{ url('/admin/transport/vehicles/' . $vehicle->id) }}" class="text-primary-600 hover:text-primary-900">
                                {{ $vehicle->vehicle_number }}
                            </a>
                            @if($vehicle->route)
                                <span class="text-gray-500">- {{ $vehicle->route->name }}</span>
                            @endif
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

