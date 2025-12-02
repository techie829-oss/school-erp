@extends('tenant.layouts.admin')

@section('title', 'Generate Transport Bill')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/transport/bills') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Bills</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Generate Bill</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Generate Transport Bill</h2>
        </div>
    </div>

    <form action="{{ url('/admin/transport/bills') }}" method="POST" class="max-w-3xl">
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
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }} ({{ $student->admission_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="assignment_id" class="block text-sm font-medium text-gray-700">Transport Assignment</label>
                    <select name="assignment_id" id="assignment_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Assignment (Optional)</option>
                        @foreach($assignments as $assignment)
                            <option value="{{ $assignment->id }}" {{ old('assignment_id') == $assignment->id ? 'selected' : '' }}>
                                {{ $assignment->student->full_name }} - {{ $assignment->route->name }} (₹{{ number_format($assignment->monthly_fare, 2) }}/month)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="bill_date" class="block text-sm font-medium text-gray-700">Bill Date <span class="text-red-500">*</span></label>
                    <input type="date" name="bill_date" id="bill_date" value="{{ old('bill_date', date('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700">Due Date <span class="text-red-500">*</span></label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700">Academic Year</label>
                    <input type="text" name="academic_year" id="academic_year" value="{{ old('academic_year') }}" placeholder="e.g., 2024-2025" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="term" class="block text-sm font-medium text-gray-700">Term</label>
                    <input type="text" name="term" id="term" value="{{ old('term') }}" placeholder="e.g., Monthly, Term 1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>

            <!-- Bill Items -->
            <div class="border-t border-gray-200 pt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Bill Items</h3>
                    <button type="button" onclick="addItem()" class="px-3 py-1 text-sm font-medium text-primary-600 bg-primary-50 rounded-md hover:bg-primary-100">
                        + Add Item
                    </button>
                </div>
                <div id="items-container" class="space-y-4">
                    <!-- Items will be added dynamically -->
                </div>
            </div>

            <!-- Totals -->
            <div class="border-t border-gray-200 pt-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="discount_amount" class="block text-sm font-medium text-gray-700">Discount Amount (₹)</label>
                        <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="tax_amount" class="block text-sm font-medium text-gray-700">Tax Amount (₹)</label>
                        <input type="number" name="tax_amount" id="tax_amount" value="{{ old('tax_amount', 0) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/transport/bills') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Generate Bill</button>
        </div>
    </form>
</div>

<script>
let itemCount = 0;

function addItem() {
    itemCount++;
    const container = document.getElementById('items-container');
    const itemHtml = `
        <div class="item-row bg-gray-50 p-4 rounded-lg border border-gray-200">
            <div class="flex justify-between items-start mb-3">
                <h4 class="text-sm font-medium text-gray-700">Item ${itemCount}</h4>
                <button type="button" onclick="removeItem(this)" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-700">Description <span class="text-red-500">*</span></label>
                    <input type="text" name="items[${itemCount}][description]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="e.g., Monthly Transport Fee">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Quantity <span class="text-red-500">*</span></label>
                    <input type="number" name="items[${itemCount}][quantity]" value="1" required min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Unit Price (₹) <span class="text-red-500">*</span></label>
                    <input type="number" name="items[${itemCount}][unit_price]" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700">Discount (₹)</label>
                    <input type="number" name="items[${itemCount}][discount]" step="0.01" min="0" value="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHtml);
}

function removeItem(button) {
    button.closest('.item-row').remove();
}

// Add one item by default
document.addEventListener('DOMContentLoaded', function() {
    addItem();
});
</script>
@endsection

