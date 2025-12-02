@extends('tenant.layouts.admin')

@section('title', 'Bulk Actions - Admit Cards')

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
                    <a href="{{ url('/admin/examinations/admit-cards') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Admit Cards</a>
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
                Bulk Actions - Admit Cards
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Select and manage multiple admit cards at once
                @if(request('exam_id'))
                    @php $exam = is_object($exams) && method_exists($exams, 'firstWhere') ? $exams->firstWhere('id', request('exam_id')) : null; @endphp
                    @if($exam)
                        for <strong>{{ $exam->exam_name }}</strong>
                    @endif
                @endif
                ({{ $admitCards->count() }} cards found)
            </p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4 space-x-3">
            <button onclick="openBulkExportModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export Selected
            </button>
            <button onclick="bulkDeleteSelected()" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete Selected
            </button>
            <a href="{{ url('/admin/examinations/admit-cards?' . http_build_query(request()->all())) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
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
    @if(request('exam_id') || request('class_id') || request('section_id') || request('search'))
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
                    @if(request('exam_id'))
                        @php $exam = is_object($exams) && method_exists($exams, 'firstWhere') ? $exams->firstWhere('id', request('exam_id')) : null; @endphp
                        Exam: {{ $exam ? $exam->exam_name : 'N/A' }}
                    @endif
                    @if(request('class_id'))
                        @php $class = is_object($classes) && method_exists($classes, 'firstWhere') ? $classes->firstWhere('id', request('class_id')) : null; @endphp
                        | Class: {{ $class ? $class->class_name : 'N/A' }}
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

    <!-- Admit Cards Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hall Ticket</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class/Section</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($admitCards as $admitCard)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="admit_card_ids[]" value="{{ $admitCard->id }}" class="admit-card-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $admitCard->hall_ticket_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $admitCard->student_name }}</div>
                            <div class="text-sm text-gray-500">{{ $admitCard->admission_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $admitCard->exam->exam_name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $admitCard->schoolClass->class_name ?? 'N/A' }}
                            @if($admitCard->section)
                                / {{ $admitCard->section->section_name }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $admitCard->generated_at ? $admitCard->generated_at->format('M d, Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ url('/admin/examinations/admit-cards/' . $admitCard->id . '/print') }}" target="_blank" class="text-primary-600 hover:text-primary-900">Print</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No admit cards found</h3>
                                <p class="mt-1 text-sm text-gray-500">Apply filters to see admit cards or generate new ones</p>
                                <div class="mt-6">
                                    <a href="{{ url('/admin/examinations/admit-cards') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                                        Go to Admit Cards
                                    </a>
                                </div>
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
            <span id="selectedCount">0</span> of <span id="totalCount">{{ $admitCards->count() }}</span> cards selected
        </p>
    </div>
</div>

<!-- Bulk Export Modal -->
<div id="bulkExportModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Preview & Export Admit Cards</h3>
                <button onclick="closeBulkExportModal()" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="bulkExportForm" action="{{ url('/admin/examinations/admit-cards/bulk-preview') }}" method="GET">
                @csrf

                <!-- Cards Per Page -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cards Per Page</label>
                    <div class="space-y-2">
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

                <!-- Display Options -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Display Options</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="show_exam_schedule" value="1" checked class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Show Exam Schedule</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="show_qr_code" value="1" checked class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Show QR Code</span>
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
                            <input type="checkbox" name="show_class_teacher_sign" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Class Teacher Signature</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="show_supervisor_sign" value="1" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-700">Exam Supervisor Signature</span>
                        </label>
                    </div>
                </div>

                <!-- Export Scope -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Export Scope</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="export_scope" value="selected" checked class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">Selected cards only</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="export_scope" value="filtered" class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">All filtered results ({{ $admitCards->count() }} cards)</span>
                        </label>
                    </div>
                </div>

                <!-- Hidden fields for filters -->
                <input type="hidden" name="exam_id" value="{{ request('exam_id') }}">
                <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                <input type="hidden" name="section_id" value="{{ request('section_id') }}">
                <input type="hidden" name="search" value="{{ request('search') }}">

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
    const checkboxes = document.querySelectorAll('.admit-card-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateSelectionInfo();
});

// Update selection count
function updateSelectionInfo() {
    const selectedCount = document.querySelectorAll('.admit-card-checkbox:checked').length;
    const totalCount = document.querySelectorAll('.admit-card-checkbox').length;
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
document.querySelectorAll('.admit-card-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        updateSelectionInfo();
        // Update select all checkbox
        const allChecked = document.querySelectorAll('.admit-card-checkbox:checked').length === document.querySelectorAll('.admit-card-checkbox').length;
        document.getElementById('selectAll').checked = allChecked && document.querySelectorAll('.admit-card-checkbox').length > 0;
    });
});

function openBulkExportModal() {
    const selectedCount = document.querySelectorAll('.admit-card-checkbox:checked').length;

    if (selectedCount === 0) {
        if (confirm('No cards selected. Export all filtered results?')) {
            document.querySelector('input[name="export_scope"][value="filtered"]').checked = true;
        } else {
            alert('Please select at least one admit card or choose to export all filtered results.');
            return;
        }
    }

    document.getElementById('bulkExportModal').classList.remove('hidden');
}

function closeBulkExportModal() {
    document.getElementById('bulkExportModal').classList.add('hidden');
}

// Update form before submit
document.getElementById('bulkExportForm')?.addEventListener('submit', function(e) {
    const exportScope = document.querySelector('input[name="export_scope"]:checked').value;

    if (exportScope === 'selected') {
        const selectedIds = Array.from(document.querySelectorAll('.admit-card-checkbox:checked'))
            .map(cb => cb.value);

        if (selectedIds.length === 0) {
            e.preventDefault();
            alert('Please select at least one admit card.');
            return;
        }

        // Add selected IDs to form
        selectedIds.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'admit_card_ids[]';
            input.value = id;
            this.appendChild(input);
        });
    }

    // Remove CSRF token for GET request
    const csrfInput = this.querySelector('input[name="_token"]');
    if (csrfInput) {
        csrfInput.remove();
    }
});

// Bulk delete selected admit cards
function bulkDeleteSelected() {
    const selectedIds = Array.from(document.querySelectorAll('.admit-card-checkbox:checked'))
        .map(cb => cb.value);

    if (selectedIds.length === 0) {
        alert('Please select at least one admit card to delete.');
        return;
    }

    if (!confirm(`Are you sure you want to delete ${selectedIds.length} admit card(s)? This action cannot be undone.`)) {
        return;
    }

    // Create a form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ url("/admin/examinations/admit-cards/bulk-delete") }}';

    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);

    selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'admit_card_ids[]';
        input.value = id;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

// Close modal on outside click
window.onclick = function(event) {
    const modal = document.getElementById('bulkExportModal');
    if (event.target == modal) {
        closeBulkExportModal();
    }
}
</script>
@endsection

