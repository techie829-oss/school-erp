<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transport Bill - {{ $bill->bill_number }}</title>
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
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 5px;
        }

        .bill-title {
            font-size: 16px;
            color: #2563eb;
            font-weight: 600;
        }

        /* Bill Info Section */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
            font-size: 13px;
        }

        .info-box {
            background: #f0f9ff;
            padding: 12px;
            border-left: 4px solid #2563eb;
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
            background: #eff6ff;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .student-section h3 {
            color: #2563eb;
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

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 13px;
        }

        thead {
            background: #2563eb;
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

        tbody tr:hover {
            background: #f0f9ff;
        }

        .text-right {
            text-align: right;
        }

        .amount {
            font-weight: 600;
            color: #1e293b;
        }

        /* Summary Section */
        .summary {
            display: grid;
            grid-template-columns: 1fr 250px;
            gap: 30px;
            margin-bottom: 20px;
        }

        .payment-status {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 15px;
            border-radius: 4px;
        }

        .status-label {
            color: #64748b;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .status-badge {
            display: inline-block;
            background: #22c55e;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.partial {
            background: #f59e0b;
        }

        .status-badge.sent {
            background: #3b82f6;
        }

        .status-badge.overdue {
            background: #ef4444;
        }

        .totals-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            overflow: hidden;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }

        .total-row.highlight {
            background: #2563eb;
            color: white;
            font-weight: 600;
            font-size: 14px;
            border: none;
        }

        .total-label {
            color: #64748b;
        }

        .total-amount {
            font-weight: 600;
            color: #1e293b;
        }

        .total-row.highlight .total-amount {
            color: white;
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
            .container {
                box-shadow: none;
                max-width: 100%;
                padding: 20px;
            }
        }

        @page {
            size: A4;
            margin: 10mm;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="school-name">{{ $tenant->data['name'] ?? ($tenant->data['school_name'] ?? 'School ERP') }}</div>
            <div class="bill-title">TRANSPORT BILL</div>
        </div>

        <!-- Bill Information -->
        <div class="info-grid">
            <div class="info-box">
                <div class="info-label">Bill Number</div>
                <div class="info-value">{{ $bill->bill_number }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Bill Date</div>
                <div class="info-value">{{ $bill->bill_date->format('d M Y') }}</div>
            </div>
            <div class="info-box">
                <div class="info-label">Due Date</div>
                <div class="info-value">{{ $bill->due_date->format('d M Y') }}</div>
            </div>
            @if($bill->academic_year)
            <div class="info-box">
                <div class="info-label">Academic Year</div>
                <div class="info-value">{{ $bill->academic_year }}</div>
            </div>
            @endif
            @if($bill->term)
            <div class="info-box">
                <div class="info-label">Term</div>
                <div class="info-value">{{ $bill->term }}</div>
            </div>
            @endif
        </div>

        <!-- Student Information -->
        <div class="student-section">
            <h3>Student Information</h3>
            <div class="student-grid">
                <div class="student-item">
                    <div class="student-label">Student Name</div>
                    <div class="student-value">{{ $bill->student->full_name }}</div>
                </div>
                <div class="student-item">
                    <div class="student-label">Admission No.</div>
                    <div class="student-value">{{ $bill->student->admission_number }}</div>
                </div>
                @if($bill->student->currentEnrollment && $bill->student->currentEnrollment->schoolClass)
                <div class="student-item">
                    <div class="student-label">Class</div>
                    <div class="student-value">{{ $bill->student->currentEnrollment->schoolClass->class_name }}</div>
                </div>
                @endif
                @if($bill->assignment && $bill->assignment->route)
                <div class="student-item">
                    <div class="student-label">Route</div>
                    <div class="student-value">{{ $bill->assignment->route->name }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Bill Items Table -->
        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Discount</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">1</td>
                    <td class="text-right amount">Rs. {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right amount">Rs. {{ number_format($item->discount, 2) }}</td>
                    <td class="text-right amount">Rs. {{ number_format($item->amount, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary Section -->
        <div class="summary">
            <div class="payment-status">
                <div class="status-label">Payment Status</div>
                <div class="status-badge {{ $bill->status }}">{{ strtoupper($bill->status) }}</div>
            </div>
            <div class="totals-box">
                <div class="total-row">
                    <span class="total-label">Subtotal</span>
                    <span class="total-amount">Rs. {{ number_format($bill->total_amount, 2) }}</span>
                </div>
                @if($bill->discount_amount > 0)
                <div class="total-row">
                    <span class="total-label">Discount</span>
                    <span class="total-amount">-Rs. {{ number_format($bill->discount_amount, 2) }}</span>
                </div>
                @endif
                @if($bill->tax_amount > 0)
                <div class="total-row">
                    <span class="total-label">Tax</span>
                    <span class="total-amount">Rs. {{ number_format($bill->tax_amount, 2) }}</span>
                </div>
                @endif
                <div class="total-row highlight">
                    <span>Total Amount</span>
                    <span>Rs. {{ number_format($bill->net_amount, 2) }}</span>
                </div>
                <div class="total-row">
                    <span class="total-label">Paid Amount</span>
                    <span class="total-amount">Rs. {{ number_format($bill->paid_amount, 2) }}</span>
                </div>
                <div class="total-row">
                    <span class="total-label">Outstanding</span>
                    <span class="total-amount">Rs. {{ number_format($bill->outstanding_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Notes -->
        @if($bill->notes)
        <div class="notes">
            <strong>Note:</strong> {{ $bill->notes }}
        </div>
        @else
        <div class="notes">
            <strong>Note:</strong> Monthly transport fee bill. This is a computer-generated bill. No signature required.
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            Generated on {{ now()->format('d M Y, h:i A') }}
        </div>
    </div>
</body>
</html>
