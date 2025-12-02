<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>No Dues Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            padding: 20px;
            font-size: 14px;
        }
        .no-print {
            margin-bottom: 20px;
            text-align: right;
        }
        .print-button {
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .certificate {
            border: 3px solid #2563eb;
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        .certificate-header {
            border-bottom: 3px solid #2563eb;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .certificate-header h1 {
            font-size: 32px;
            color: #1e40af;
            margin-bottom: 10px;
        }
        .certificate-header p {
            font-size: 18px;
            color: #6b7280;
            font-weight: bold;
        }
        .certificate-body {
            padding: 30px 0;
        }
        .certificate-body h2 {
            font-size: 28px;
            margin-bottom: 30px;
            color: #111827;
        }
        .certificate-body p {
            font-size: 16px;
            line-height: 2;
            margin-bottom: 15px;
        }
        .student-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e40af;
            margin: 20px 0;
        }
        .certificate-footer {
            margin-top: 50px;
            display: flex;
            justify-content: space-around;
            padding-top: 30px;
        }
        .signature-box {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 2px solid #111827;
            margin-top: 80px;
            padding-top: 5px;
            font-weight: bold;
        }
        .stamp-placeholder {
            border: 2px dashed #9ca3af;
            padding: 20px;
            min-height: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            font-size: 12px;
            color: #6b7280;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .certificate {
                border: 3px solid #2563eb;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Print Button (hidden when printing) -->
    <div class="no-print">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="font-size: 24px; color: #111827;">Print No Dues Certificate</h1>
            <button onclick="updatePrint()" class="print-button">
                Update & Print
            </button>
        </div>
        <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">Display Options</label>
                    <label style="display: flex; align-items: center; margin-bottom: 5px;">
                        <input type="checkbox" id="showPrincipalStamp" {{ $showPrincipalStamp ? 'checked' : '' }} style="margin-right: 8px;">
                        <span>Principal Stamp</span>
                    </label>
                    <label style="display: flex; align-items: center; margin-bottom: 5px;">
                        <input type="checkbox" id="showClassTeacherSign" {{ $showClassTeacherSign ? 'checked' : '' }} style="margin-right: 8px;">
                        <span>Class Teacher</span>
                    </label>
                    <label style="display: flex; align-items: center;">
                        <input type="checkbox" id="showAccountantSign" {{ $showAccountantSign ? 'checked' : '' }} style="margin-right: 8px;">
                        <span>Accountant</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Certificate -->
    <div class="certificate">
        <div class="certificate-header">
            <h1>{{ $tenant->institution_name ?? 'School Name' }}</h1>
            <p>NO DUES CERTIFICATE</p>
        </div>
        <div class="certificate-body">
            <h2>CERTIFICATE</h2>
            <p>This is to certify that</p>
            <p class="student-name">{{ $certificate->student->full_name ?? 'N/A' }}</p>
            <p>Admission Number: <strong>{{ $certificate->student->admission_number ?? 'N/A' }}</strong></p>
            <p>Class: <strong>{{ $certificate->schoolClass->class_name ?? 'N/A' }}</strong></p>
            @if($certificate->section)
            <p>Section: <strong>{{ $certificate->section->section_name }}</strong></p>
            @endif
            <p style="margin-top: 30px;">has cleared all dues and no outstanding amount is pending against his/her name.</p>
            <p style="margin-top: 20px;">Certificate Number: <strong>{{ $certificate->certificate_number }}</strong></p>
            <p>Issue Date: <strong>{{ $certificate->issue_date ? $certificate->issue_date->format('d M Y') : 'N/A' }}</strong></p>
            @if($certificate->remarks)
            <p style="margin-top: 15px; font-style: italic; color: #6b7280;">Remarks: {{ $certificate->remarks }}</p>
            @endif
        </div>
        @if($showPrincipalStamp || $showClassTeacherSign || $showAccountantSign)
        <div class="certificate-footer">
            @if($showPrincipalStamp)
            <div class="signature-box">
                <div class="stamp-placeholder">Principal Stamp</div>
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
        @endif
    </div>

    <script>
        function updatePrint() {
            const url = new URL(window.location.href);
            url.searchParams.set('show_principal_stamp', document.getElementById('showPrincipalStamp').checked ? '1' : '0');
            url.searchParams.set('show_class_teacher_sign', document.getElementById('showClassTeacherSign').checked ? '1' : '0');
            url.searchParams.set('show_accountant_sign', document.getElementById('showAccountantSign').checked ? '1' : '0');
            window.location.href = url.toString();
        }

        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('show_principal_stamp') || urlParams.has('show_class_teacher_sign') || urlParams.has('show_accountant_sign')) {
                window.print();
            }
        };
    </script>
</body>
</html>

