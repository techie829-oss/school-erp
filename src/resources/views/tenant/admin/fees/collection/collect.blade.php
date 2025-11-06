@extends('tenant.layouts.admin')

@section('title', 'Collect Fee')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
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
                    <a href="{{ url('/admin/fees/collection/' . $student->id) }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Student Details</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Collect Payment</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Collect Fee Payment
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $student->first_name }} {{ $student->last_name }} • {{ $student->admission_number }}
            </p>
        </div>
    </div>

    <!-- Error Messages -->
    @if($errors->any())
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Student Info & Fee Breakdown -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Student Card -->
            <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Student Information</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Name</p>
                            <p class="font-semibold">{{ $student->first_name }} {{ $student->last_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Admission No</p>
                            <p class="font-mono">{{ $student->admission_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Class</p>
                            <p>{{ $student->schoolClass->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Fee Summary -->
            <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Fee Summary</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Fee:</span>
                            <span class="font-semibold">₹{{ number_format($student->studentFeeCard->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Discount:</span>
                            <span class="font-semibold text-green-600">-₹{{ number_format($student->studentFeeCard->discount_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Paid:</span>
                            <span class="font-semibold text-blue-600">₹{{ number_format($student->studentFeeCard->paid_amount, 2) }}</span>
                        </div>
                        <div class="border-t pt-3 flex justify-between">
                            <span class="font-bold text-gray-800">Balance Due:</span>
                            <span class="font-bold text-xl text-red-600">₹{{ number_format($student->studentFeeCard->balance_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Fee Items -->
            <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Fee Breakdown</h2>
                    <div class="space-y-2">
                        @foreach($student->studentFeeCard->feeItems as $item)
                            <div class="flex justify-between text-sm">
                                <div>
                                    <p class="font-medium">{{ $item->feeComponent->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        Paid: ₹{{ number_format($item->paid_amount, 2) }} / ₹{{ number_format($item->net_amount, 2) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    @if($item->status == 'paid')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Paid</span>
                                    @elseif($item->status == 'partial')
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">Partial</span>
                                    @else
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs">Pending</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>

                    <form action="{{ url('/admin/fees/collection/' . $student->id . '/payment') }}" method="POST">
                        @csrf

                        <div class="space-y-4">
                            <!-- Payment Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700">
                                    Payment Amount <span class="text-red-500">*</span>
                                </label>
                                <div class="relative mt-1">
                                    <span class="absolute left-3 top-2 text-gray-500">₹</span>
                                    <input type="number" name="amount" id="amount"
                                           value="{{ old('amount', $student->studentFeeCard->balance_amount) }}"
                                           class="pl-8 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('amount') border-red-300 @enderror"
                                           placeholder="0.00" step="0.01" min="1"
                                           max="{{ $student->studentFeeCard->balance_amount }}" required>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">Maximum: ₹{{ number_format($student->studentFeeCard->balance_amount, 2) }}</p>
                            </div>

                            <!-- Payment Date -->
                            <div>
                                <label for="payment_date" class="block text-sm font-medium text-gray-700">
                                    Payment Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="payment_date" id="payment_date"
                                       value="{{ old('payment_date', date('Y-m-d')) }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('payment_date') border-red-300 @enderror">
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <label for="payment_method" class="block text-sm font-medium text-gray-700">
                                    Payment Method <span class="text-red-500">*</span>
                                </label>
                                <select name="payment_method" id="paymentMethod" onchange="togglePaymentFields()" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm @error('payment_method') border-red-300 @enderror">
                                    <option value="">Select Method</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                                    @if($paymentSettings['enable_razorpay'] ?? false)
                                        <option value="razorpay" {{ old('payment_method') == 'razorpay' ? 'selected' : '' }}>Razorpay</option>
                                    @endif
                                </select>
                            </div>

                            <!-- Reference Number (for cheque/bank transfer) -->
                            <div id="referenceField" style="display: none;">
                                <label for="reference_number" class="block text-sm font-medium text-gray-700">
                                    Reference Number / Cheque Number
                                </label>
                                <input type="text" name="reference_number" id="reference_number"
                                       value="{{ old('reference_number') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                       placeholder="Enter reference/cheque number">
                            </div>

                            <!-- Transaction ID (for online/razorpay) -->
                            <div id="transactionField" style="display: none;">
                                <label for="transaction_id" class="block text-sm font-medium text-gray-700">
                                    Transaction ID
                                </label>
                                <input type="text" name="transaction_id" id="transaction_id"
                                       value="{{ old('transaction_id') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                       placeholder="Enter transaction ID">
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes / Remarks</label>
                                <textarea name="notes" id="notes" rows="2"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                                    placeholder="Optional payment notes">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ url('/admin/fees/collection/' . $student->id) }}"
                               class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Collect Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePaymentFields() {
    const method = document.getElementById('paymentMethod').value;
    const referenceField = document.getElementById('referenceField');
    const transactionField = document.getElementById('transactionField');

    // Hide all optional fields first
    referenceField.style.display = 'none';
    transactionField.style.display = 'none';

    // Show relevant fields based on method
    if (method === 'cheque' || method === 'bank_transfer') {
        referenceField.style.display = 'block';
    } else if (method === 'online' || method === 'razorpay') {
        transactionField.style.display = 'block';
    }
}

// Call on page load
togglePaymentFields();
</script>
@endsection

