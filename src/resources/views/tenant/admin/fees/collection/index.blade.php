@extends('tenant.layouts.admin')

@section('title', 'Fee Collection')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Fee Collection
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Collect and manage student fee payments
            </p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-4 bg-green-100 rounded-lg">
                        <i class="fas fa-rupee-sign text-3xl text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Collected Today</p>
                        <p class="text-3xl font-bold text-gray-800">₹{{ number_format($stats['total_collected_today'], 2) }}</p>
                    </div>
                </div>
            </div>

        <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-4 bg-red-100 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Total Pending</p>
                        <p class="text-3xl font-bold text-gray-800">₹{{ number_format($stats['total_pending'], 2) }}</p>
                    </div>
                </div>
            </div>

        <div class="bg-white shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-4 bg-orange-100 rounded-lg">
                        <i class="fas fa-users text-3xl text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-gray-500 text-sm">Students with Dues</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stats['students_with_dues'] }}</p>
                    </div>
                </div>
            </div>
        </div>

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-6">
            <form method="GET" action="{{ url('/admin/fees/collection') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Student</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                           placeholder="Admission No, Name...">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                    <select name="class_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Section</label>
                    <select name="section_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Sections</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->schoolClass->name }} - {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </div>
            </form>
        </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Students Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
            @if($students->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admission No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Fee</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($students as $student)
                            @php
                                $feeCard = $student->studentFeeCard;
                                $totalFee = $feeCard->total_amount ?? 0;
                                $paidAmount = $feeCard->paid_amount ?? 0;
                                $balance = $feeCard->balance_amount ?? 0;
                                $status = $feeCard->status ?? 'not_assigned';
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm">{{ $student->admission_number }}</span>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $student->schoolClass->name ?? 'N/A' }} - {{ $student->section->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-right font-semibold">
                                    ₹{{ number_format($totalFee, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right text-green-600 font-semibold">
                                    ₹{{ number_format($paidAmount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-right font-semibold">
                                    <span class="{{ $balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        ₹{{ number_format($balance, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($status == 'paid')
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                            Paid
                                        </span>
                                    @elseif($status == 'partial')
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                                            Partial
                                        </span>
                                    @elseif($status == 'overdue')
                                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">
                                            Overdue
                                        </span>
                                    @elseif($status == 'active')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">
                                            Not Assigned
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap text-sm font-medium">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ url('/admin/fees/collection/' . $student->id) }}"
                                           class="text-primary-600 hover:text-primary-900">
                                            View
                                        </a>
                                        @if($balance > 0)
                                            <span class="text-gray-300">|</span>
                                            <a href="{{ url('/admin/fees/collection/' . $student->id . '/collect') }}"
                                               class="text-green-600 hover:text-green-900">
                                                Collect
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50">
                    {{ $students->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No students found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

