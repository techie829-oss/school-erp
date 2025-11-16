<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $payment->payment_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            padding: 30px;
            font-size: 13px;
        }
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #2563eb;
            border-radius: 10px;
            overflow: hidden;
        }
        .receipt-header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .receipt-header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }
        .receipt-header p {
            font-size: 12px;
            opacity: 0.9;
        }
        .receipt-number {
            background: white;
            color: #2563eb;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            border-bottom: 2px dashed #e5e7eb;
        }
        .receipt-body {
            padding: 30px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item {
            display: flex;
        }
        .info-label {
            font-weight: bold;
            color: #6b7280;
            width: 140px;
        }
        .info-value {
            color: #1f2937;
            flex: 1;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background: #f9fafb;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #e5e7eb;
            font-size: 12px;
        }
        td {
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
        }
        .text-right {
            text-align: right;
        }
        .amount-box {
            background: #f0f9ff;
            border: 2px solid #2563eb;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        .amount-box .amount-label {
            font-size: 12px;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .amount-box .amount-value {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
        }
        .amount-words {
            margin-top: 10px;
            font-style: italic;
            color: #374151;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }
        .signature-box {
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #374151;
            width: 200px;
            margin: 40px auto 10px;
        }
        .stamp-box {
            border: 2px dashed #d1d5db;
            width: 150px;
            height: 150px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9ca3af;
            font-size: 11px;
            text-align: center;
            margin: 20px auto;
        }
        .receipt-footer {
            background: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .no-print {
            text-align: center;
            margin-bottom: 20px;
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
    <!-- Print Button -->
    <div class="no-print">
        <button onclick="window.print()" style="padding: 12px 24px; background: #2563eb; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px;">
            Print Receipt
        </button>
        <a href="{{ url('/admin/fees/receipts/' . $payment->id . '/download') }}" style="margin-left: 10px; padding: 12px 24px; background: #059669; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px;">
            Download PDF
        </a>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <h1>PAYMENT RECEIPT</h1>
            <p>{{ $tenant->institution_name ?? 'School Name' }}</p>
            <p>{{ $tenant->address ?? '' }}</p>
        </div>

        <!-- Receipt Number -->
        <div class="receipt-number">
            Receipt No: {{ $payment->payment_number }}
        </div>

        <!-- Body -->
        <div class="receipt-body">
            <!-- Student Details -->
            <div class="section">
                <div class="section-title">Student Details</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Student Name:</div>
                        <div class="info-value">{{ $payment->student->full_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Admission No:</div>
                        <div class="info-value">{{ $payment->student->admission_number }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Class:</div>
                        <div class="info-value">
                            {{ $payment->student->currentEnrollment?->schoolClass?->class_name ?? '-' }}
                            {{ $payment->student->currentEnrollment?->section?->section_name ? ' - ' . $payment->student->currentEnrollment->section->section_name : '' }}
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Father's Name:</div>
                        <div class="info-value">{{ $payment->student->father_name ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="section">
                <div class="section-title">Payment Details</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Payment Date:</div>
                        <div class="info-value">{{ $payment->payment_date->format('d M Y') }}</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Payment Method:</div>
                        <div class="info-value">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</div>
                    </div>
                    @if($payment->reference_number)
                        <div class="info-item">
                            <div class="info-label">Reference No:</div>
                            <div class="info-value">{{ $payment->reference_number }}</div>
                        </div>
                    @endif
                    @if($payment->transaction_id)
                        <div class="info-item">
                            <div class="info-label">Transaction ID:</div>
                            <div class="info-value">{{ $payment->transaction_id }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Fee Breakdown -->
            @if($payment->invoice && $payment->invoice->items->count() > 0)
                <div class="section">
                    <div class="section-title">Fee Breakdown</div>
                    <table>
                        <thead>
                            <tr>
                                <th>Fee Component</th>
                                <th class="text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payment->invoice->items as $item)
                                <tr>
                                    <td>{{ $item->feeComponent->name ?? $item->description }}</td>
                                    <td class="text-right">₹{{ number_format($item->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Amount -->
            <div class="amount-box">
                <div class="amount-label">Amount Paid</div>
                <div class="amount-value">₹{{ number_format($payment->amount, 2) }}</div>
                <div class="amount-words">
                    In Words: {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($payment->amount)) }} Rupees Only
                </div>
            </div>

            <!-- Remarks -->
            @if($payment->notes)
                <div class="section">
                    <div class="section-title">Remarks</div>
                    <p>{{ $payment->notes }}</p>
                </div>
            @endif

            <!-- Footer -->
            <div class="footer">
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div>Received By</div>
                    </div>
                    <div class="signature-box">
                        <div class="stamp-box">
                            SCHOOL<br>STAMP
                        </div>
                    </div>
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div>Authorized Signature</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Footer -->
        <div class="receipt-footer">
            <p><strong>Thank you for your payment!</strong></p>
            <p>This is a computer-generated receipt and does not require a physical signature.</p>
            <p>For any queries, please contact the school office.</p>
            <p style="margin-top: 10px; font-size: 10px;">Generated on: {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>
</html>

