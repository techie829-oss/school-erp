@extends('tenant.layouts.admin')

@section('title', 'Print Fee Plan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="md:flex md:items-center md:justify-between print:hidden">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Printable Fee Plan Summary</h1>
            <p class="text-sm text-gray-500">Class {{ $plan->schoolClass->class_name ?? 'N/A' }} • {{ $plan->academic_year }} • {{ ucfirst(str_replace('_', ' ', $plan->term)) }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 rounded-md bg-primary-600 text-white text-sm font-semibold hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
            <a href="{{ url('/admin/fees/plans/' . $plan->id) }}" class="inline-flex items-center px-4 py-2 rounded-md border border-gray-300 text-gray-700 text-sm font-medium hover:bg-gray-50">
                Back to Plan
            </a>
        </div>
    </div>

    <div id="printableArea" class="bg-white shadow rounded-2xl p-8 space-y-8">
        <div class="text-center border-b pb-6">
            <h2 class="text-3xl font-bold text-gray-900">{{ $tenant->data['school_name'] ?? 'School ERP' }}</h2>
            @if(isset($tenant->data['school_address']))
                <p class="text-sm text-gray-600 mt-1">{{ $tenant->data['school_address'] }}</p>
            @endif
            @if(isset($tenant->data['school_email']) || isset($tenant->data['school_phone']))
                <p class="text-sm text-gray-500 mt-1">
                    {{ $tenant->data['school_email'] ?? '' }}
                    @if(isset($tenant->data['school_email']) && isset($tenant->data['school_phone'])) • @endif
                    {{ $tenant->data['school_phone'] ?? '' }}
                </p>
            @endif
            <p class="text-lg font-semibold text-primary-600 mt-3">Fee Plan Summary</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-xl p-5 space-y-3">
                <h3 class="text-sm uppercase tracking-wide text-gray-500">Plan Information</h3>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Plan Name:</span>
                    <span class="font-semibold text-gray-900">{{ $plan->name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Class:</span>
                    <span class="font-semibold text-gray-900">{{ $plan->schoolClass->class_name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Academic Year:</span>
                    <span class="font-semibold text-gray-900">{{ $plan->academic_year }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Term:</span>
                    <span class="font-semibold text-gray-900 capitalize">{{ str_replace('_', ' ', $plan->term) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Effective:</span>
                    <span class="font-semibold text-gray-900">
                        {{ optional($plan->effective_from)->format('d M Y') }}
                        @if($plan->effective_to)
                            – {{ $plan->effective_to->format('d M Y') }}
                        @endif
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-semibold {{ $plan->is_active ? 'text-emerald-600' : 'text-gray-500' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div class="pt-3 border-t text-right">
                    <p class="text-xs uppercase text-gray-500">Total Plan Amount</p>
                    <p class="text-3xl font-bold text-primary-600">₹{{ number_format($plan->total_amount, 2) }}</p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-5 space-y-3">
                <h3 class="text-sm uppercase tracking-wide text-gray-500">Statistics</h3>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Fee Components:</span>
                    <span class="font-semibold">{{ $plan->feePlanItems->count() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Students Assigned:</span>
                    <span class="font-semibold">{{ $plan->studentFeeCards->count() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Paid:</span>
                    <span class="font-semibold text-emerald-600">{{ $plan->studentFeeCards->where('status', 'paid')->count() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Partial:</span>
                    <span class="font-semibold text-amber-600">{{ $plan->studentFeeCards->where('status', 'partial')->count() }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Active:</span>
                    <span class="font-semibold text-sky-600">{{ $plan->studentFeeCards->where('status', 'active')->count() }}</span>
                </div>
                <div class="pt-3 border-t text-right">
                    <p class="text-xs uppercase text-gray-500">Outstanding Balance</p>
                    <p class="text-2xl font-bold text-gray-900">₹{{ number_format($plan->studentFeeCards->sum('balance_amount'), 2) }}</p>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Fee Components</h3>
            <table class="w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-600">Component</th>
                        <th class="px-4 py-2 text-left text-gray-600">Type</th>
                        <th class="px-4 py-2 text-right text-gray-600">Amount</th>
                        <th class="px-4 py-2 text-left text-gray-600">Due Date</th>
                        <th class="px-4 py-2 text-center text-gray-600">Mandatory</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($plan->feePlanItems as $item)
                        <tr>
                            <td class="px-4 py-2">
                                <div class="font-medium text-gray-900">{{ $item->feeComponent->name }}</div>
                                <div class="text-xs text-gray-500">{{ $item->feeComponent->code }}</div>
                            </td>
                            <td class="px-4 py-2 text-gray-800">{{ $item->feeComponent->type === 'recurring' ? 'Recurring' : 'One-time' }}</td>
                            <td class="px-4 py-2 text-right text-gray-900 font-semibold">₹{{ number_format($item->amount, 2) }}</td>
                            <td class="px-4 py-2 text-gray-700">{{ $item->due_date ? $item->due_date->format('d M Y') : '-' }}</td>
                            <td class="px-4 py-2 text-center text-gray-900">{{ $item->is_mandatory ? 'Yes' : 'No' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Assigned Students</h3>
            <table class="w-full border border-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-600">Student</th>
                        <th class="px-4 py-2 text-left text-gray-600">Roll No.</th>
                        <th class="px-4 py-2 text-left text-gray-600">Section</th>
                        <th class="px-4 py-2 text-right text-gray-600">Total</th>
                        <th class="px-4 py-2 text-right text-gray-600">Paid</th>
                        <th class="px-4 py-2 text-right text-gray-600">Balance</th>
                        <th class="px-4 py-2 text-center text-gray-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($plan->studentFeeCards as $card)
                        <tr>
                            <td class="px-4 py-2">
                                <div class="font-medium text-gray-900">{{ $card->student->full_name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $card->student->admission_number ?? '' }}</div>
                            </td>
                            <td class="px-4 py-2 text-gray-900">{{ $card->student->currentEnrollment->roll_number ?? '—' }}</td>
                            <td class="px-4 py-2 text-gray-900">{{ $card->student->currentEnrollment?->section?->section_name ?? '—' }}</td>
                            <td class="px-4 py-2 text-right font-semibold text-gray-900">₹{{ number_format($card->total_amount, 2) }}</td>
                            <td class="px-4 py-2 text-right text-gray-900">₹{{ number_format($card->paid_amount, 2) }}</td>
                            <td class="px-4 py-2 text-right text-gray-900">₹{{ number_format($card->balance_amount, 2) }}</td>
                            <td class="px-4 py-2 text-center text-gray-900">{{ ucfirst($card->status) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">No students assigned yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <p class="text-xs text-gray-400 text-center pt-6 border-t">
            Generated on {{ now()->format('d M Y, h:i A') }} from School ERP.
        </p>
    </div>
</div>

<style>
    @media print {
        body {
            background: white;
        }
        .print\:hidden {
            display: none !important;
        }
        #printableArea {
            box-shadow: none !important;
        }
    }
</style>
@endsection

