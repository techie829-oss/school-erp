<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt - {{ $payment->payment_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #111827;
            padding: 15px;
        }
        .receipt-container {
            border: 2px solid #059669;
            background: white;
            padding: 15px;
            max-width: 100%;
        }
        .receipt-header {
            border-bottom: 2px solid #059669;
            padding-bottom: 8px;
            margin-bottom: 12px;
            text-align: center;
        }
        .school-name {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }
        .receipt-title {
            font-size: 11px;
            font-weight: 600;
            color: #059669;
            margin-bottom: 6px;
        }
        .receipt-number {
            font-size: 12px;
            font-weight: bold;
            color: #1f2937;
        }
        .amount-box {
            text-align: center;
            margin: 15px 0;
            padding: 15px;
            background: #f0fdf4;
            border: 2px solid #059669;
            border-radius: 4px;
        }
        .amount-label {
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 6px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .amount-value {
            font-size: 24px;
            font-weight: bold;
            color: #059669;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 12px;
        }
        .info-section {
            font-size: 10px;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 6px;
            padding-bottom: 3px;
            border-bottom: 1px solid #d1d5db;
            text-transform: uppercase;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 10px;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
            width: 100px;
            flex-shrink: 0;
        }
        .info-value {
            color: #1f2937;
            flex: 1;
            text-align: right;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
            font-size: 10px;
        }
        .details-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e5e7eb;
        }
        .details-table td:first-child {
            font-weight: 600;
            color: #6b7280;
            width: 40%;
        }
        .details-table td:last-child {
            color: #1f2937;
        }
        .notes-box {
            margin-top: 12px;
            padding: 8px;
            background: #f9fafb;
            border-left: 3px solid #059669;
            font-size: 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            background: #d1fae5;
            color: #065f46;
        }
        .receipt-footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }
        @media print {
            body {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <div class="school-name">{{ $tenant->data['name'] ?? ($tenant->data['school_name'] ?? 'School ERP') }}</div>
            <div class="receipt-title">PAYMENT RECEIPT</div>
            <div class="receipt-number">Receipt No: {{ $payment->payment_number }}</div>
        </div>

        <div class="info-grid">
            <div class="info-section">
                <div class="section-title">Receipt Information</div>
                <div class="info-row">
                    <span class="info-label">Receipt Number:</span>
                    <span class="info-value">{{ $payment->payment_number }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Date:</span>
                    <span class="info-value">{{ $payment->payment_date->format('d M Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value">
                        <span class="status-badge">{{ ucfirst($payment->status) }}</span>
                    </span>
                </div>
            </div>
            <div class="info-section">
                <div class="section-title">Student Information</div>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">{{ $payment->student->full_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Admission No:</span>
                    <span class="info-value">{{ $payment->student->admission_number }}</span>
                </div>
                @if($payment->student->currentEnrollment && $payment->student->currentEnrollment->schoolClass)
                <div class="info-row">
                    <span class="info-label">Class:</span>
                    <span class="info-value">{{ $payment->student->currentEnrollment->schoolClass->class_name }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="amount-box">
            <div class="amount-label">Amount Received</div>
            <div class="amount-value">Rs. {{ number_format($payment->amount, 2) }}</div>
        </div>

        <div class="info-section">
            <div class="section-title">Payment Details</div>
            <table class="details-table">
                <tr>
                    <td>Payment Method:</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                </tr>
                @if($payment->payment_type)
                <tr>
                    <td>Payment Type:</td>
                    <td>{{ $payment->payment_type }}</td>
                </tr>
                @endif
                @if($payment->transaction_id)
                <tr>
                    <td>Transaction ID:</td>
                    <td>{{ $payment->transaction_id }}</td>
                </tr>
                @endif
                @if($payment->reference_number)
                <tr>
                    <td>Reference Number:</td>
                    <td>{{ $payment->reference_number }}</td>
                </tr>
                @endif
                @if($payment->bill)
                <tr>
                    <td>Bill Number:</td>
                    <td>{{ $payment->bill->bill_number }}</td>
                </tr>
                @endif
                @if($payment->collected_by && $payment->collector)
                <tr>
                    <td>Collected By:</td>
                    <td>{{ $payment->collector->name }}</td>
                </tr>
                @endif
            </table>
        </div>

        @if($payment->notes)
        <div class="notes-box">
            <strong>Notes:</strong> {{ $payment->notes }}
        </div>
        @endif

        <div class="receipt-footer">
            <p>This is a computer-generated receipt. No signature required.</p>
            <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>
</html>
