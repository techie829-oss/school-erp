@extends('tenant.layouts.admin')

@section('title', 'Payment Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/transport/payments') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Payments</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Payment Details</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $payment->payment_number }}</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/transport/payments/' . $payment->id . '/receipt') }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Preview & Print Receipt
            </a>
            <a href="{{ url('/admin/transport/payments') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Payment Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Number</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $payment->payment_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->payment_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Amount</dt>
                        <dd class="mt-1 text-2xl font-bold text-gray-900">₹{{ number_format($payment->amount, 2) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payment->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </dd>
                    </div>
                    @if($payment->payment_type)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Payment Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->payment_type }}</dd>
                    </div>
                    @endif
                    @if($payment->transaction_id)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Transaction ID</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->transaction_id }}</dd>
                    </div>
                    @endif
                    @if($payment->reference_number)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Reference Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->reference_number }}</dd>
                    </div>
                    @endif
                    @if($payment->collected_by)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Collected By</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->collector->name ?? '-' }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Student Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Student Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->student->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Admission Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $payment->student->admission_number }}</dd>
                    </div>
                </dl>
            </div>

            @if($payment->bill)
            <!-- Bill Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Bill Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Bill Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <a href="{{ url('/admin/transport/bills/' . $payment->bill->id) }}" class="text-primary-600 hover:text-primary-900">
                                {{ $payment->bill->bill_number }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Bill Amount</dt>
                        <dd class="mt-1 text-sm text-gray-900">₹{{ number_format($payment->bill->net_amount, 2) }}</dd>
                    </div>
                </dl>
            </div>
            @endif

            @if($payment->notes)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                <p class="text-sm text-gray-900">{{ $payment->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
                <div class="space-y-2">
                    <a href="{{ url('/admin/transport/payments/' . $payment->id . '/receipt') }}" target="_blank" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Preview & Print Receipt
                    </a>
                    @if($payment->bill)
                    <a href="{{ url('/admin/transport/bills/' . $payment->bill->id) }}" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        View Bill
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

