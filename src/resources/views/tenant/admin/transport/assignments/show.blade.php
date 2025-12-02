@extends('tenant.layouts.admin')

@section('title', 'Assignment Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/transport/assignments') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Assignments</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Transport Assignment Details</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $assignment->student->full_name }}</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/transport/assignments') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Student Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Student Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assignment->student->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Admission Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assignment->student->admission_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Class</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assignment->student->class->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Section</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assignment->student->section->name ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Transport Details -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Transport Details</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Route</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ url('/admin/transport/routes/' . $assignment->route->id) }}" class="text-primary-600 hover:text-primary-900">
                                {{ $assignment->route->name }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Vehicle</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            @if($assignment->vehicle)
                                <a href="{{ url('/admin/transport/vehicles/' . $assignment->vehicle->id) }}" class="text-primary-600 hover:text-primary-900">
                                    {{ $assignment->vehicle->vehicle_number }}
                                </a>
                            @else
                                <span class="text-gray-500">Not assigned</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Pickup Stop</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assignment->pickupStop->stop_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Drop Stop</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assignment->dropStop->stop_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Monthly Fare</dt>
                        <dd class="mt-1 text-sm text-gray-900">₹{{ number_format($assignment->monthly_fare, 2) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Booking Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Booking Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Booking Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assignment->booking_date?->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Start Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assignment->start_date?->format('d M Y') ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">End Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $assignment->end_date?->format('d M Y') ?? 'Ongoing' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Booking Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $assignment->booking_status === 'active' ? 'bg-green-100 text-green-800' : ($assignment->booking_status === 'confirmed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($assignment->booking_status) }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $assignment->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($assignment->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            @if($assignment->notes)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                <p class="text-sm text-gray-900">{{ $assignment->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Bills -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Bills</h3>
                @if($assignment->bills->count() > 0)
                    <ul class="space-y-2">
                        @foreach($assignment->bills->take(5) as $bill)
                        <li class="text-sm">
                            <a href="{{ url('/admin/transport/bills/' . $bill->id) }}" class="text-primary-600 hover:text-primary-900">
                                {{ $bill->bill_number }}
                            </a>
                            <span class="text-gray-500">- ₹{{ number_format($bill->net_amount, 2) }}</span>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $bill->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($bill->status) }}
                            </span>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">No bills generated</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

