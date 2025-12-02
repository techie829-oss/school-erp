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
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .receipt-info > div {
            flex: 1;
        }
        .receipt-info h3 {
            font-size: 14px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .receipt-info p {
            margin: 5px 0;
            font-size: 12px;
        }
        .amount-box {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background-color: #f5f5f5;
            border: 2px solid #333;
        }
        .amount-box .label {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .amount-box .amount {
            font-size: 32px;
            font-weight: bold;
            color: #333;
        }
        .payment-details {
            margin: 20px 0;
        }
        .payment-details table {
            width: 100%;
            border-collapse: collapse;
        }
        .payment-details table td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .payment-details table td:first-child {
            font-weight: bold;
            width: 40%;
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
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->name ?? 'School ERP' }}</h1>
        <p>Payment Receipt</p>
    </div>

    <div class="receipt-info">
        <div>
            <h3>Receipt Information</h3>
            <p><strong>Receipt Number:</strong> {{ $payment->payment_number }}</p>
            <p><strong>Payment Date:</strong> {{ $payment->payment_date->format('d M Y') }}</p>
            <p><strong>Status:</strong> 
                <span class="status-badge">{{ ucfirst($payment->status) }}</span>
            </p>
        </div>
        <div>
            <h3>Student Information</h3>
            <p><strong>Name:</strong> {{ $payment->student->full_name }}</p>
            <p><strong>Admission Number:</strong> {{ $payment->student->admission_number }}</p>
            @if($payment->student->class)
            <p><strong>Class:</strong> {{ $payment->student->class->name }}</p>
            @endif
        </div>
    </div>

    <div class="amount-box">
        <div class="label">Amount Received</div>
        <div class="amount">₹{{ number_format($payment->amount, 2) }}</div>
    </div>

    <div class="payment-details">
        <table>
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
            @if($payment->collected_by)
            <tr>
                <td>Collected By:</td>
                <td>{{ $payment->collector->name ?? '-' }}</td>
            </tr>
            @endif
        </table>
    </div>

    @if($payment->notes)
    <div style="margin-top: 20px; padding: 10px; background-color: #f9f9f9; border-left: 3px solid #333;">
        <strong>Notes:</strong> {{ $payment->notes }}
    </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated receipt. No signature required.</p>
        <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>
</body>
</html>

