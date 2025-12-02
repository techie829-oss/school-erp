@extends('tenant.layouts.admin')

@section('title', 'Bulk Actions - Fee Cards')

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
                    <a href="{{ url('/admin/fees/cards') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Fee Cards</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Bulk Actions</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="md:flex md:items-center md:justify-between">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Bulk Actions - Fee Cards
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Select and manage multiple fee cards at once
                @if(request('class_id'))
                    @php $class = is_object($classes) && method_exists($classes, 'firstWhere') ? $classes->firstWhere('id', request('class_id')) : null; @endphp
                    @if($class)
                        for <strong>{{ $class->class_name }}</strong>
                    @endif
                @endif
                ({{ $students->count() }} students found)
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <button onclick="openBulkExportModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Selected
            </button>
            <a href="{{ url('/admin/fees/cards?' . http_build_query(request()->all())) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back to List
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="rounded-md bg-green-50 p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-md bg-red-50 p-4">
        <div class="flex">
            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Active Filters Display -->
    @if(request('class_id') || request('section_id') || request('search'))
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Active Filters:</strong>
                    @if(request('class_id'))
                        @php $class = is_object($classes) && method_exists($classes, 'firstWhere') ? $classes->firstWhere('id', request('class_id')) : null; @endphp
                        Class: {{ $class ? $class->class_name : 'N/A' }}
                    @endif
                    @if(request('section_id'))
                        | Section: {{ request('section_id') }}
                    @endif
                    @if(request('search'))
                        | Search: "{{ request('search') }}"
                    @endif
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Students Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class/Section</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee Cards</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Balance</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="student-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500" value="{{ $student->id }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $student->admission_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $student->currentEnrollment?->schoolClass?->class_name ?? '-' }}
                            @if($student->currentEnrollment?->section)
                                / {{ $student->currentEnrollment->section->section_name }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $student->feeCards->count() }} card(s)
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $student->feeCards->sum('balance_amount') > 0 ? 'text-red-600' : 'text-green-600' }}">
                            ₹{{ number_format($student->feeCards->sum('balance_amount'), 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ url('/admin/fees/cards/' . $student->id . '/print') }}" target="_blank" class="text-primary-600 hover:text-primary-900">Print</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No students found</h3>
                                <p class="mt-1 text-sm text-gray-500">Select a class to view fee cards</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Selection Info -->
    <div id="selectionInfo" class="bg-gray-50 border border-gray-200 rounded-lg p-4 hidden">
        <p class="text-sm text-gray-700">
            <span id="selectedCount">0</span> of <span id="totalCount">{{ $students->count() }}</span> students selected
        </p>
    </div>
</div>

<!-- Bulk Export Modal -->
<div id="bulkExportModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Preview & Export Fee Cards</h3>
                <button onclick="closeBulkExportModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="bulkExportForm" action="{{ url('/admin/fees/cards/bulk-preview') }}" method="GET">
                <!-- Cards Per Page -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cards Per Page</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="cards_per_page" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">1 card per page</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="cards_per_page" value="2" checked class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">2 cards per page</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="cards_per_page" value="4" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">4 cards per page</span>
                        </label>
                    </div>
                </div>

                <!-- Signature Options -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Signature & Stamp Options</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="show_principal_stamp" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Principal Stamp</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="show_accountant_sign" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Accountant Signature</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeBulkExportModal()" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                        Preview
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Select All functionality
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectionInfo();
});

// Update selection count
function updateSelectionInfo() {
    const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
    const totalCount = document.querySelectorAll('.student-checkbox').length;
    const selectionInfo = document.getElementById('selectionInfo');

    if (selectedCount > 0) {
        document.getElementById('selectedCount').textContent = selectedCount;
        document.getElementById('totalCount').textContent = totalCount;
        selectionInfo.classList.remove('hidden');
    } else {
        selectionInfo.classList.add('hidden');
    }
}

// Update on checkbox change
document.querySelectorAll('.student-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        updateSelectionInfo();
        // Update select all checkbox
        const allChecked = document.querySelectorAll('.student-checkbox:checked').length === document.querySelectorAll('.student-checkbox').length;
        document.getElementById('selectAll').checked = allChecked && document.querySelectorAll('.student-checkbox').length > 0;
    });
});

function openBulkExportModal() {
    const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;

    if (selectedCount === 0) {
        alert('Please select at least one student to export fee cards.');
        return;
    }

    document.getElementById('bulkExportModal').classList.remove('hidden');
}

function closeBulkExportModal() {
    document.getElementById('bulkExportModal').classList.add('hidden');
}

// Update form before submit
document.getElementById('bulkExportForm')?.addEventListener('submit', function(e) {
    const selectedIds = Array.from(document.querySelectorAll('.student-checkbox:checked'))
        .map(cb => cb.value);

    if (selectedIds.length === 0) {
        e.preventDefault();
        alert('Please select at least one student.');
        return;
    }

    // Add selected IDs to form
    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'student_ids[]';
        input.value = id;
        this.appendChild(input);
    });
});

// Close modal on outside click
window.onclick = function(event) {
    const modal = document.getElementById('bulkExportModal');
    if (event.target == modal) {
        closeBulkExportModal();
    }
}
</script>
@endsection

