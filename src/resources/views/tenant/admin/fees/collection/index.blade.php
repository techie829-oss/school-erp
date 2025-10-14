@extends('tenant.layouts.admin')

@section('title', 'Fee Collection')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Fee Collection</h1>
            <p class="text-gray-600 mt-1">Collect fees from students</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-xl shadow-lg p-6">
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

            <div class="bg-white rounded-xl shadow-lg p-6">
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

            <div class="bg-white rounded-xl shadow-lg p-6">
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
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
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
                        <!-- Will be populated from controller -->
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Section</label>
                    <select name="section_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Sections</option>
                        <!-- Will be populated from controller -->
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
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <!-- Students Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($students->count() > 0)
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                        <tr>
                            <th class="px-6 py-4 text-left">Admission No</th>
                            <th class="px-6 py-4 text-left">Student Name</th>
                            <th class="px-6 py-4 text-left">Class</th>
                            <th class="px-6 py-4 text-right">Total Fee</th>
                            <th class="px-6 py-4 text-right">Paid</th>
                            <th class="px-6 py-4 text-right">Balance</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
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
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center gap-2">
                                        <a href="{{ url('/admin/fees/collection/' . $student->id) }}"
                                           class="px-4 py-2 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition text-sm">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        @if($balance > 0)
                                            <a href="{{ url('/admin/fees/collection/' . $student->id . '/collect') }}"
                                               class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm">
                                                <i class="fas fa-rupee-sign mr-1"></i>Collect
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
                <div class="text-center py-16">
                    <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-search text-5xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Students Found</h3>
                    <p class="text-gray-500">Try adjusting your filters</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

