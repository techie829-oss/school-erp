<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Attendance Report - {{ $tenant->data['name'] ?? 'School ERP' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .header p {
            font-size: 14px;
            color: #666;
        }
        .report-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border-radius: 5px;
        }
        .report-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .report-info td {
            padding: 5px 10px;
        }
        .report-info td:first-child {
            font-weight: bold;
            width: 150px;
        }
        .summary {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #e8f4f8;
            border-radius: 5px;
        }
        .summary h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #1e3a8a;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .summary-item {
            text-align: center;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
        }
        .summary-item .label {
            font-size: 11px;
            color: #666;
            margin-bottom: 5px;
        }
        .summary-item .value {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        thead {
            background-color: #1e3a8a;
            color: white;
        }
        thead th {
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 11px;
        }
        tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tbody tr:hover {
            background-color: #f3f4f6;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-present {
            background-color: #10b981;
            color: white;
        }
        .status-absent {
            background-color: #ef4444;
            color: white;
        }
        .status-late {
            background-color: #f59e0b;
            color: white;
        }
        .status-on-leave {
            background-color: #6366f1;
            color: white;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        @media print {
            body {
                margin: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $tenant->data['name'] ?? 'School ERP' }}</h1>
        <p>Student Attendance Report</p>
    </div>

    <div class="report-info">
        <table>
            <tr>
                <td>Report Type:</td>
                <td>{{ ucfirst(str_replace('_', ' ', $reportData['type'] ?? 'daily')) }}</td>
                <td>Generated On:</td>
                <td>{{ now()->format('F d, Y h:i A') }}</td>
            </tr>
            @if(isset($reportData['date']))
            <tr>
                <td>Date:</td>
                <td>{{ \Carbon\Carbon::parse($reportData['date'])->format('F d, Y') }}</td>
                <td></td>
                <td></td>
            </tr>
            @endif
            @if(isset($reportData['date_from']) && isset($reportData['date_to']))
            <tr>
                <td>Date Range:</td>
                <td>{{ \Carbon\Carbon::parse($reportData['date_from'])->format('M d, Y') }} - {{ \Carbon\Carbon::parse($reportData['date_to'])->format('M d, Y') }}</td>
                <td></td>
                <td></td>
            </tr>
            @endif
        </table>
    </div>

    @if(isset($reportData['summary']))
    <div class="summary">
        <h3>Summary</h3>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="label">Total Students</div>
                <div class="value">{{ $reportData['summary']['total'] ?? 0 }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Present</div>
                <div class="value">{{ $reportData['summary']['present'] ?? 0 }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Absent</div>
                <div class="value">{{ $reportData['summary']['absent'] ?? 0 }}</div>
            </div>
            <div class="summary-item">
                <div class="label">Attendance %</div>
                <div class="value">{{ number_format($reportData['summary']['percentage'] ?? 0, 1) }}%</div>
            </div>
        </div>
    </div>
    @endif

    @if(isset($reportData['records']) && $reportData['records']->count() > 0)
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Admission No.</th>
                <th>Student Name</th>
                <th>Class</th>
                <th>Section</th>
                <th>Status</th>
                @if(isset($reportData['type']) && $reportData['type'] == 'student_wise')
                <th>Total Days</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Percentage</th>
                @else
                <th>Time</th>
                <th>Remarks</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($reportData['records'] as $index => $record)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $record->student->admission_number ?? 'N/A' }}</td>
                <td>{{ $record->student->full_name ?? 'N/A' }}</td>
                <td>{{ $record->class->name ?? 'N/A' }}</td>
                <td>{{ $record->section->name ?? 'N/A' }}</td>
                <td>
                    <span class="status-badge status-{{ $record->status }}">
                        {{ ucfirst($record->status) }}
                    </span>
                </td>
                @if(isset($reportData['type']) && $reportData['type'] == 'student_wise')
                <td>{{ $record->total_days ?? 0 }}</td>
                <td>{{ $record->present_days ?? 0 }}</td>
                <td>{{ $record->absent_days ?? 0 }}</td>
                <td>{{ number_format($record->percentage ?? 0, 1) }}%</td>
                @else
                <td>{{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('h:i A') : 'N/A' }}</td>
                <td>{{ $record->remarks ?? '-' }}</td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="no-data">
        <p>No attendance records found for the selected criteria.</p>
    </div>
    @endif

    <div class="footer">
        <p>This is a computer-generated report. Generated on {{ now()->format('F d, Y h:i A') }}</p>
        <p>{{ $tenant->data['name'] ?? 'School ERP' }} - Student Attendance Management System</p>
    </div>
</body>
</html>

