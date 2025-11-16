@extends('tenant.layouts.admin')

@section('title', 'Fee Plan Details')

@section('content')
<div class="space-y-6">
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
                    <a href="{{ url('/admin/fees/plans') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Fee Plans</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Details</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                {{ $plan->name }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ $plan->schoolClass->class_name ?? 'N/A' }} • {{ $plan->academic_year }} • {{ ucfirst(str_replace('_', ' ', $plan->term)) }}
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3 flex-wrap">
            <a href="{{ url('/admin/fees/plans/' . $plan->id . '/assign') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Assign Students
            </a>
            <a href="{{ url('/admin/fees/plans/' . $plan->id . '/print') }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50" target="_blank">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Plan
            </a>
            <a href="{{ url('/admin/fees/plans/' . $plan->id . '/export') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-700 hover:bg-slate-800">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v12m0 0l-3-3m3 3l3-3M6 20h12"/>
                </svg>
                Export CSV
            </a>
            <a href="{{ url('/admin/fees/plans/' . $plan->id . '/edit') }}"
               class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <a href="{{ url('/admin/fees/plans') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back to List
            </a>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row lg:items-start gap-6">
        <!-- Left Column: Plan Details -->
        <div class="space-y-6 lg:w-1/3">
            <!-- Plan Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Plan Information</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        @if($plan->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Inactive
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Class</p>
                        <p class="font-medium text-gray-900">{{ $plan->schoolClass->class_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Academic Year</p>
                        <p class="font-medium text-gray-900">{{ $plan->academic_year }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Term</p>
                        <p class="font-medium capitalize text-gray-900">{{ str_replace('_', ' ', $plan->term) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Effective From</p>
                        <p class="font-medium text-gray-900">{{ $plan->effective_from->format('d M Y') }}</p>
                    </div>
                    @if($plan->effective_to)
                        <div>
                            <p class="text-sm text-gray-500">Effective To</p>
                            <p class="font-medium text-gray-900">{{ $plan->effective_to->format('d M Y') }}</p>
                        </div>
                    @endif
                    @if($plan->description)
                        <div>
                            <p class="text-sm text-gray-500">Description</p>
                            <p class="text-sm text-gray-900">{{ $plan->description }}</p>
                        </div>
                    @endif
                    <div class="border-t pt-3">
                        <p class="text-sm text-gray-500">Total Amount</p>
                        <p class="text-2xl font-bold text-primary-600">₹{{ number_format($plan->total_amount, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Fee Components:</span>
                        <span class="font-semibold">{{ $plan->feePlanItems->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Students Assigned:</span>
                        <span class="font-semibold">{{ $plan->studentFeeCards->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Fee Components -->
        <div class="lg:w-2/3 space-y-6">
            <!-- Fee Components List -->
            <div class="bg-white shadow rounded-lg w-full">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Fee Components</h3>
                </div>
                @if($plan->feePlanItems->count() > 0)
                    <div class="overflow-x-auto lg:overflow-visible w-full">
                        <!-- Use min-w-full so the table expands to container width and allow automatic layout -->
                        <table class="min-w-full table-auto divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Component</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Mandatory</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($plan->feePlanItems as $item)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->feeComponent->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $item->feeComponent->code }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="text-sm font-semibold text-gray-900">₹{{ number_format($item->amount, 2) }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->feeComponent->type == 'recurring' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                                {{ $item->feeComponent->type == 'recurring' ? 'Recurring' : 'One Time' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $item->due_date ? $item->due_date->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if($item->is_mandatory)
                                                <span class="text-green-600">✓</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <p>No components in this plan</p>
                    </div>
                @endif
            </div>

            <!-- Assigned Students -->
            <div class="bg-white shadow rounded-lg w-full">
                <div class="px-6 py-4 border-b border-gray-200 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Assigned Students</h3>
                        <p class="text-sm text-gray-500">Students currently linked to this plan with generated fee cards.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-700">
                            {{ $plan->studentFeeCards->count() }} Assigned
                        </div>
                        <a href="{{ url('/admin/fees/plans/' . $plan->id . '/assign') }}"
                           class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700">
                            Manage Assignments
                        </a>
                    </div>
                </div>

                @if($plan->studentFeeCards->count() > 0)
                    @php
                        $cards = $plan->studentFeeCards->sortBy(function ($card) {
                            return $card->student->full_name ?? '';
                        });
                        $totalBalance = $plan->studentFeeCards->sum('balance_amount');
                    @endphp
                    <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-3 gap-4 border-b border-gray-100">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Total Outstanding</p>
                            <p class="text-xl font-semibold text-gray-900">₹{{ number_format($totalBalance, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Status Breakdown</p>
                            <div class="text-sm text-gray-900 flex flex-wrap gap-3 mt-1">
                                <span>Paid: {{ $plan->studentFeeCards->where('status', 'paid')->count() }}</span>
                                <span>Partial: {{ $plan->studentFeeCards->where('status', 'partial')->count() }}</span>
                                <span>Active: {{ $plan->studentFeeCards->where('status', 'active')->count() }}</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500">Last Assignment</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $plan->studentFeeCards->sortByDesc('created_at')->first()?->created_at?->format('d M Y') ?? '—' }}
                            </p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($cards as $card)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $card->student->full_name ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $card->student->admission_number ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $card->student->currentEnrollment->roll_number ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $card->student->currentEnrollment?->section?->section_name ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                            ₹{{ number_format($card->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-gray-900">
                                            ₹{{ number_format($card->paid_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm text-gray-900">
                                            ₹{{ number_format($card->balance_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $statusColors = [
                                                    'paid' => 'bg-emerald-100 text-emerald-800',
                                                    'partial' => 'bg-yellow-100 text-yellow-800',
                                                    'overdue' => 'bg-red-100 text-red-800',
                                                    'active' => 'bg-sky-100 text-sky-800',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$card->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($card->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <p>No students assigned to this plan yet.</p>
                        <p class="mt-2">
                            <a href="{{ url('/admin/fees/plans/' . $plan->id . '/assign') }}" class="text-primary-600 hover:text-primary-700">
                                Assign the first student
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

