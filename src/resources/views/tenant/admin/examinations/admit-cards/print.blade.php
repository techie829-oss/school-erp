<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Card - {{ $admitCard->hall_ticket_number }}</title>
    <style>
        @page {
            margin: 0;
            size: A4 portrait;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #111827;
        }

        .page {
            width: 210mm;
            height: 297mm;
            background: white;
            margin: 0 auto;
            padding: 0;
        }

        .admit-card {
            border: 1px solid #d1d5db;
            background: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
            color: #111827;
            height: 100%;
        }

        .card-header {
            border-bottom: 2px solid #2563eb;
            padding-bottom: 8px;
            margin-bottom: 12px;
            text-align: center;
        }

        .school-name {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 0.05em;
        }

        .card-title {
            font-size: 12px;
            font-weight: 600;
            color: #2563eb;
        }

        .card-content {
            font-size: 12px;
            display: flex;
            gap: 12px;
        }

        .info-section {
            flex: 1;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .info-label {
            font-weight: 600;
        }

        .photo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .photo-placeholder {
            width: 35mm;
            height: 45mm;
            border: 1px solid #ccc;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 9px;
            color: #999;
        }

        .qr-code {
            width: 25mm;
            height: 25mm;
            border: 1px solid #ccc;
            background: #f9f9f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #999;
        }

        .qr-code img {
            max-width: 100%;
            max-height: 100%;
        }

        .exam-schedule {
            margin-top: 12px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            overflow: hidden;
        }

        .exam-schedule-title {
            background-color: #2563eb;
            color: white;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
        }

        .exam-schedule table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }

        .exam-schedule th {
            background-color: #f3f4f6;
            padding: 4px;
            text-align: left;
            font-weight: 600;
            border-bottom: 1px solid #d1d5db;
        }

        .exam-schedule td {
            padding: 4px;
            border-bottom: 1px solid #e5e7eb;
        }

        .exam-schedule tr:last-child td {
            border-bottom: none;
        }

        .signatures {
            display: flex;
            margin-top: 16px;
            padding-top: 12px;
            border-top: 1px solid #d1d5db;
        }

        .signature-box {
            text-align: center;
            flex: 1;
        }

        .signature-line {
            height: 40px;
            border-bottom: 1px solid #9ca3af;
            margin-bottom: 4px;
        }

        .signature-label {
            font-size: 12px;
            font-weight: 600;
        }

        .stamp-placeholder {
            border: 2px dashed #9ca3af;
            padding: 8px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            min-height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 4px;
        }

        .card-footer {
            font-size: 12px;
            color: #6b7280;
            margin-top: 8px;
            text-align: center;
        }

        .no-print {
            background: white;
            padding: 20px;
            border-bottom: 2px solid #2563eb;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .control-panel {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #111827;
        }

        .print-button {
            background-color: #2563eb;
            color: white;
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button:hover {
            background-color: #1d4ed8;
        }

        .options-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 768px) {
            .options-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .option-group {
            display: flex;
            flex-direction: column;
        }

        .option-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .checkbox-group {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .checkbox-input {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #2563eb;
        }

        .checkbox-text {
            font-size: 14px;
            color: #4b5563;
        }

        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .page {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <div class="control-panel">
            <div class="header-row">
                <h1 class="header-title">Print Admit Card</h1>
                <button onclick="updatePrint()" class="print-button">Update & Print</button>
            </div>

            <div class="options-grid">
                <div class="option-group">
                    <label class="option-label">Display Options</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="show_exam_schedule" {{ (isset($showExamSchedule) && $showExamSchedule) ? 'checked' : '' }} class="checkbox-input">
                            <span class="checkbox-text">Show Exam Schedule</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="show_qr_code" {{ (isset($showQrCode) && $showQrCode) ? 'checked' : '' }} class="checkbox-input">
                            <span class="checkbox-text">Show QR Code</span>
                        </label>
                    </div>
                </div>

                <div class="option-group">
                    <label class="option-label">Signature & Stamp Options</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="show_principal_stamp" {{ (isset($showPrincipalStamp) && $showPrincipalStamp) ? 'checked' : '' }} class="checkbox-input">
                            <span class="checkbox-text">Principal Stamp</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="show_class_teacher_sign" {{ (isset($showClassTeacherSign) && $showClassTeacherSign) ? 'checked' : '' }} class="checkbox-input">
                            <span class="checkbox-text">Class Teacher</span>
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" id="show_supervisor_sign" {{ (isset($showSupervisorSign) && $showSupervisorSign) ? 'checked' : '' }} class="checkbox-input">
                            <span class="checkbox-text">Supervisor</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="page">
        <div class="admit-card">
            <div class="card-header">
                <h2 class="school-name">{{ $tenant->data['school_name'] ?? $tenant->data['name'] ?? 'School Name' }}</h2>
                <p class="card-title">ADMIT CARD</p>
            </div>
            <div class="card-content">
                <div class="info-section">
                    <div class="info-row">
                        <span class="info-label">Hall Ticket:</span>
                        <span>{{ $admitCard->hall_ticket_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Student Name:</span>
                        <span>{{ $admitCard->student_name }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Admission No:</span>
                        <span>{{ $admitCard->admission_number }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Class:</span>
                        <span>{{ $admitCard->schoolClass->class_name ?? 'N/A' }}</span>
                    </div>
                    @if($admitCard->section)
                    <div class="info-row">
                        <span class="info-label">Section:</span>
                        <span>{{ $admitCard->section->section_name }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-label">Exam:</span>
                        <span>{{ $admitCard->exam->exam_name ?? 'N/A' }}</span>
                    </div>

                    @if(isset($showExamSchedule) && $showExamSchedule && $admitCard->exam_details)
                    @php
                        $examDetails = is_string($admitCard->exam_details) ? json_decode($admitCard->exam_details, true) : $admitCard->exam_details;
                        // Deduplicate by subject name
                        $seenSubjects = [];
                        $uniqueDetails = [];
                        if (is_array($examDetails)) {
                            foreach($examDetails as $detail) {
                                $subject = $detail['subject'] ?? 'N/A';
                                if (!in_array($subject, $seenSubjects)) {
                                    $seenSubjects[] = $subject;
                                    $uniqueDetails[] = $detail;
                                }
                            }
                        }
                    @endphp
                    @if(count($uniqueDetails) > 0)
                    <div class="exam-schedule">
                        <div class="exam-schedule-title">Examination Schedule</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($uniqueDetails as $detail)
                                <tr>
                                    <td>{{ $detail['subject'] ?? 'N/A' }}</td>
                                    <td>{{ $detail['date'] ?? 'N/A' }}</td>
                                    <td>{{ $detail['time'] ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    @endif
                </div>
                <div class="photo-container">
                    <div class="photo-placeholder">Photo</div>
                    @if(isset($showQrCode) && $showQrCode && $admitCard->qr_code)
                    <div class="qr-code">
                        <img src="data:image/png;base64,{{ $admitCard->qr_code }}" alt="QR Code">
                    </div>
                    @elseif(isset($showQrCode) && $showQrCode)
                    <div class="qr-code">
                        QR Code
                    </div>
                    @endif
                </div>
            </div>
            @if(isset($showPrincipalStamp) && ($showPrincipalStamp || $showClassTeacherSign || $showSupervisorSign))
            <div class="signatures">
                @if($showPrincipalStamp)
                <div class="signature-box">
                    <div class="stamp-placeholder">Principal Stamp</div>
                </div>
                @endif
                @if($showClassTeacherSign)
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p class="signature-label">Class Teacher</p>
                </div>
                @endif
                @if($showSupervisorSign)
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p class="signature-label">Exam Supervisor</p>
                </div>
                @endif
            </div>
            @endif
            <p class="card-footer">
                Generated on: {{ $admitCard->generated_at ? $admitCard->generated_at->format('d M Y') : date('d M Y') }}
            </p>
        </div>
    </div>

    <script>
        function updatePrint() {
            const showExamSchedule = document.getElementById('show_exam_schedule')?.checked ? '1' : '0';
            const showQrCode = document.getElementById('show_qr_code')?.checked ? '1' : '0';
            const showPrincipalStamp = document.getElementById('show_principal_stamp')?.checked ? '1' : '0';
            const showClassTeacherSign = document.getElementById('show_class_teacher_sign')?.checked ? '1' : '0';
            const showSupervisorSign = document.getElementById('show_supervisor_sign')?.checked ? '1' : '0';

            const url = new URL(window.location);
            url.searchParams.set('show_exam_schedule', showExamSchedule);
            url.searchParams.set('show_qr_code', showQrCode);
            url.searchParams.set('show_principal_stamp', showPrincipalStamp);
            url.searchParams.set('show_class_teacher_sign', showClassTeacherSign);
            url.searchParams.set('show_supervisor_sign', showSupervisorSign);

            window.location.href = url.toString();
        }

        // Auto-print when options are selected via URL parameters
        if (window.location.search.includes('show_principal_stamp') ||
            window.location.search.includes('show_class_teacher_sign') ||
            window.location.search.includes('show_supervisor_sign')) {
            // Options were set, ready to print
            setTimeout(() => {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
