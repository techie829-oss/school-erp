@extends('tenant.layouts.admin')

@section('title', 'Edit Hostel Fee')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><a href="{{ url('/admin/hostel/fees') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Fees</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Edit</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Hostel Fee</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $fee->name }}</p>
        </div>
    </div>

    <form action="{{ url('/admin/hostel/fees/' . $fee->id) }}" method="POST" class="max-w-2xl">
        @csrf
        @method('PUT')

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
                <div>
                    <label for="hostel_id" class="block text-sm font-medium text-gray-700">Hostel <span class="text-red-500">*</span></label>
                    <select name="hostel_id" id="hostel_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">Select Hostel</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}" {{ old('hostel_id', $fee->hostel_id) == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="fee_type" class="block text-sm font-medium text-gray-700">Fee Type <span class="text-red-500">*</span></label>
                    <input type="text" name="fee_type" id="fee_type" value="{{ old('fee_type', $fee->fee_type) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Fee Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $fee->name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Amount (₹) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount', $fee->amount) }}" step="0.01" min="0" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>

                <div>
                    <label for="frequency" class="block text-sm font-medium text-gray-700">Frequency <span class="text-red-500">*</span></label>
                    <select name="frequency" id="frequency" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="one_time" {{ old('frequency', $fee->frequency) == 'one_time' ? 'selected' : '' }}>One Time</option>
                        <option value="monthly" {{ old('frequency', $fee->frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('frequency', $fee->frequency) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="semester" {{ old('frequency', $fee->frequency) == 'semester' ? 'selected' : '' }}>Semester</option>
                        <option value="annual" {{ old('frequency', $fee->frequency) == 'annual' ? 'selected' : '' }}>Annual</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="active" {{ old('status', $fee->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $fee->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">{{ old('description', $fee->description) }}</textarea>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ url('/admin/hostel/fees') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Update Fee</button>
        </div>
    </form>
</div>
@endsection

