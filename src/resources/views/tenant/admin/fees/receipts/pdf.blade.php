<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $payment->payment_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .container { padding: 20px; }
        .header { text-align: center; border-bottom: 3px solid #2563eb; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { font-size: 24px; color: #1e40af; }
        .receipt-number { background: #f0f9ff; padding: 10px; text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 20px; }
        .section { margin-bottom: 20px; }
        .section-title { font-weight: bold; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 5px 0; }
        .info-table .label { font-weight: bold; width: 150px; }
        .fee-table { margin-top: 15px; }
        .fee-table th { background: #f9fafb; padding: 8px; border: 1px solid #e5e7eb; text-align: left; }
        .fee-table td { padding: 8px; border: 1px solid #e5e7eb; }
        .amount-box { background: #f0f9ff; border: 2px solid #2563eb; padding: 15px; margin: 20px 0; }
        .amount-box .amount-value { font-size: 24px; font-weight: bold; color: #2563eb; }
        .amount-words { margin-top: 8px; font-style: italic; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #6b7280; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ $tenant->institution_name ?? 'School Name' }}</h1>
            <p>PAYMENT RECEIPT</p>
        </div>

        <div class="receipt-number">
            Receipt No: {{ $payment->payment_number }}
        </div>

        <div class="section">
            <div class="section-title">Student Details</div>
            <table class="info-table">
                <tr>
                    <td class="label">Student Name:</td>
                    <td>{{ $payment->student->full_name }}</td>
                </tr>
                <tr>
                    <td class="label">Admission No:</td>
                    <td>{{ $payment->student->admission_number }}</td>
                </tr>
                <tr>
                    <td class="label">Class:</td>
                    <td>{{ $payment->student->currentEnrollment?->schoolClass?->class_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Father's Name:</td>
                    <td>{{ $payment->student->father_name ?? '-' }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Payment Details</div>
            <table class="info-table">
                <tr>
                    <td class="label">Payment Date:</td>
                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Payment Method:</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                </tr>
                @if($payment->reference_number)
                <tr>
                    <td class="label">Reference No:</td>
                    <td>{{ $payment->reference_number }}</td>
                </tr>
                @endif
            </table>
        </div>

        @if($payment->invoice && $payment->invoice->items->count() > 0)
        <div class="section">
            <div class="section-title">Fee Breakdown</div>
            <table class="fee-table">
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

        <div class="amount-box">
            <div>Amount Paid</div>
            <div class="amount-value">₹{{ number_format($payment->amount, 2) }}</div>
            <div class="amount-words">
                In Words: {{ ucwords(\NumberFormatter::create('en_IN', \NumberFormatter::SPELLOUT)->format($payment->amount)) }} Rupees Only
            </div>
        </div>

        <div class="footer">
            <p>Thank you for your payment!</p>
            <p>This is a computer-generated receipt.</p>
            <p>Generated on: {{ now()->format('d M Y') }}</p>
        </div>
    </div>
</body>
</html>

