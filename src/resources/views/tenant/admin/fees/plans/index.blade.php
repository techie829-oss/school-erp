@extends('tenant.layouts.admin')

@section('title', 'Fee Plans')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Fee Plans</h1>
                <p class="text-gray-600 mt-1">Manage class-wise fee structures</p>
            </div>
            <a href="{{ url('/admin/fees/plans/create') }}"
               class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition">
                <i class="fas fa-plus mr-2"></i>Create Fee Plan
            </a>
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

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <form method="GET" action="{{ url('/admin/fees/plans') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Academic Year Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
                    <select name="academic_year" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('academic_year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Class Filter -->
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

                <!-- Submit -->
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Plans Grid -->
        @if($plans->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition">
                        <!-- Header -->
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-bold">{{ $plan->name }}</h3>
                                @if($plan->is_active)
                                    <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full">Active</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-500 text-white text-xs rounded-full">Inactive</span>
                                @endif
                            </div>
                            <p class="text-blue-100 text-sm">{{ $plan->schoolClass->name ?? 'N/A' }}</p>
                        </div>

                        <!-- Details -->
                        <div class="p-6">
                            <div class="space-y-3 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Academic Year:</span>
                                    <span class="font-semibold">{{ $plan->academic_year }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Term:</span>
                                    <span class="font-semibold capitalize">{{ str_replace('_', ' ', $plan->term) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Effective:</span>
                                    <span class="font-semibold">{{ $plan->effective_from->format('d M Y') }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Total Amount:</span>
                                    <span class="font-bold text-lg text-blue-600">₹{{ number_format($plan->total_amount, 2) }}</span>
                                </div>
                            </div>

                            <!-- Components -->
                            <div class="border-t pt-4 mb-4">
                                <p class="text-sm font-semibold text-gray-700 mb-2">Components ({{ $plan->feePlanItems->count() }})</p>
                                <div class="space-y-1">
                                    @foreach($plan->feePlanItems->take(3) as $item)
                                        <div class="flex justify-between text-xs">
                                            <span class="text-gray-600">{{ $item->feeComponent->name }}</span>
                                            <span class="font-semibold">₹{{ number_format($item->amount, 2) }}</span>
                                        </div>
                                    @endforeach
                                    @if($plan->feePlanItems->count() > 3)
                                        <p class="text-xs text-gray-500 italic">+{{ $plan->feePlanItems->count() - 3 }} more</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <a href="{{ url('/admin/fees/plans/' . $plan->id) }}"
                                   class="flex-1 px-4 py-2 bg-blue-100 text-blue-600 text-center rounded hover:bg-blue-200 transition text-sm">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <a href="{{ url('/admin/fees/plans/' . $plan->id . '/edit') }}"
                                   class="flex-1 px-4 py-2 bg-green-100 text-green-600 text-center rounded hover:bg-green-200 transition text-sm">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <form action="{{ url('/admin/fees/plans/' . $plan->id) }}"
                                      method="POST"
                                      class="flex-1"
                                      onsubmit="return confirm('Delete this fee plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="w-full px-4 py-2 bg-red-100 text-red-600 rounded hover:bg-red-200 transition text-sm">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $plans->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-lg text-center py-16">
                <div class="inline-block p-6 bg-gray-100 rounded-full mb-4">
                    <i class="fas fa-file-invoice-dollar text-5xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Fee Plans Yet</h3>
                <p class="text-gray-500 mb-6">Create your first fee plan to get started</p>
                <a href="{{ url('/admin/fees/plans/create') }}"
                   class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:shadow-lg transform hover:scale-105 transition inline-block">
                    <i class="fas fa-plus mr-2"></i>Create First Plan
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

