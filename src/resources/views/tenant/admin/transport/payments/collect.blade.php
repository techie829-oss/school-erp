@extends('tenant.layouts.admin')

@section('title', 'Collect Transport Payment')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/transport/payments') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Payments</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Collect Payment</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Collect Transport Payment</h2>
        </div>
    </div>

    <form action="{{ url('/admin/transport/payments') }}" method="POST" class="max-w-3xl">
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
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" {{ ($student && $student->id == $s->id) || old('student_id') == $s->id ? 'selected' : '' }}>
                                {{ $s->full_name }} ({{ $s->admission_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="bill_id" class="block text-sm font-medium text-gray-700">Bill (Optional)</label>
                    <select name="bill_id" id="bill_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Bill (Optional)</option>
                        @foreach($bills as $bill)
                            <option value="{{ $bill->id }}" data-outstanding="{{ $bill->outstanding_amount }}" {{ old('bill_id') == $bill->id ? 'selected' : '' }}>
                                {{ $bill->bill_number }} - ₹{{ number_format($bill->net_amount, 2) }} (Outstanding: ₹{{ number_format($bill->outstanding_amount, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date <span class="text-red-500">*</span></label>
                    <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount (₹) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Payment Method <span class="text-red-500">*</span></label>
                    <select name="payment_method" id="payment_method" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="payment_type" class="block text-sm font-medium text-gray-700">Payment Type</label>
                    <input type="text" name="payment_type" id="payment_type" value="{{ old('payment_type') }}" placeholder="e.g., monthly_fare" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="transaction_id" class="block text-sm font-medium text-gray-700">Transaction ID</label>
                    <input type="text" name="transaction_id" id="transaction_id" value="{{ old('transaction_id') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="reference_number" class="block text-sm font-medium text-gray-700">Reference Number</label>
                    <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <!-- Cheque fields (shown when payment method is cheque) -->
                <div id="cheque-fields" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="cheque_number" class="block text-sm font-medium text-gray-700">Cheque Number</label>
                        <input type="text" name="cheque_number" id="cheque_number" value="{{ old('cheque_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="cheque_date" class="block text-sm font-medium text-gray-700">Cheque Date</label>
                        <input type="date" name="cheque_date" id="cheque_date" value="{{ old('cheque_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="bank_name" class="block text-sm font-medium text-gray-700">Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/transport/payments') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Collect Payment</button>
        </div>
    </form>
</div>

<script>
// Show/hide cheque fields based on payment method
document.getElementById('payment_method').addEventListener('change', function() {
    const chequeFields = document.getElementById('cheque-fields');
    if (this.value === 'cheque') {
        chequeFields.classList.remove('hidden');
    } else {
        chequeFields.classList.add('hidden');
    }
});

// Auto-fill amount from bill outstanding amount
document.getElementById('bill_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.value) {
        const outstanding = selectedOption.getAttribute('data-outstanding');
        if (outstanding) {
            document.getElementById('amount').value = outstanding;
        }
    }
});
</script>
@endsection

