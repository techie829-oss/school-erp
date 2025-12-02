<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Cards PDF</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }
        @page {
            size: A4 portrait;
            margin: 0;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 15mm;
            page-break-after: always;
        }
        .page:last-child {
            page-break-after: auto;
        }
        .fee-card {
            border: 1px solid #d1d5db;
            background: white;
            padding: 15px;
            margin-bottom: 15px;
            page-break-inside: avoid;
        }
        .fee-card-header {
            background: #2563eb;
            color: white;
            padding: 10px 15px;
            border-radius: 5px 5px 0 0;
            font-weight: bold;
            margin: -15px -15px 15px -15px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th, td {
            padding: 6px;
            border: 1px solid #e5e7eb;
            text-align: left;
        }
        th {
            background: #f9fafb;
            font-weight: bold;
            font-size: 9px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        h2 {
            font-size: 14px;
            margin-bottom: 10px;
        }
        p {
            margin-bottom: 8px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    @foreach($students as $student)
        <div class="page">
            <div class="fee-card">
                <div class="fee-card-header">
                    {{ $tenant->institution_name ?? 'School Name' }} - Fee Card
                </div>
                <h2>{{ $student->full_name }}</h2>
                <p><strong>Admission Number:</strong> {{ $student->admission_number }}</p>
                <p><strong>Class:</strong> {{ $student->currentEnrollment?->schoolClass?->class_name ?? '-' }} / {{ $student->currentEnrollment?->section?->section_name ?? '-' }}</p>

                @if($student->feeCards->count() > 0)
                    @foreach($student->feeCards as $card)
                        <div style="margin-bottom: 15px;">
                            <h3 style="font-size: 12px; margin-bottom: 8px;">{{ $card->feePlan->name ?? 'Fee Plan' }} - {{ $card->academic_year }}</h3>
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

