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
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 12px;
            color: #1f2937;
            padding: 30px;
            background: #fff;
        }
        .receipt-container {
            max-width: 700px;
            margin: 0 auto;
            border: 2px solid #059669;
            border-radius: 8px;
            overflow: hidden;
            background: white;
        }
        .receipt-header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }
        .receipt-header h1 {
            font-size: 26px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .receipt-header p {
            font-size: 13px;
            opacity: 0.95;
        }
        .receipt-number {
            background: white;
            color: #059669;
            padding: 12px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            border-bottom: 2px dashed #e5e7eb;
        }
        .receipt-body {
            padding: 30px;
        }
        .amount-box {
            text-align: center;
            margin: 30px 0;
            padding: 30px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 3px solid #059669;
            border-radius: 8px;
        }
        .amount-box .label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .amount-box .amount {
            font-size: 36px;
            font-weight: bold;
            color: #059669;
        }
        .info-section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e5e7eb;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .info-item {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            font-weight: 600;
            color: #6b7280;
            width: 140px;
            flex-shrink: 0;
        }
        .info-value {
            color: #1f2937;
            flex: 1;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .details-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
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
            margin-top: 25px;
            padding: 15px;
            background: #f9fafb;
            border-left: 4px solid #059669;
            border-radius: 4px;
        }
        .notes-box strong {
            color: #374151;
            display: block;
            margin-bottom: 5px;
        }
        .receipt-footer {
            background: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            background: #d1fae5;
            color: #065f46;
        }
        @media print {
            body {
                padding: 0;
            }
            .receipt-container {
                border: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>{{ $tenant->data['name'] ?? ($tenant->name ?? 'School ERP') }}</h1>
            <p>Payment Receipt</p>
        </div>
        
        <div class="receipt-number">
            Receipt No: {{ $payment->payment_number }}
        </div>

        <div class="receipt-body">
            <div class="info-section">
                <div class="info-grid">
                    <div>
                        <div class="section-title">Receipt Information</div>
                        <div class="info-item">
                            <span class="info-label">Receipt Number:</span>
                            <span class="info-value">{{ $payment->payment_number }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Payment Date:</span>
                            <span class="info-value">{{ $payment->payment_date->format('d M Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                <span class="status-badge">{{ ucfirst($payment->status) }}</span>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="section-title">Student Information</div>
                        <div class="info-item">
                            <span class="info-label">Name:</span>
                            <span class="info-value">{{ $payment->student->full_name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Admission No:</span>
                            <span class="info-value">{{ $payment->student->admission_number }}</span>
                        </div>
                        @if($payment->student->currentEnrollment && $payment->student->currentEnrollment->schoolClass)
                        <div class="info-item">
                            <span class="info-label">Class:</span>
                            <span class="info-value">{{ $payment->student->currentEnrollment->schoolClass->class_name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="amount-box">
                <div class="label">Amount Received</div>
                <div class="amount">₹{{ number_format($payment->amount, 2) }}</div>
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
                <strong>Notes:</strong>
                <div>{{ $payment->notes }}</div>
            </div>
            @endif
        </div>

        <div class="receipt-footer">
            <p>This is a computer-generated receipt. No signature required.</p>
            <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>
</html>
