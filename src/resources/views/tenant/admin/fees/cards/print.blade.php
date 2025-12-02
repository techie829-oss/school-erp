<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Card - {{ $student->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .header p {
            color: #6b7280;
            font-size: 11px;
        }
        .student-info {
            background: #f3f4f6;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 5px;
        }
        .student-info .label {
            font-weight: bold;
            color: #374151;
            width: 150px;
        }
        .fee-card {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .fee-card-header {
            background: #2563eb;
            color: white;
            padding: 10px 15px;
            border-radius: 5px 5px 0 0;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background: #f9fafb;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #e5e7eb;
            font-size: 11px;
        }
        td {
            padding: 8px 10px;
            border: 1px solid #e5e7eb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            background: #f9fafb;
            font-weight: bold;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }
        .badge-paid {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-partial {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-unpaid {
            background: #fee2e2;
            color: #991b1b;
        }
        .badge-waived {
            background: #e5e7eb;
            color: #374151;
        }
        .summary-boxes {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        .summary-box {
            flex: 1;
            border: 2px solid #e5e7eb;
            padding: 15px;
            margin: 0 5px;
            text-align: center;
            border-radius: 5px;
        }
        .summary-box .label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .summary-box .value {
            font-size: 18px;
            font-weight: bold;
        }
        .value.paid {
            color: #059669;
        }
        .value.due {
            color: #dc2626;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button (hidden when printing) -->
    <div class="no-print" style="margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="font-size: 24px; color: #111827;">Print Fee Card</h1>
            <button onclick="updatePrint()" class="print-button" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer;">
                Update & Print
            </button>
        </div>
        <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Signature & Stamp Options</label>
                    <label style="display: flex; align-items: center; margin-bottom: 5px;">
                        <input type="checkbox" id="showPrincipalStamp" {{ $showPrincipalStamp ?? false ? 'checked' : '' }} style="margin-right: 8px;">
                        <span>Principal Stamp</span>
                    </label>
                    <label style="display: flex; align-items: center;">
                        <input type="checkbox" id="showAccountantSign" {{ $showAccountantSign ?? false ? 'checked' : '' }} style="margin-right: 8px;">
                        <span>Accountant Signature</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <div class="header">
        <h1>{{ $tenant->institution_name ?? 'School Name' }}</h1>
        <p>Student Fee Card</p>
        <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <!-- Student Information -->
    <div class="student-info">
        <table>
            <tr>
                <td class="label">Student Name:</td>
                <td>{{ $student->full_name }}</td>
                <td class="label">Admission Number:</td>
                <td>{{ $student->admission_number }}</td>
            </tr>
            <tr>
                <td class="label">Class:</td>
                <td>{{ $student->currentEnrollment?->schoolClass?->class_name ?? '-' }}</td>
                <td class="label">Section:</td>
                <td>{{ $student->currentEnrollment?->section?->section_name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Father's Name:</td>
                <td>{{ $student->father_name ?? '-' }}</td>
                <td class="label">Contact:</td>
                <td>{{ $student->father_phone ?? $student->guardian_phone ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Overall Summary -->
    @if($feeCards->count() > 0)
        @php
            $totalAmount = $feeCards->sum('total_amount');
            $totalDiscount = $feeCards->sum('discount_amount');
            $totalPaid = $feeCards->sum('paid_amount');
            $totalDue = $feeCards->sum('balance_amount');
        @endphp

        <div class="summary-boxes">
            <div class="summary-box">
                <div class="label">Total Amount</div>
                <div class="value">₹{{ number_format($totalAmount, 2) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Discount</div>
                <div class="value" style="color: #059669;">₹{{ number_format($totalDiscount, 2) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Net Amount</div>
                <div class="value">₹{{ number_format($totalAmount - $totalDiscount, 2) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Amount Paid</div>
                <div class="value paid">₹{{ number_format($totalPaid, 2) }}</div>
            </div>
            <div class="summary-box">
                <div class="label">Balance Due</div>
                <div class="value due">₹{{ number_format($totalDue, 2) }}</div>
            </div>
        </div>

        <!-- Fee Cards Details -->
        @foreach($feeCards as $card)
            <div class="fee-card">
                <div class="fee-card-header">
                    {{ $card->feePlan->name }} - {{ $card->academic_year }}
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Fee Component</th>
                            <th class="text-right">Amount</th>
                            <th class="text-right">Discount</th>
                            <th class="text-right">Net Amount</th>
                            <th class="text-right">Paid</th>
                            <th class="text-right">Balance</th>
                            <th class="text-center">Due Date</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($card->feeItems as $item)
                            <tr>
                                <td>
                                    {{ $item->feeComponent->name }}
                                    @if($item->discount_reason)
                                        <br><small style="color: #6b7280;">{{ $item->discount_reason }}</small>
                                    @endif
                                </td>
                                <td class="text-right">₹{{ number_format($item->original_amount, 2) }}</td>
                                <td class="text-right">
                                    @if($item->discount_amount > 0)
                                        ₹{{ number_format($item->discount_amount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right">₹{{ number_format($item->net_amount, 2) }}</td>
                                <td class="text-right">₹{{ number_format($item->paid_amount, 2) }}</td>
                                <td class="text-right">₹{{ number_format($item->net_amount - $item->paid_amount, 2) }}</td>
                                <td class="text-center">{{ $item->due_date ? $item->due_date->format('d M Y') : '-' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="3" class="text-right">Total:</td>
                            <td class="text-right">₹{{ number_format($card->total_amount - $card->discount_amount, 2) }}</td>
                            <td class="text-right">₹{{ number_format($card->paid_amount, 2) }}</td>
                            <td class="text-right">₹{{ number_format($card->balance_amount, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endforeach
    @else
        <p style="text-align: center; padding: 40px; color: #6b7280;">No fee cards found for this student.</p>
    @endif

    <!-- Footer -->
    <div class="footer">
        @if(($showPrincipalStamp ?? false) || ($showAccountantSign ?? false))
        <div style="margin-top: 40px; display: flex; justify-content: space-around; padding-top: 30px; border-top: 1px solid #e5e7eb;">
            @if($showPrincipalStamp ?? false)
            <div style="text-align: center; width: 200px;">
                <div style="border: 2px dashed #9ca3af; padding: 20px; min-height: 80px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; font-size: 12px; color: #6b7280;">
                    Principal Stamp
                </div>
                <div style="border-top: 1px solid #111827; padding-top: 5px; font-weight: bold;">Principal</div>
            </div>
            @endif
            @if($showAccountantSign ?? false)
            <div style="text-align: center; width: 200px;">
                <div style="border-top: 1px solid #111827; margin-top: 80px; padding-top: 5px; font-weight: bold;">Accountant</div>
            </div>
            @endif
        </div>
        @else
        <p>This is a computer-generated document. No signature is required.</p>
        <p>For any queries, please contact the school office.</p>
        @endif
    </div>

    <script>
        function updatePrint() {
            const url = new URL(window.location.href);
            url.searchParams.set('show_principal_stamp', document.getElementById('showPrincipalStamp').checked ? '1' : '0');
            url.searchParams.set('show_accountant_sign', document.getElementById('showAccountantSign').checked ? '1' : '0');
            window.location.href = url.toString();
        }

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('show_principal_stamp') || urlParams.has('show_accountant_sign')) {
                window.print();
            }
        };
    </script>
</body>
</html>

