<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transport Bill - {{ $bill->bill_number }}</title>
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
        .bill-container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #2563eb;
            border-radius: 8px;
            overflow: hidden;
            background: white;
        }
        .bill-header {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            color: white;
            padding: 25px 30px;
            text-align: center;
        }
        .bill-header h1 {
            font-size: 26px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .bill-header p {
            font-size: 13px;
            opacity: 0.95;
        }
        .bill-number {
            background: white;
            color: #2563eb;
            padding: 12px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            border-bottom: 2px dashed #e5e7eb;
        }
        .bill-body {
            padding: 30px;
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
            width: 130px;
            flex-shrink: 0;
        }
        .info-value {
            color: #1f2937;
            flex: 1;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 1px solid #e5e7eb;
        }
        .items-table thead {
            background: #f3f4f6;
        }
        .items-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            color: #374151;
            border-bottom: 2px solid #d1d5db;
        }
        .items-table td {
            padding: 10px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        .items-table tbody tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-section {
            margin-top: 25px;
            margin-left: auto;
            width: 350px;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 12px;
        }
        .totals-table .label {
            font-weight: 600;
            color: #6b7280;
            text-align: right;
        }
        .totals-table .amount {
            text-align: right;
            color: #1f2937;
            font-weight: 500;
        }
        .totals-table .total-row {
            background: #f3f4f6;
            font-weight: bold;
            font-size: 13px;
        }
        .totals-table .outstanding-row {
            background: #fef2f2;
            color: #dc2626;
            font-weight: bold;
            font-size: 13px;
        }
        .notes-box {
            margin-top: 25px;
            padding: 15px;
            background: #f9fafb;
            border-left: 4px solid #2563eb;
            border-radius: 4px;
        }
        .notes-box strong {
            color: #374151;
            display: block;
            margin-bottom: 5px;
        }
        .bill-footer {
            background: #f9fafb;
            padding: 20px 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
        }
        .status-text {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-paid {
            background: #d1fae5;
            color: #065f46;
        }
        .status-sent {
            background: #dbeafe;
            color: #1e40af;
        }
        .status-partial {
            background: #fef3c7;
            color: #92400e;
        }
        .status-overdue {
            background: #fee2e2;
            color: #991b1b;
        }
        @media print {
            body {
                padding: 0;
            }
            .bill-container {
                border: none;
                border-radius: 0;
            }
        }
    </style>
</head>
<body>
    <div class="bill-container">
        <div class="bill-header">
            <h1>{{ $tenant->data['name'] ?? ($tenant->name ?? 'School ERP') }}</h1>
            <p>Transport Bill</p>
        </div>

        <div class="bill-number">
            Bill No: {{ $bill->bill_number }}
        </div>

        <div class="bill-body">
            <div class="info-section">
                <div class="info-grid">
                    <div>
                        <div class="section-title">Bill Information</div>
                        <div class="info-item">
                            <span class="info-label">Bill Number:</span>
                            <span class="info-value">{{ $bill->bill_number }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Bill Date:</span>
                            <span class="info-value">{{ $bill->bill_date->format('d M Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Due Date:</span>
                            <span class="info-value">{{ $bill->due_date->format('d M Y') }}</span>
                        </div>
                        @if($bill->academic_year)
                        <div class="info-item">
                            <span class="info-label">Academic Year:</span>
                            <span class="info-value">{{ $bill->academic_year }}</span>
                        </div>
                        @endif
                        @if($bill->term)
                        <div class="info-item">
                            <span class="info-label">Term:</span>
                            <span class="info-value">{{ $bill->term }}</span>
                        </div>
                        @endif
                        <div class="info-item">
                            <span class="info-label">Status:</span>
                            <span class="info-value">
                                <span class="status-text status-{{ $bill->status }}">
                                    {{ ucfirst($bill->status) }}
                                </span>
                            </span>
                        </div>
                    </div>
                    <div>
                        <div class="section-title">Student Information</div>
                        <div class="info-item">
                            <span class="info-label">Name:</span>
                            <span class="info-value">{{ $bill->student->full_name }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Admission No:</span>
                            <span class="info-value">{{ $bill->student->admission_number }}</span>
                        </div>
                        @if($bill->student->currentEnrollment && $bill->student->currentEnrollment->schoolClass)
                        <div class="info-item">
                            <span class="info-label">Class:</span>
                            <span class="info-value">{{ $bill->student->currentEnrollment->schoolClass->class_name }}</span>
                        </div>
                        @endif
                        @if($bill->assignment && $bill->assignment->route)
                        <div class="info-item">
                            <span class="info-label">Route:</span>
                            <span class="info-value">{{ $bill->assignment->route->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <table class="items-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-center" style="width: 80px;">Qty</th>
                        <th class="text-right" style="width: 100px;">Unit Price</th>
                        <th class="text-right" style="width: 100px;">Discount</th>
                        <th class="text-right" style="width: 120px;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bill->items as $item)
                    <tr>
                        <td>{{ $item->description }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">₹{{ number_format($item->discount, 2) }}</td>
                        <td class="text-right">₹{{ number_format($item->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="totals-section">
                <table class="totals-table">
                    <tr>
                        <td class="label">Subtotal:</td>
                        <td class="amount">₹{{ number_format($bill->total_amount, 2) }}</td>
                    </tr>
                    @if($bill->discount_amount > 0)
                    <tr>
                        <td class="label">Discount:</td>
                        <td class="amount">-₹{{ number_format($bill->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($bill->tax_amount > 0)
                    <tr>
                        <td class="label">Tax:</td>
                        <td class="amount">₹{{ number_format($bill->tax_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td class="label">Total Amount:</td>
                        <td class="amount">₹{{ number_format($bill->net_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Paid Amount:</td>
                        <td class="amount">₹{{ number_format($bill->paid_amount, 2) }}</td>
                    </tr>
                    <tr class="outstanding-row">
                        <td class="label">Outstanding:</td>
                        <td class="amount">₹{{ number_format($bill->outstanding_amount, 2) }}</td>
                    </tr>
                </table>
            </div>

            @if($bill->notes)
            <div class="notes-box">
                <strong>Notes:</strong>
                <div>{{ $bill->notes }}</div>
            </div>
            @endif
        </div>

        <div class="bill-footer">
            <p>This is a computer-generated bill. No signature required.</p>
            <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>
</html>
