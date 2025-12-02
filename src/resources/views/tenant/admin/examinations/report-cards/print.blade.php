<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Card - {{ $reportCard->student->full_name ?? 'Student' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .report-card {
            border: 2px solid #000;
            padding: 20px;
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .card-title {
            font-size: 18px;
            margin-top: 10px;
        }
        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .info-group {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }
        .info-value {
            display: inline-block;
        }
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .results-table th,
        .results-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .results-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f5f5f5;
            border: 1px solid #ccc;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ccc;
            font-size: 12px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="report-card">
        <div class="header">
            <div class="school-name">{{ $tenant->data['school_name'] ?? 'School Name' }}</div>
            <div class="card-title">REPORT CARD</div>
            <div style="margin-top: 10px; font-size: 14px;">{{ $reportCard->exam->exam_name ?? 'Examination' }} - {{ $reportCard->exam->academic_year ?? '' }}</div>
        </div>

        <div class="student-info">
            <div>
                <div class="info-group">
                    <span class="info-label">Student Name:</span>
                    <span class="info-value">{{ $reportCard->student->full_name ?? 'N/A' }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Admission No:</span>
                    <span class="info-value">{{ $reportCard->student->admission_number ?? 'N/A' }}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Class:</span>
                    <span class="info-value">{{ $reportCard->schoolClass->class_name ?? 'N/A' }}</span>
                </div>
            </div>
            <div>
                @if($reportCard->section)
                <div class="info-group">
                    <span class="info-label">Section:</span>
                    <span class="info-value">{{ $reportCard->section->section_name }}</span>
                </div>
                @endif
                <div class="info-group">
                    <span class="info-label">Roll Number:</span>
                    <span class="info-value">{{ $reportCard->student->roll_number ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <table class="results-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Max Marks</th>
                    <th>Marks Obtained</th>
                    <th>Percentage</th>
                    <th>Grade</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($reportCard->subject_results) && is_array($reportCard->subject_results))
                    @foreach($reportCard->subject_results as $subjectResult)
                    <tr>
                        <td>{{ $subjectResult['subject_name'] ?? 'N/A' }}</td>
                        <td>{{ number_format($subjectResult['max_marks'] ?? 0, 2) }}</td>
                        <td>{{ number_format($subjectResult['marks_obtained'] ?? 0, 2) }}</td>
                        <td>{{ number_format($subjectResult['percentage'] ?? 0, 2) }}%</td>
                        <td>{{ $subjectResult['grade'] ?? '-' }}</td>
                        <td>{{ ucfirst($subjectResult['status'] ?? '-') }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">No results available</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-row">
                <span><strong>Total Marks Obtained:</strong></span>
                <span>{{ number_format($reportCard->total_marks_obtained, 2) }}</span>
            </div>
            <div class="summary-row">
                <span><strong>Total Maximum Marks:</strong></span>
                <span>{{ number_format($reportCard->total_max_marks, 2) }}</span>
            </div>
            <div class="summary-row">
                <span><strong>Overall Percentage:</strong></span>
                <span>{{ number_format($reportCard->overall_percentage, 2) }}%</span>
            </div>
            @if($reportCard->overall_grade)
            <div class="summary-row">
                <span><strong>Overall Grade:</strong></span>
                <span>{{ $reportCard->overall_grade }}</span>
            </div>
            @endif
            @if($reportCard->overall_gpa)
            <div class="summary-row">
                <span><strong>Overall GPA:</strong></span>
                <span>{{ $reportCard->overall_gpa }}</span>
            </div>
            @endif
            @if($reportCard->rank)
            <div class="summary-row">
                <span><strong>Rank:</strong></span>
                <span>{{ $reportCard->rank }}</span>
            </div>
            @endif
        </div>

        @if($reportCard->remarks)
        <div style="margin-top: 20px; padding: 15px; background-color: #f9f9f9; border: 1px solid #ccc;">
            <div style="font-weight: bold; margin-bottom: 10px;">Remarks:</div>
            <div>{!! nl2br(e($reportCard->remarks)) !!}</div>
        </div>
        @endif

        <div class="footer">
            <div>This is a computer-generated document. No signature required.</div>
            <div style="margin-top: 10px;">Generated on: {{ $reportCard->generated_at ? $reportCard->generated_at->format('d M Y') : date('d M Y') }}</div>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #4F46E5; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Print Report Card
        </button>
    </div>
</body>
</html>

