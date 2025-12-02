<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt - {{ $payment->payment_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .no-print {
            position: sticky;
            top: 0;
            z-index: 50;
            background: white;
            border-bottom: 2px solid #059669;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: -20px -20px 20px -20px;
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
            background-color: #059669;
            color: white;
            padding: 8px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .print-button:hover {
            background-color: #047857;
        }

        .download-button {
            background-color: #10b981;
            color: white;
            padding: 8px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .download-button:hover {
            background-color: #059669;
        }

        .back-button {
            background-color: #6b7280;
            color: white;
            padding: 8px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .back-button:hover {
            background-color: #4b5563;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Header */
        .header {
            text-align: center;
            border-bottom: 3px solid #059669;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #047857;
            margin-bottom: 5px;
        }

        .receipt-title {
            font-size: 16px;
            color: #059669;
            font-weight: 600;
        }

        /* Receipt Info Section */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
            font-size: 13px;
        }

        .info-box {
            background: #f0fdf4;
            padding: 12px;
            border-left: 4px solid #059669;
        }

        .info-label {
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .info-value {
            color: #1e293b;
            font-weight: 600;
            font-size: 14px;
        }

        /* Student Info Section */
        .student-section {
            background: #ecfdf5;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .student-section h3 {
            color: #059669;
            font-size: 13px;
            margin-bottom: 10px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .student-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .student-item {
            color: #334155;
        }

        .student-label {
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .student-value {
            color: #1e293b;
            font-weight: 600;
        }

        /* Amount Box */
        .amount-box {
            text-align: center;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 3px solid #059669;
            border-radius: 8px;
            padding: 30px;
            margin: 25px 0;
        }

        .amount-label {
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .amount-value {
            font-size: 36px;
            font-weight: bold;
            color: #059669;
        }

        /* Payment Details Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
        }

        thead {
            background: #059669;
            color: white;
        }

        th {
            padding: 10px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }

        tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .details-table td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .details-table td:first-child {
            font-weight: 600;
            color: #64748b;
            width: 40%;
        }

        .details-table td:last-child {
            color: #1e293b;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            background: #22c55e;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 11px;
        }

        .notes {
            background: #fef3c7;
            border-left: 4px solid #eab308;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            font-size: 12px;
            color: #854d0e;
        }

        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .container {
                box-shadow: none;
                max-width: 100%;
                padding: 20px;
                margin: 0;
            }
        }

        @page {
            size: A4;
            margin: 10mm;
        }
    </style>
</head>
<body>
    <!-- Control Panel -->
    <div class="no-print">
        <div class="header-row">
            <h1 class="header-title">Payment Receipt Preview</h1>
            <div class="button-group">
                <a href="{{ url('/admin/transport/payments/' . $payment->id) }}" class="back-button">
                    ← Back
                </a>
                <a href="{{ url('/admin/transport/payments/' . $payment->id . '/receipt?download=pdf') }}" class="download-button">
                    Download PDF
                </a>
                <button onclick="window.print()" class="print-button">
                    Print / Export PDF
                </button>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="school-name">{{ $tenant->data['name'] ?? ($tenant->data['school_name'] ?? 'School ERP') }}</div>
            <div class="receipt-title">PAYMENT RECEIPT</div>
        </div>

        <!-- Receipt Information -->
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">Receipt Number</div>
                <div class="info-value">{{ $payment->payment_number }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Payment Date</div>
                <div class="info-value">{{ $payment->payment_date->format('d M Y') }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Status</div>
                <div class="info-value">
                    <span class="status-badge">{{ strtoupper($payment->status) }}</span>
                </div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="student-section">
            <h3>Student Information</h3>
            <div class="student-grid">
                <div class="student-item">
                    <div class="student-label">Student Name</div>
                    <div class="student-value">{{ $payment->student->full_name }}</div>
                </div>
                <div class="student-item">
                    <div class="student-label">Admission No.</div>
                    <div class="student-value">{{ $payment->student->admission_number }}</div>
                </div>
                @if($payment->student->currentEnrollment && $payment->student->currentEnrollment->schoolClass)
                <div class="student-item">
                    <div class="student-label">Class</div>
                    <div class="student-value">{{ $payment->student->currentEnrollment->schoolClass->class_name }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Amount Box -->
        <div class="amount-box">
            <div class="amount-label">Amount Received</div>
            <div class="amount-value">Rs. {{ number_format($payment->amount, 2) }}</div>
        </div>

        <!-- Payment Details -->
        <table class="details-table">
            <tr>
                <td>Payment Method</td>
                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
            </tr>
            @if($payment->payment_type)
            <tr>
                <td>Payment Type</td>
                <td>{{ $payment->payment_type }}</td>
            </tr>
            @endif
            @if($payment->transaction_id)
            <tr>
                <td>Transaction ID</td>
                <td>{{ $payment->transaction_id }}</td>
            </tr>
            @endif
            @if($payment->reference_number)
            <tr>
                <td>Reference Number</td>
                <td>{{ $payment->reference_number }}</td>
            </tr>
            @endif
            @if($payment->bill)
            <tr>
                <td>Bill Number</td>
                <td>{{ $payment->bill->bill_number }}</td>
            </tr>
            @endif
            @if($payment->collected_by && $payment->collector)
            <tr>
                <td>Collected By</td>
                <td>{{ $payment->collector->name }}</td>
            </tr>
            @endif
        </table>

        <!-- Notes -->
        @if($payment->notes)
        <div class="notes">
            <strong>Note:</strong> {{ $payment->notes }}
        </div>
        @else
        <div class="notes">
            <strong>Note:</strong> This is a computer-generated receipt. No signature required.
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            Generated on {{ now()->format('d M Y, h:i A') }}
        </div>
    </div>
</body>
</html>
