<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>No Dues Certificates PDF Preview</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
        }
        @media print {
            body {
                margin: 0;
                padding: 0;
                background: white;
            }
            .no-print {
                display: none !important;
            }
            .a4-page {
                page-break-after: always;
                box-shadow: none !important;
                margin: 0 !important;
            }
        }
        .no-print {
            position: sticky;
            top: 0;
            z-index: 50;
            background: white;
            border-bottom: 2px solid #2563eb;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
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
            background-color: #2563eb;
            color: white;
            padding: 8px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            margin-left: 10px;
        }
        .a4-page {
            width: 210mm;
            min-height: 297mm;
            background: white;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            padding: 20mm;
            page-break-after: always;
        }
        .a4-page:last-child {
            page-break-after: auto;
        }
        .certificate {
            border: 2px solid #2563eb;
            padding: 30px;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .certificate-header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .certificate-header h1 {
            font-size: 28px;
            color: #1e40af;
            margin-bottom: 5px;
        }
        .certificate-body {
            flex: 1;
            padding: 20px 0;
        }
        .certificate-body h2 {
            font-size: 24px;
            margin-bottom: 30px;
            color: #111827;
        }
        .certificate-body p {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 15px;
        }
        .student-name {
            font-size: 22px;
            font-weight: bold;
            color: #1e40af;
            margin: 20px 0;
        }
        .certificate-footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-around;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #111827;
            margin-top: 60px;
            padding-top: 5px;
        }
        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div class="header-row">
            <h1 class="header-title">Bulk Export No Dues Certificates</h1>
            <div>
                <a href="{{ url('/admin/fees/no-dues/bulk-actions?' . http_build_query(request()->except(['certificate_ids']))) }}" style="background-color: #6b7280; color: white; padding: 8px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block;">
                    ← Back
                </a>
                <form id="downloadForm" action="{{ url('/admin/fees/no-dues/bulk-export') }}" method="POST" style="display: inline;">
                    @csrf
                    @foreach(request('certificate_ids', []) as $id)
                        <input type="hidden" name="certificate_ids[]" value="{{ $id }}">
                    @endforeach
                    <input type="hidden" name="cards_per_page" value="{{ request('cards_per_page', 1) }}">
                    <input type="hidden" name="show_principal_stamp" value="{{ request('show_principal_stamp', 0) }}">
                    <input type="hidden" name="show_class_teacher_sign" value="{{ request('show_class_teacher_sign', 0) }}">
                    <input type="hidden" name="show_accountant_sign" value="{{ request('show_accountant_sign', 0) }}">
                    <button type="submit" style="background-color: #10b981; color: white; padding: 8px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; margin-left: 10px;">
                        Download PDF
                    </button>
                </form>
                <button onclick="window.print()" class="print-button">Print</button>
            </div>
        </div>
    </div>

    @foreach($certificates as $certificate)
        <div class="a4-page">
            <div class="certificate">
                <div class="certificate-header">
                    <h1>{{ $tenant->institution_name ?? 'School Name' }}</h1>
                    <p style="font-size: 14px; color: #6b7280;">NO DUES CERTIFICATE</p>
                </div>
                <div class="certificate-body">
                    <h2>CERTIFICATE</h2>
                    <p>This is to certify that <span class="student-name">{{ $certificate->student->full_name ?? 'N/A' }}</span></p>
                    <p>Admission Number: <strong>{{ $certificate->student->admission_number ?? 'N/A' }}</strong></p>
                    <p>Class: <strong>{{ $certificate->schoolClass->class_name ?? 'N/A' }}</strong></p>
                    @if($certificate->section)
                    <p>Section: <strong>{{ $certificate->section->section_name }}</strong></p>
                    @endif
                    <p style="margin-top: 30px;">has cleared all dues and no outstanding amount is pending against his/her name.</p>
                    <p style="margin-top: 20px;">Certificate Number: <strong>{{ $certificate->certificate_number }}</strong></p>
                    <p>Issue Date: <strong>{{ $certificate->issue_date ? $certificate->issue_date->format('d M Y') : 'N/A' }}</strong></p>
                </div>
                <div class="certificate-footer">
                    @if($showPrincipalStamp)
                    <div class="signature-box">
                        <div style="border: 2px dashed #9ca3af; padding: 20px; min-height: 80px; display: flex; align-items: center; justify-content: center;">
                            Principal Stamp
                        </div>
                        <div class="signature-line">Principal</div>
                    </div>
                    @endif
                    @if($showClassTeacherSign)
                    <div class="signature-box">
                        <div class="signature-line">Class Teacher</div>
                    </div>
                    @endif
                    @if($showAccountantSign)
                    <div class="signature-box">
                        <div class="signature-line">Accountant</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</body>
</html>

