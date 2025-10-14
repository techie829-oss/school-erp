@extends('tenant.layouts.admin')

@section('title', 'Collect Fee')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6">
    <div class="max-w-5xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ url('/admin/fees/collection') }}" 
                   class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Collect Fee Payment</h1>
            </div>
            <p class="text-gray-600">Process fee payment for student</p>
        </div>

        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Student Info & Fee Breakdown -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Student Card -->
                <div class="bg-white rounded-xl shadow-lg p-6">
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
                <div class="bg-white rounded-xl shadow-lg p-6">
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
                <div class="bg-white rounded-xl shadow-lg p-6">
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
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Payment Details</h2>

                    <form action="{{ url('/admin/fees/collection/' . $student->id . '/payment') }}" method="POST">
                        @csrf

                        <!-- Payment Amount -->
                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">
                                Payment Amount <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-gray-500 font-semibold">₹</span>
                                <input type="number" 
                                       name="amount" 
                                       value="{{ old('amount', $student->studentFeeCard->balance_amount) }}"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg font-semibold"
                                       placeholder="0.00"
                                       step="0.01"
                                       min="1"
                                       max="{{ $student->studentFeeCard->balance_amount }}"
                                       required>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">Maximum: ₹{{ number_format($student->studentFeeCard->balance_amount, 2) }}</p>
                        </div>

                        <!-- Payment Date -->
                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">
                                Payment Date <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="payment_date" 
                                   value="{{ old('payment_date', date('Y-m-d')) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   required>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">
                                Payment Method <span class="text-red-500">*</span>
                            </label>
                            <select name="payment_method" 
                                    id="paymentMethod"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    onchange="togglePaymentFields()"
                                    required>
                                <option value="">-- Select Method --</option>
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
                        <div class="mb-6" id="referenceField" style="display: none;">
                            <label class="block text-gray-700 font-semibold mb-2">
                                Reference Number / Cheque Number
                            </label>
                            <input type="text" 
                                   name="reference_number" 
                                   value="{{ old('reference_number') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter reference/cheque number">
                        </div>

                        <!-- Transaction ID (for online/razorpay) -->
                        <div class="mb-6" id="transactionField" style="display: none;">
                            <label class="block text-gray-700 font-semibold mb-2">
                                Transaction ID
                            </label>
                            <input type="text" 
                                   name="transaction_id" 
                                   value="{{ old('transaction_id') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter transaction ID">
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label class="block text-gray-700 font-semibold mb-2">
                                Notes / Remarks
                            </label>
                            <textarea name="notes" 
                                      rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="Optional payment notes">{{ old('notes') }}</textarea>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex gap-4">
                            <button type="submit" 
                                    class="flex-1 px-6 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white text-lg font-semibold rounded-lg hover:shadow-lg transform hover:scale-105 transition">
                                <i class="fas fa-check mr-2"></i>Collect Payment
                            </button>
                            <a href="{{ url('/admin/fees/collection') }}" 
                               class="px-6 py-4 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                                Cancel
                            </a>
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

