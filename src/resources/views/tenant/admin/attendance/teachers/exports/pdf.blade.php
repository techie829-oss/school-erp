<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Attendance Report - Print</title>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .print-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .print-header h1 {
            margin: 0;
            font-size: 24px;
            color: #1f2937;
        }
        .print-header p {
            margin: 5px 0;
            color: #6b7280;
            font-size: 14px;
        }
        .report-info {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9fafb;
            border-radius: 5px;
        }
        .report-info h2 {
            margin: 0 0 10px 0;
            font-size: 18px;
            color: #1f2937;
        }
        .report-info p {
            margin: 5px 0;
            font-size: 14px;
            color: #4b5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #d1d5db;
        }
        th {
            background-color: #f3f4f6;
            font-weight: 600;
            color: #1f2937;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .summary-box {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .summary-item {
            flex: 1;
            min-width: 150px;
            padding: 15px;
            background-color: #f3f4f6;
            border-radius: 5px;
            text-align: center;
        }
        .summary-item .label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 5px;
        }
        .summary-item .value {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
        }
        .print-actions {
            margin-bottom: 20px;
            text-align: center;
        }
        .btn-print {
            background-color: #2563eb;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
        }
        .btn-print:hover {
            background-color: #1d4ed8;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-present {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-absent {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .badge-late {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-on-leave {
            background-color: #e9d5ff;
            color: #6b21a8;
        }
    </style>
</head>
<body>
    <div class="no-print print-actions">
        <button onclick="window.print()" class="btn-print">Print Report</button>
    </div>

    <div class="print-header">
        <h1>{{ $tenant->data['name'] ?? 'School ERP' }}</h1>
        <p>Teacher Attendance Report</p>
        <p>Generated: {{ now()->format('F d, Y h:i A') }}</p>
    </div>

    <div class="report-info">
        <h2>{{ $reportData['title'] }}</h2>
        @if(isset($reportData['date']))
            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($reportData['date'])->format('F d, Y') }}</p>
        @endif
        @if(isset($reportData['date_from']) && isset($reportData['date_to']))
            <p><strong>Period:</strong> {{ \Carbon\Carbon::parse($reportData['date_from'])->format('M d, Y') }} to {{ \Carbon\Carbon::parse($reportData['date_to'])->format('M d, Y') }}</p>
        @endif
    </div>

    @if(isset($reportData['summary']))
    <div class="summary-box">
        @if(isset($reportData['summary']['total']))
        <div class="summary-item">
            <div class="label">Total Teachers</div>
            <div class="value">{{ $reportData['summary']['total'] }}</div>
        </div>
        @endif
        @if(isset($reportData['summary']['present']))
        <div class="summary-item">
            <div class="label">Present</div>
            <div class="value">{{ $reportData['summary']['present'] }}</div>
        </div>
        @endif
        @if(isset($reportData['summary']['absent']))
        <div class="summary-item">
            <div class="label">Absent</div>
            <div class="value">{{ $reportData['summary']['absent'] }}</div>
        </div>
        @endif
        @if(isset($reportData['summary']['late']))
        <div class="summary-item">
            <div class="label">Late</div>
            <div class="value">{{ $reportData['summary']['late'] }}</div>
        </div>
        @endif
        @if(isset($reportData['summary']['percentage']))
        <div class="summary-item">
            <div class="label">Attendance %</div>
            <div class="value">{{ number_format($reportData['summary']['percentage'], 2) }}%</div>
        </div>
        @endif
        @if(isset($reportData['summary']['avg_hours']))
        <div class="summary-item">
            <div class="label">Avg Hours</div>
            <div class="value">{{ number_format($reportData['summary']['avg_hours'], 2) }}</div>
        </div>
        @endif
    </div>
    @endif

    <table>
        <thead>
            @if($reportData['type'] === 'daily')
            <tr>
                <th>Employee ID</th>
                <th>Teacher Name</th>
                <th>Department</th>
                <th>Status</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Hours</th>
                <th>Remarks</th>
            </tr>
            @elseif($reportData['type'] === 'monthly' || $reportData['type'] === 'defaulters')
            <tr>
                <th>Employee ID</th>
                <th>Teacher Name</th>
                <th>Department</th>
                <th>Total Days</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Late</th>
                <th>On Leave</th>
                <th>Attendance %</th>
                <th>Avg Hours</th>
            </tr>
            @elseif($reportData['type'] === 'department_wise')
            <tr>
                <th>Department</th>
                <th>Teachers</th>
                <th>Total Days</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Late</th>
                <th>On Leave</th>
                <th>Attendance %</th>
            </tr>
            @elseif($reportData['type'] === 'teacher_wise')
            <tr>
                <th>Date</th>
                <th>Status</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Hours</th>
                <th>Remarks</th>
            </tr>
            @endif
        </thead>
        <tbody>
            @if($reportData['type'] === 'daily')
                @foreach($reportData['records'] as $record)
                <tr>
                    <td>{{ $record->teacher->employee_id ?? 'N/A' }}</td>
                    <td>{{ $record->teacher->full_name ?? 'N/A' }}</td>
                    <td>{{ $record->teacher->department->name ?? 'N/A' }}</td>
                    <td>
                        <span class="badge badge-{{ str_replace('_', '-', $record->status) }}">
                            {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                        </span>
                    </td>
                    <td>{{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('h:i A') : '-' }}</td>
                    <td>{{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('h:i A') : '-' }}</td>
                    <td>{{ $record->total_hours ? number_format($record->total_hours, 2) : '-' }}</td>
                    <td>{{ $record->remarks ?? '-' }}</td>
                </tr>
                @endforeach
            @elseif($reportData['type'] === 'monthly' || $reportData['type'] === 'defaulters')
                @foreach($reportData['records'] as $record)
                <tr>
                    <td>{{ $record['teacher']->employee_id ?? 'N/A' }}</td>
                    <td>{{ $record['teacher']->full_name ?? 'N/A' }}</td>
                    <td>{{ $record['department'] ?? 'N/A' }}</td>
                    <td class="text-center">{{ $record['total_days'] ?? 0 }}</td>
                    <td class="text-center">{{ $record['present'] ?? 0 }}</td>
                    <td class="text-center">{{ $record['absent'] ?? 0 }}</td>
                    <td class="text-center">{{ $record['late'] ?? 0 }}</td>
                    <td class="text-center">{{ $record['on_leave'] ?? 0 }}</td>
                    <td class="text-center">{{ number_format($record['percentage'] ?? 0, 2) }}%</td>
                    <td class="text-center">{{ $record['avg_hours'] ? number_format($record['avg_hours'], 2) : '-' }}</td>
                </tr>
                @endforeach
            @elseif($reportData['type'] === 'department_wise')
                @foreach($reportData['records'] as $record)
                <tr>
                    <td>{{ $record['department'] ?? 'N/A' }}</td>
                    <td class="text-center">{{ $record['teacher_count'] ?? 0 }}</td>
                    <td class="text-center">{{ $record['total_days'] ?? 0 }}</td>
                    <td class="text-center">{{ $record['present'] ?? 0 }}</td>
                    <td class="text-center">{{ $record['absent'] ?? 0 }}</td>
                    <td class="text-center">{{ $record['late'] ?? 0 }}</td>
                    <td class="text-center">{{ $record['on_leave'] ?? 0 }}</td>
                    <td class="text-center">{{ number_format($record['percentage'] ?? 0, 2) }}%</td>
                </tr>
                @endforeach
            @elseif($reportData['type'] === 'teacher_wise')
                @foreach($reportData['records'] as $record)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($record->attendance_date)->format('M d, Y') }}</td>
                    <td>
                        <span class="badge badge-{{ str_replace('_', '-', $record->status) }}">
                            {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                        </span>
                    </td>
                    <td>{{ $record->check_in_time ? \Carbon\Carbon::parse($record->check_in_time)->format('h:i A') : '-' }}</td>
                    <td>{{ $record->check_out_time ? \Carbon\Carbon::parse($record->check_out_time)->format('h:i A') : '-' }}</td>
                    <td>{{ $record->total_hours ? number_format($record->total_hours, 2) : '-' }}</td>
                    <td>{{ $record->remarks ?? '-' }}</td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</body>
</html>
