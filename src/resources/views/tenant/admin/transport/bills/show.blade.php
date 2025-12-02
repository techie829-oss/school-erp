@extends('tenant.layouts.admin')

@section('title', 'Bill Details')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/transport/bills') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Bills</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Details</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Transport Bill Details</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $bill->bill_number }}</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <a href="{{ url('/admin/transport/bills/' . $bill->id . '/print') }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Preview & Print
            </a>
            <a href="{{ url('/admin/transport/bills') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Bill Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Bill Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Bill Number</dt>
                        <dd class="mt-1 text-sm font-medium text-gray-900">{{ $bill->bill_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Bill Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $bill->bill_date->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Due Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ $bill->due_date->format('d M Y') }}
                            @if($bill->is_overdue)
                                <span class="ml-2 text-red-600 text-xs">(Overdue)</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="mt-1">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $bill->status === 'paid' ? 'bg-green-100 text-green-800' : ($bill->status === 'overdue' ? 'bg-red-100 text-red-800' : ($bill->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($bill->status) }}
                            </span>
                        </dd>
                    </div>
                    @if($bill->academic_year)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $bill->academic_year }}</dd>
                    </div>
                    @endif
                    @if($bill->term)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Term</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $bill->term }}</dd>
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
                        <dd class="mt-1 text-sm text-gray-900">{{ $bill->student->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Admission Number</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $bill->student->admission_number }}</dd>
                    </div>
                    @if($bill->assignment)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Route</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $bill->assignment->route->name }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Bill Items -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Bill Items</h3>
                @if($bill->items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Discount</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($bill->items as $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $item->description }}</td>
                                    <td class="px-4 py-3 text-sm text-center text-gray-900">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900">₹{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right text-gray-900">₹{{ number_format($item->discount, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-medium text-gray-900">₹{{ number_format($item->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Subtotal:</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right">₹{{ number_format($bill->total_amount, 2) }}</td>
                                </tr>
                                @if($bill->discount_amount > 0)
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Discount:</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right">-₹{{ number_format($bill->discount_amount, 2) }}</td>
                                </tr>
                                @endif
                                @if($bill->tax_amount > 0)
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Tax:</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 text-right">₹{{ number_format($bill->tax_amount, 2) }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm font-bold text-gray-900 text-right">Total:</td>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right">₹{{ number_format($bill->net_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Paid:</td>
                                    <td class="px-4 py-3 text-sm font-medium text-green-600 text-right">₹{{ number_format($bill->paid_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm font-medium text-gray-900 text-right">Outstanding:</td>
                                    <td class="px-4 py-3 text-sm font-medium text-red-600 text-right">₹{{ number_format($bill->outstanding_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500">No items in this bill</p>
                @endif
            </div>

            @if($bill->notes)
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                <p class="text-sm text-gray-900">{{ $bill->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Payment Summary -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Summary</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Total Amount</dt>
                        <dd class="text-sm font-medium text-gray-900">₹{{ number_format($bill->net_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm font-medium text-gray-500">Paid Amount</dt>
                        <dd class="text-sm font-medium text-green-600">₹{{ number_format($bill->paid_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between border-t pt-3">
                        <dt class="text-sm font-bold text-gray-900">Outstanding</dt>
                        <dd class="text-sm font-bold text-red-600">₹{{ number_format($bill->outstanding_amount, 2) }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Payments -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Payments</h3>
                @if($bill->payments->count() > 0)
                    <ul class="space-y-2">
                        @foreach($bill->payments as $payment)
                        <li class="text-sm border-l-2 border-primary-500 pl-3">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-900">{{ $payment->payment_number }}</span>
                                <span class="text-gray-900">₹{{ number_format($payment->amount, 2) }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $payment->payment_date->format('d M Y') }} - {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-gray-500">No payments received</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

