<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Cards PDF Preview</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            .no-print {
                display: none !important;
            }
            .a4-page {
                page-break-after: always;
                box-shadow: none !important;
                margin: 0 !important;
            }
        }
        .no-print {
            position: sticky;
            top: 0;
            z-index: 50;
            background: white;
            border-bottom: 2px solid #2563eb;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
        }
        .print-button {
            background-color: #2563eb;
            color: white;
            padding: 8px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-left: 10px;
        }
        .a4-page {
            width: 210mm;
            min-height: 297mm;
            background: white;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            padding: 20px;
        }
        .fee-card {
            border: 1px solid #d1d5db;
            background: white;
            margin-bottom: 20px;
            padding: 15px;
        }
        .fee-card-header {
            background: #2563eb;
            color: white;
            padding: 10px 15px;
            border-radius: 5px 5px 0 0;
            font-weight: bold;
            margin: -15px -15px 15px -15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }
        th, td {
            padding: 8px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }
        th {
            background: #f9fafb;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div class="header-row">
            <h1 class="header-title">Bulk Export Fee Cards</h1>
            <div>
                <a href="{{ url('/admin/fees/cards/bulk-actions?' . http_build_query(request()->except(['student_ids']))) }}" style="background-color: #6b7280; color: white; padding: 8px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block;">
                    ← Back
                </a>
                <form id="downloadForm" action="{{ url('/admin/fees/cards/bulk-export') }}" method="POST" style="display: inline;">
                    @csrf
                    @foreach(request('student_ids', []) as $id)
                        <input type="hidden" name="student_ids[]" value="{{ $id }}">
                    @endforeach
                    <input type="hidden" name="cards_per_page" value="{{ request('cards_per_page', 1) }}">
                    <input type="hidden" name="show_principal_stamp" value="{{ request('show_principal_stamp', 0) }}">
                    <input type="hidden" name="show_accountant_sign" value="{{ request('show_accountant_sign', 0) }}">
                </form>
                <button onclick="window.print()" class="print-button">Print / Export PDF</button>
            </div>
        </div>
    </div>

    @foreach($students as $student)
        <div class="a4-page">
            <div class="fee-card">
                <div class="fee-card-header">
                    {{ $tenant->institution_name ?? 'School Name' }} - Fee Card
                </div>
                <h2 style="margin-bottom: 15px;">{{ $student->full_name }}</h2>
                <p style="margin-bottom: 10px;"><strong>Admission Number:</strong> {{ $student->admission_number }}</p>
                <p style="margin-bottom: 15px;"><strong>Class:</strong> {{ $student->currentEnrollment?->schoolClass?->class_name ?? '-' }} / {{ $student->currentEnrollment?->section?->section_name ?? '-' }}</p>

                @if($student->feeCards->count() > 0)
                    @foreach($student->feeCards as $card)
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin-bottom: 10px;">{{ $card->feePlan->name ?? 'Fee Plan' }} - {{ $card->academic_year }}</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Fee Component</th>
                                        <th class="text-right">Amount</th>
                                        <th class="text-right">Discount</th>
                                        <th class="text-right">Net Amount</th>
                                        <th class="text-right">Paid</th>
                                        <th class="text-right">Balance</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($card->feeItems as $item)
                                        <tr>
                                            <td>{{ $item->feeComponent->name ?? 'N/A' }}</td>
                                            <td class="text-right">₹{{ number_format($item->original_amount, 2) }}</td>
                                            <td class="text-right">₹{{ number_format($item->discount_amount, 2) }}</td>
                                            <td class="text-right">₹{{ number_format($item->net_amount, 2) }}</td>
                                            <td class="text-right">₹{{ number_format($item->paid_amount, 2) }}</td>
                                            <td class="text-right">₹{{ number_format($item->net_amount - $item->paid_amount, 2) }}</td>
                                            <td class="text-center">{{ ucfirst($item->status) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="background: #f9fafb; font-weight: bold;">
                                        <td colspan="3" class="text-right">Total:</td>
                                        <td class="text-right">₹{{ number_format($card->total_amount - $card->discount_amount, 2) }}</td>
                                        <td class="text-right">₹{{ number_format($card->paid_amount, 2) }}</td>
                                        <td class="text-right">₹{{ number_format($card->balance_amount, 2) }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endforeach
                @else
                    <p>No fee cards found for this student.</p>
                @endif
            </div>
        </div>
    @endforeach
</body>
</html>

