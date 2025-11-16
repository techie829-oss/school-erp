@extends('tenant.layouts.admin')

@section('title', 'Assign Fee Plan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <nav class="flex items-center text-sm text-gray-500 space-x-2">
                <a href="{{ url('/admin/dashboard') }}" class="hover:text-primary-600">Dashboard</a>
                <span>/</span>
                <a href="{{ url('/admin/fees/plans') }}" class="hover:text-primary-600">Fee Plans</a>
                <span>/</span>
                <span class="text-gray-700 font-medium">Assign Students</span>
            </nav>
            <h1 class="mt-2 text-2xl font-bold text-gray-900">Assign Students to {{ $plan->name }}</h1>
            <p class="text-sm text-gray-500">
                Students must belong to <span class="font-semibold">{{ $plan->schoolClass->class_name }}</span>
                for academic year <span class="font-semibold">{{ $plan->academic_year }}</span>.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ url('/admin/fees/plans/' . $plan->id) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Back to Plan
            </a>
        </div>
    </div>

    @if(!empty($syncedCount) && $syncedCount > 0)
        <div class="rounded-md bg-blue-50 border border-blue-200 p-4 text-xs text-blue-800">
            <p>
                We detected and automatically synced <strong>{{ $syncedCount }}</strong> existing student fee card(s)
                to academic year <strong>{{ $plan->academic_year }}</strong> for their current class.
                Old/historical fee cards were not changed.
            </p>
        </div>
    @endif

    @php
        $totalAmount = $plan->feePlanItems->sum('amount');
        $assignedCount = count($assignedStudentIds);
        $assignableCount = $students->filter(function ($student) use ($assignedStudentIds) {
            return !in_array($student->id, $assignedStudentIds);
        })->count();
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6 space-y-4">
            <div>
                <p class="text-sm text-gray-500">Plan Name</p>
                <p class="text-lg font-semibold text-gray-900">{{ $plan->name }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Class</p>
                    <p class="font-medium text-gray-900">{{ $plan->schoolClass->class_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Term</p>
                    <p class="font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $plan->term) }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Academic Year</p>
                    <p class="font-medium text-gray-900">{{ $plan->academic_year }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Components</p>
                    <p class="font-medium text-gray-900">{{ $plan->feePlanItems->count() }}</p>
                </div>
            </div>
            <div class="pt-4 border-t border-gray-100">
                <p class="text-sm text-gray-500">Total Plan Amount</p>
                <p class="text-2xl font-bold text-primary-600">₹{{ number_format($totalAmount, 2) }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6 space-y-4">
            <h3 class="text-base font-semibold text-gray-900">Assignment Summary</h3>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div class="bg-primary-50 rounded-lg p-4">
                    <dt class="text-xs uppercase tracking-wide text-primary-600">Assigned</dt>
                    <dd class="mt-1 text-2xl font-bold text-primary-700">{{ $assignedCount }}</dd>
                </div>
                <div class="bg-emerald-50 rounded-lg p-4">
                    <dt class="text-xs uppercase tracking-wide text-emerald-600">Available</dt>
                    <dd class="mt-1 text-2xl font-bold text-emerald-700">{{ $assignableCount }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Effective From</dt>
                    <dd class="font-medium text-gray-900">{{ optional($plan->effective_from)->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Effective To</dt>
                    <dd class="font-medium text-gray-900">{{ optional($plan->effective_to)->format('d M Y') ?? '—' }}</dd>
                </div>
            </dl>
            <p class="text-xs text-gray-500 leading-relaxed">
                Students already assigned are highlighted and cannot be selected again.
            </p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-base font-semibold text-gray-900">How assignment works</h3>
            <ol class="mt-3 space-y-2 text-sm text-gray-600 list-decimal list-inside">
                <li>Select students currently enrolled in this class.</li>
                <li>The system will generate fee cards and component-wise fee items automatically.</li>
                <li>Students already assigned are skipped to avoid duplicates.</li>
            </ol>
            <div class="mt-4 rounded-lg bg-blue-50 border border-blue-100 p-3 text-xs text-blue-700">
                Tip: Use “Select All” to assign the plan to every student who isn’t already linked.
            </div>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ url('/admin/fees/plans/' . $plan->id . '/assign') }}">
            @csrf
            <div class="px-6 py-4 border-b border-gray-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Students in {{ $plan->schoolClass->class_name }}</h3>
                    <p class="text-sm text-gray-500">Only students with an active enrollment in this class are shown.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="select-all-students"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        Select All Available
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 rounded-md text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 disabled:opacity-50"
                        id="assign-selected-btn">
                        Assign Selected
                    </button>
                </div>
            </div>

            @if ($errors->any())
                <div class="px-6 pt-4">
                    <div class="rounded-md bg-red-50 border border-red-100 p-4 text-sm text-red-700">
                        <ul class="list-disc ml-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">
                                <span class="sr-only">Select</span>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($students as $student)
                            @php
                                $isAssigned = in_array($student->id, $assignedStudentIds);
                            @endphp
                            <tr class="{{ $isAssigned ? 'bg-green-50/50' : '' }}">
                                <td class="px-6 py-4">
                                    <input type="checkbox"
                                           name="student_ids[]"
                                           value="{{ $student->id }}"
                                           class="student-checkbox h-4 w-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500"
                                           {{ $isAssigned ? 'disabled' : '' }}>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $student->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $student->admission_number }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $student->currentEnrollment->roll_number ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $student->currentEnrollment?->section?->section_name ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $student->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $student->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($isAssigned)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Assigned
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Not Assigned
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No students found for this class and academic year.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectAllBtn = document.getElementById('select-all-students');
        const assignBtn = document.getElementById('assign-selected-btn');
        const checkboxes = Array.from(document.querySelectorAll('.student-checkbox:not([disabled])'));

        const updateAssignButtonState = () => {
            const anyChecked = checkboxes.some(checkbox => checkbox.checked);
            assignBtn.disabled = !anyChecked;
        };

        if (selectAllBtn) {
            selectAllBtn.addEventListener('click', () => {
                const shouldSelectAll = checkboxes.some(checkbox => !checkbox.checked);
                checkboxes.forEach(checkbox => {
                    checkbox.checked = shouldSelectAll;
                });
                updateAssignButtonState();
            });
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateAssignButtonState);
        });

        updateAssignButtonState();
    });
</script>
@endsection

