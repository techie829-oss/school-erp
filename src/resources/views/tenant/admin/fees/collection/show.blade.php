@extends('tenant.layouts.admin')

@section('title', 'Student Fee Details')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <a href="{{ url('/admin/fees/collection') }}" 
                       class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Fee Details</h1>
                        <p class="text-gray-600">{{ $student->first_name }} {{ $student->last_name }}</p>
                    </div>
                </div>
                @if($student->studentFeeCard && $student->studentFeeCard->balance_amount > 0)
                    <a href="{{ url('/admin/fees/collection/' . $student->id . '/collect') }}" 
                       class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition">
                        <i class="fas fa-rupee-sign mr-2"></i>Collect Payment
                    </a>
                @endif
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Student Info & Fee Summary -->
            <div class="space-y-6">
                <!-- Student Card -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user-graduate text-blue-600 mr-2"></i>
                        Student Information
                    </h2>
                    <div class="space-y-3">
                        @if($student->photo)
                            <div class="flex justify-center mb-4">
                                <img src="{{ $student->photo_url }}" 
                                     alt="{{ $student->first_name }}" 
                                     class="w-24 h-24 rounded-full border-4 border-blue-100">
                            </div>
                        @endif
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
                            <p>{{ $student->schoolClass->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Section</p>
                            <p>{{ $student->section->name ?? 'N/A' }}</p>
                        </div>
                        @if($student->phone)
                            <div>
                                <p class="text-sm text-gray-500">Phone</p>
                                <p>{{ $student->phone }}</p>
                            </div>
                        @endif
                        @if($student->email)
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="text-sm">{{ $student->email }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Fee Summary -->
                @if($student->studentFeeCard)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-chart-pie text-green-600 mr-2"></i>
                            Fee Summary
                        </h2>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Academic Year:</span>
                                <span class="font-semibold">{{ $student->studentFeeCard->academic_year }}</span>
                            </div>
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
                                <span class="font-bold text-xl {{ $student->studentFeeCard->balance_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    ₹{{ number_format($student->studentFeeCard->balance_amount, 2) }}
                                </span>
                            </div>
                            <div class="mt-4">
                                <span class="text-sm text-gray-500">Status:</span>
                                @if($student->studentFeeCard->status == 'paid')
                                    <span class="ml-2 px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                        Fully Paid
                                    </span>
                                @elseif($student->studentFeeCard->status == 'partial')
                                    <span class="ml-2 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                        Partial Payment
                                    </span>
                                @elseif($student->studentFeeCard->status == 'overdue')
                                    <span class="ml-2 px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                        Overdue
                                    </span>
                                @else
                                    <span class="ml-2 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                        Active
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl mr-3"></i>
                            <div>
                                <p class="font-semibold text-yellow-800">No Fee Card Assigned</p>
                                <p class="text-sm text-yellow-700">Please assign a fee plan to this student.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Fee Items & Payment History -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Fee Items Breakdown -->
                @if($student->studentFeeCard && $student->studentFeeCard->feeItems->count() > 0)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-list text-purple-600 mr-2"></i>
                            Fee Breakdown
                        </h2>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Component</th>
                                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Original</th>
                                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Discount</th>
                                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Net Amount</th>
                                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Paid</th>
                                        <th class="px-4 py-3 text-right text-sm font-semibold text-gray-700">Balance</th>
                                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($student->studentFeeCard->feeItems as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <div>
                                                    <p class="font-medium text-gray-800">{{ $item->feeComponent->name }}</p>
                                                    @if($item->due_date)
                                                        <p class="text-xs text-gray-500">Due: {{ $item->due_date->format('d M Y') }}</p>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-right">₹{{ number_format($item->original_amount, 2) }}</td>
                                            <td class="px-4 py-3 text-right text-green-600">
                                                @if($item->discount_amount > 0)
                                                    -₹{{ number_format($item->discount_amount, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-right font-semibold">₹{{ number_format($item->net_amount, 2) }}</td>
                                            <td class="px-4 py-3 text-right text-blue-600 font-semibold">₹{{ number_format($item->paid_amount, 2) }}</td>
                                            <td class="px-4 py-3 text-right font-semibold {{ ($item->net_amount - $item->paid_amount) > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                ₹{{ number_format($item->net_amount - $item->paid_amount, 2) }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($item->status == 'paid')
                                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Paid</span>
                                                @elseif($item->status == 'partial')
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold">Partial</span>
                                                @else
                                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Payment History -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-history text-blue-600 mr-2"></i>
                        Payment History
                    </h2>
                    @if($payments->count() > 0)
                        <div class="space-y-4">
                            @foreach($payments as $payment)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <p class="font-semibold text-gray-800">Payment #{{ $payment->payment_number }}</p>
                                            <p class="text-sm text-gray-500">{{ $payment->payment_date->format('d M Y, h:i A') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-2xl font-bold text-green-600">₹{{ number_format($payment->amount, 2) }}</p>
                                            @if($payment->status == 'success')
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Success</span>
                                            @else
                                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold">{{ ucfirst($payment->status) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4 mt-3 text-sm">
                                        <div>
                                            <p class="text-gray-500">Method:</p>
                                            <p class="font-medium capitalize">{{ str_replace('_', ' ', $payment->payment_method) }}</p>
                                        </div>
                                        @if($payment->transaction_id)
                                            <div>
                                                <p class="text-gray-500">Transaction ID:</p>
                                                <p class="font-mono text-xs">{{ $payment->transaction_id }}</p>
                                            </div>
                                        @endif
                                        @if($payment->reference_number)
                                            <div>
                                                <p class="text-gray-500">Reference:</p>
                                                <p class="font-mono text-xs">{{ $payment->reference_number }}</p>
                                            </div>
                                        @endif
                                        @if($payment->notes)
                                            <div class="col-span-2">
                                                <p class="text-gray-500">Notes:</p>
                                                <p class="text-gray-700">{{ $payment->notes }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3 pt-3 border-t flex justify-between items-center">
                                        <p class="text-sm text-gray-500">
                                            Collected by: {{ $payment->collectedBy->name ?? 'System' }}
                                        </p>
                                        <a href="{{ url('/admin/fees/collection/receipt/' . $payment->id) }}" 
                                           class="px-3 py-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition text-sm">
                                            <i class="fas fa-receipt mr-1"></i>Receipt
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500">No payments recorded yet</p>
                        </div>
                    @endif
                </div>

                <!-- Invoices -->
                @if($invoices->count() > 0)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-file-invoice text-orange-600 mr-2"></i>
                            Invoices
                        </h2>
                        <div class="space-y-3">
                            @foreach($invoices as $invoice)
                                <div class="border border-gray-200 rounded-lg p-4 flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold">{{ $invoice->invoice_number }}</p>
                                        <p class="text-sm text-gray-500">Date: {{ $invoice->invoice_date->format('d M Y') }}</p>
                                        <p class="text-sm text-gray-500">Due: {{ $invoice->due_date->format('d M Y') }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-bold">₹{{ number_format($invoice->net_amount, 2) }}</p>
                                        <p class="text-sm text-gray-500">Paid: ₹{{ number_format($invoice->paid_amount, 2) }}</p>
                                        @if($invoice->status == 'paid')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">Paid</span>
                                        @elseif($invoice->status == 'partial')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold">Partial</span>
                                        @else
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded text-xs font-semibold">{{ ucfirst($invoice->status) }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

