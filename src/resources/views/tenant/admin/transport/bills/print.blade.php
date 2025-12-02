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
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .header p {
            font-size: 14px;
            color: #666;
        }
        .bill-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .bill-info > div {
            flex: 1;
        }
        .bill-info h3 {
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .bill-info p {
            margin: 5px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        table td {
            font-size: 11px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            margin-top: 20px;
            margin-left: auto;
            width: 300px;
        }
        .totals table {
            margin-bottom: 0;
        }
        .totals td {
            border: none;
            padding: 5px 8px;
        }
        .totals .label {
            font-weight: bold;
            text-align: right;
        }
        .totals .amount {
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-paid { background-color: #d4edda; color: #155724; }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-overdue { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->name ?? 'School ERP' }}</h1>
        <p>Transport Bill</p>
    </div>

    <div class="bill-info">
        <div>
            <h3>Bill Information</h3>
            <p><strong>Bill Number:</strong> {{ $bill->bill_number }}</p>
            <p><strong>Bill Date:</strong> {{ $bill->bill_date->format('d M Y') }}</p>
            <p><strong>Due Date:</strong> {{ $bill->due_date->format('d M Y') }}</p>
            @if($bill->academic_year)
            <p><strong>Academic Year:</strong> {{ $bill->academic_year }}</p>
            @endif
            @if($bill->term)
            <p><strong>Term:</strong> {{ $bill->term }}</p>
            @endif
            <p><strong>Status:</strong>
                <span class="status-badge status-{{ $bill->status }}">
                    {{ ucfirst($bill->status) }}
                </span>
            </p>
        </div>
        <div>
            <h3>Student Information</h3>
            <p><strong>Name:</strong> {{ $bill->student->full_name }}</p>
            <p><strong>Admission Number:</strong> {{ $bill->student->admission_number }}</p>
            @if($bill->student->class)
            <p><strong>Class:</strong> {{ $bill->student->class->name }}</p>
            @endif
            @if($bill->assignment && $bill->assignment->route)
            <p><strong>Route:</strong> {{ $bill->assignment->route->name }}</p>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-center">Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Amount</th>
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

    <div class="totals">
        <table>
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
            <tr>
                <td class="label"><strong>Total Amount:</strong></td>
                <td class="amount"><strong>₹{{ number_format($bill->net_amount, 2) }}</strong></td>
            </tr>
            <tr>
                <td class="label">Paid Amount:</td>
                <td class="amount">₹{{ number_format($bill->paid_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label"><strong>Outstanding:</strong></td>
                <td class="amount"><strong>₹{{ number_format($bill->outstanding_amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    @if($bill->notes)
    <div style="margin-top: 20px; padding: 10px; background-color: #f9f9f9; border-left: 3px solid #333;">
        <strong>Notes:</strong> {{ $bill->notes }}
    </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated bill. No signature required.</p>
        <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>
</body>
</html>

