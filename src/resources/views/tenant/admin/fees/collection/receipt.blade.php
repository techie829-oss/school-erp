@extends('tenant.layouts.admin')

@section('title', 'Payment Receipt')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ url('/admin/dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                    </svg>
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ url('/admin/fees/collection') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Fee Collection</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ url('/admin/fees/collection/' . $payment->student_id) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Student Details</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Receipt</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Payment Receipt
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Receipt #{{ $payment->payment_number }}
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <button onclick="window.print()"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
            <a href="{{ url('/admin/fees/collection/' . $payment->student_id) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <!-- Receipt Content -->
    <div class="bg-white shadow rounded-lg p-8" id="receiptContent">
        <!-- School Header -->
        <div class="text-center border-b-2 border-primary-600 pb-6 mb-6">
            <h1 class="text-3xl font-bold text-gray-900">{{ $tenant->data['school_name'] ?? 'School' }}</h1>
            @if(isset($tenant->data['school_address']))
                <p class="text-sm text-gray-600 mt-1">{{ $tenant->data['school_address'] }}</p>
            @endif
            @if(isset($tenant->data['school_phone']))
                <p class="text-sm text-gray-600">Phone: {{ $tenant->data['school_phone'] }}</p>
            @endif
            <p class="text-lg font-semibold text-primary-600 mt-2">FEE PAYMENT RECEIPT</p>
        </div>

        <!-- Receipt Info -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-500">Receipt No:</p>
                <p class="font-semibold text-lg">{{ $payment->payment_number }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Date:</p>
                <p class="font-semibold">{{ $payment->payment_date->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Student Information -->
        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-3">Student Information</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Student Name:</p>
                    <p class="font-medium">{{ $payment->student->full_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Admission No:</p>
                    <p class="font-medium font-mono">{{ $payment->student->admission_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Class:</p>
                    <p class="font-medium">{{ $payment->student->schoolClass->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Section:</p>
                    <p class="font-medium">{{ $payment->student->section->name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="mb-6">
            <h3 class="font-semibold text-gray-900 mb-3">Payment Details</h3>
            <table class="w-full border border-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Description</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @if($payment->invoice && $payment->invoice->items->count() > 0)
                        @foreach($payment->invoice->items as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $item->description }}</td>
                                <td class="px-4 py-3 text-sm text-right">₹{{ number_format($item->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">Fee Payment</td>
                            <td class="px-4 py-3 text-sm text-right">₹{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td class="px-4 py-3 text-sm font-bold text-gray-900">Total Paid:</td>
                        <td class="px-4 py-3 text-right">
                            <span class="text-lg font-bold text-green-600">₹{{ number_format($payment->amount, 2) }}</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Payment Method -->
        <div class="grid grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-500">Payment Method:</p>
                <p class="font-medium capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</p>
            </div>
            @if($payment->transaction_id)
                <div>
                    <p class="text-sm text-gray-500">Transaction ID:</p>
                    <p class="font-mono text-sm">{{ $payment->transaction_id }}</p>
                </div>
            @endif
            @if($payment->reference_number)
                <div>
                    <p class="text-sm text-gray-500">Reference Number:</p>
                    <p class="font-mono text-sm">{{ $payment->reference_number }}</p>
                </div>
            @endif
            @if($payment->notes)
                <div class="col-span-2">
                    <p class="text-sm text-gray-500">Remarks:</p>
                    <p class="text-sm">{{ $payment->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Amount in Words -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600">Amount in Words:</p>
            <p class="font-medium text-gray-900">Rupees {{ ucwords(strtolower($payment->amount)) }} Only</p>
        </div>

        <!-- Footer -->
        <div class="flex justify-between items-end pt-6 border-t border-gray-200">
            <div>
                <p class="text-sm text-gray-500">Collected By:</p>
                <p class="font-medium">{{ $payment->collectedBy->name ?? 'System' }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $payment->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="text-right">
                <div class="border-t-2 border-gray-800 pt-2 mt-8">
                    <p class="text-sm font-medium">Authorized Signature</p>
                </div>
            </div>
        </div>

        <!-- Footer Note -->
        <div class="mt-8 text-center text-xs text-gray-500">
            <p>This is a computer-generated receipt and does not require a physical signature.</p>
            <p class="mt-1">For queries, contact: {{ $tenant->data['school_email'] ?? 'admin@school.com' }}</p>
        </div>
    </div>

    <!-- Print Button (Hidden in Print) -->
    <div class="text-center print:hidden">
        <button onclick="window.print()"
                class="inline-flex items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Receipt
        </button>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #receiptContent, #receiptContent * {
        visibility: visible;
    }
    #receiptContent {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .print\:hidden {
        display: none !important;
    }
}
</style>
@endsection

