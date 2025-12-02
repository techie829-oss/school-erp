@extends('tenant.layouts.admin')

@section('title', 'Hostel Allocations')

@section('content')
<div class="space-y-6">
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li><a href="{{ url('/admin/dashboard') }}" class="text-sm font-medium text-gray-700 hover:text-primary-600">Dashboard</a></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Hostel</span></li>
            <li><span class="text-gray-500">/</span></li>
            <li><span class="text-sm font-medium text-gray-500">Allocations</span></li>
        </ol>
    </nav>

    <div class="md:flex md:items-center md:justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Hostel Allocations</h2>
            <p class="mt-1 text-sm text-gray-500">Manage student hostel allocations</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ url('/admin/hostel/allocations/create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Allocate Student
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-md bg-red-50 p-4">
        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white shadow rounded-lg p-4">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="hostel_id" class="block text-sm font-medium text-gray-700">Hostel</label>
                    <select name="hostel_id" id="hostel_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="">All Hostels</option>
                        @foreach($hostels as $hostel)
                            <option value="{{ $hostel->id }}" {{ request('hostel_id') == $hostel->id ? 'selected' : '' }}>{{ $hostel->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                        <option value="active" {{ request('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="released" {{ request('status') == 'released' ? 'selected' : '' }}>Released</option>
                        <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-3">
                <a href="{{ url('/admin/hostel/allocations') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Clear</a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">Apply Filters</button>
            </div>
        </form>
    </div>

    <!-- Allocations Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hostel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Bed</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Allocation Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Release Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($allocations as $allocation)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $allocation->student->full_name }}</div>
                            <div class="text-xs text-gray-500">{{ $allocation->student->admission_number }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $allocation->hostel->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $allocation->room->room_number }}</td>
                        <td class="px-6 py-4 text-sm text-center text-gray-900">{{ $allocation->bed_number ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $allocation->allocation_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $allocation->release_date ? $allocation->release_date->format('d M Y') : '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $allocation->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($allocation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            @if($allocation->status === 'active')
                            <button onclick="showReleaseModal({{ $allocation->id }})" class="text-red-600 hover:text-red-900">Release</button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <p class="text-sm text-gray-500">No allocations found. <a href="{{ url('/admin/hostel/allocations/create') }}" class="text-primary-600 hover:text-primary-900">Create one</a></p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($allocations->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $allocations->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Release Modal -->
<div id="releaseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Release Student</h3>
            <form id="releaseForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="release_date" class="block text-sm font-medium text-gray-700">Release Date <span class="text-red-500">*</span></label>
                    <input type="date" name="release_date" id="release_date" value="{{ now()->format('Y-m-d') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
                </div>
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea name="notes" id="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeReleaseModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700">Release</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showReleaseModal(allocationId) {
    document.getElementById('releaseForm').action = `/admin/hostel/allocations/${allocationId}/release`;
    document.getElementById('releaseModal').classList.remove('hidden');
}

function closeReleaseModal() {
    document.getElementById('releaseModal').classList.add('hidden');
}
</script>
@endsection

