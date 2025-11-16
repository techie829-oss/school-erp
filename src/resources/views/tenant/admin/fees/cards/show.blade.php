@extends('tenant.layouts.admin')

@section('title', 'Student Fee Card - ' . $student->full_name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ url('/admin/dashboard') }}" class="text-gray-700 hover:text-primary-600">
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ url('/admin/fees/collection') }}" class="ml-1 text-gray-700 hover:text-primary-600">Fee Collection</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-gray-500">Fee Card</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Student Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                @if($student->photo_url)
                    <img src="{{ asset('storage/' . $student->photo_url) }}" alt="{{ $student->full_name }}" class="w-20 h-20 rounded-full border-4 border-white">
                @else
                    <div class="w-20 h-20 rounded-full border-4 border-white bg-white/20 flex items-center justify-center">
                        <span class="text-3xl font-bold">{{ substr($student->full_name, 0, 1) }}</span>
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold">{{ $student->full_name }}</h1>
                    <p class="text-blue-100">{{ $student->admission_number }}</p>
                    <p class="text-sm text-blue-200">
                        {{ $student->currentEnrollment?->schoolClass?->class_name }}
                        {{ $student->currentEnrollment?->section?->section_name ? '- Section ' . $student->currentEnrollment->section->section_name : '' }}
                    </p>
                </div>
            </div>
            <div class="text-right space-y-2">
                <a href="{{ url('/admin/fees/cards/' . $student->id . '/print') }}" target="_blank"
                   class="inline-flex items-center px-4 py-2 bg-white text-blue-700 rounded-lg hover:bg-blue-50 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Fee Card
                </a>
                <a href="{{ url('/admin/fees/collection/' . $student->id) }}"
                   class="block px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition text-center">
                    Collect Payment
                </a>
            </div>
        </div>
    </div>

    <!-- Fee Cards Summary -->
    @if($feeCards->count() > 0)
        @php
            $totalDue = $feeCards->sum('balance_amount');
            $totalPaid = $feeCards->sum('paid_amount');
            $totalAmount = $feeCards->sum('total_amount');
        @endphp

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Amount</p>
                        <p class="text-2xl font-bold text-gray-900">₹{{ number_format($totalAmount, 2) }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Total Paid</p>
                        <p class="text-2xl font-bold text-emerald-600">₹{{ number_format($totalPaid, 2) }}</p>
                    </div>
                    <div class="p-3 bg-emerald-100 rounded-full">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Balance Due</p>
                        <p class="text-2xl font-bold text-red-600">₹{{ number_format($totalDue, 2) }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Payment Status</p>
                        @if($totalDue <= 0)
                            <p class="text-lg font-bold text-emerald-600">Paid</p>
                        @elseif($totalPaid > 0)
                            <p class="text-lg font-bold text-yellow-600">Partial</p>
                        @else
                            <p class="text-lg font-bold text-red-600">Unpaid</p>
                        @endif
                    </div>
                    <div class="p-3 {{ $totalDue <= 0 ? 'bg-emerald-100' : 'bg-yellow-100' }} rounded-full">
                        <svg class="w-6 h-6 {{ $totalDue <= 0 ? 'text-emerald-600' : 'text-yellow-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Cards Details -->
        @foreach($feeCards as $card)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $card->feePlan->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $card->academic_year }} • {{ $card->feePlan->schoolClass->class_name }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            @php
                                $statusColors = [
                                    'paid' => 'bg-emerald-100 text-emerald-800',
                                    'partial' => 'bg-yellow-100 text-yellow-800',
                                    'overdue' => 'bg-red-100 text-red-800',
                                    'active' => 'bg-blue-100 text-blue-800',
                                ];
                            @endphp
                            <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$card->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($card->status) }}
                            </span>
                            @if($card->balance_amount > 0)
                                <button onclick="showDiscountModal({{ $card->id }})"
                                        class="px-3 py-1 text-xs font-medium text-primary-700 bg-primary-50 rounded-full hover:bg-primary-100">
                                    Apply Discount
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Fee Items -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Component</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Discount</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net Amount</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($card->feeItems as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->feeComponent->name }}</div>
                                        @if($item->discount_reason)
                                            <div class="text-xs text-gray-500">{{ $item->discount_reason }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900">₹{{ number_format($item->original_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-emerald-600">
                                        @if($item->discount_amount > 0)
                                            -₹{{ number_format($item->discount_amount, 2) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">₹{{ number_format($item->net_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900">₹{{ number_format($item->paid_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-medium {{ $item->net_amount - $item->paid_amount > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                        ₹{{ number_format($item->net_amount - $item->paid_amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-900">
                                        {{ $item->due_date ? $item->due_date->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @php
                                            $itemStatusColors = [
                                                'paid' => 'bg-emerald-100 text-emerald-800',
                                                'partial' => 'bg-yellow-100 text-yellow-800',
                                                'unpaid' => 'bg-red-100 text-red-800',
                                                'waived' => 'bg-gray-100 text-gray-800',
                                            ];
                                        @endphp
                                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $itemStatusColors[$item->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($item->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Total:</td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">₹{{ number_format($card->total_amount - $card->discount_amount, 2) }}</td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-emerald-600">₹{{ number_format($card->paid_amount, 2) }}</td>
                                <td class="px-6 py-4 text-right text-sm font-bold text-red-600">₹{{ number_format($card->balance_amount, 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endforeach

        <!-- Payment History -->
        @if($payments->count() > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Payment History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment #</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($payments as $payment)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $payment->payment_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-emerald-600">₹{{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ ucfirst($payment->payment_method) }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $payment->reference_number ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ url('/admin/fees/receipts/' . $payment->id) }}" target="_blank"
                                           class="text-primary-600 hover:text-primary-900 text-sm font-medium">
                                            View Receipt
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    @else
        <!-- No Fee Cards -->
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No Fee Card Found</h3>
            <p class="mt-1 text-sm text-gray-500">This student has not been assigned to any fee plan yet.</p>
            <div class="mt-6">
                <a href="{{ url('/admin/fees/plans') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                    Go to Fee Plans
                </a>
            </div>
        </div>
    @endif
</div>

<!-- Discount Modal -->
<div id="discountModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Apply Discount</h3>
            <form id="discountForm" method="POST" action="">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Discount Type</label>
                        <select name="discount_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (₹)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Discount Value</label>
                        <input type="number" name="discount_value" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reason</label>
                        <textarea name="discount_reason" rows="3" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeDiscountModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">Apply Discount</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showDiscountModal(feeCardId) {
    const form = document.getElementById('discountForm');
    form.action = `/admin/fees/cards/${feeCardId}/discount`;
    document.getElementById('discountModal').classList.remove('hidden');
}

function closeDiscountModal() {
    document.getElementById('discountModal').classList.add('hidden');
}
</script>
@endpush
@endsection

