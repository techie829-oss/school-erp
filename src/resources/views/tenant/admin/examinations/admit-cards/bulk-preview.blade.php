<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Cards PDF Preview</title>
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
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }

        .print-button:hover {
            background-color: #1d4ed8;
        }

        .print-icon {
            width: 20px;
            height: 20px;
        }

        .a4-page {
            width: 210mm;
            height: 297mm;
            background: white;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.15);
            padding: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .admit-card {
            border: 1px solid #d1d5db;
            background: white;
            page-break-inside: avoid;
            display: flex;
            flex-direction: column;
            padding: 20px;
            color: #111827;
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

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            height: 100%;
        }

        .grid-4 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: repeat(2, 1fr);
            gap: 0;
            height: 100%;
        }

        #pdfContainer {
            padding-bottom: 40px;
        }

        @page {
            size: A4;
            margin: 0;
        }
    </style>
</head>
<body>
    <!-- Control Panel -->
    <div class="no-print">
        <div class="header-row">
            <h1 class="header-title">Bulk Export Admit Cards</h1>
            <div style="display: flex; gap: 10px;">
                <a href="{{ url('/admin/examinations/admit-cards/bulk-actions?' . http_build_query($filters)) }}" style="background-color: #6b7280; color: white; padding: 8px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block;">
                    ← Back
                </a>
                <button onclick="downloadPDF()" style="background-color: #10b981; color: white; padding: 8px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    Download PDF
                </button>
                <button onclick="window.print()" class="print-button">
                    <svg class="print-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2z"></path>
                    </svg>
                    Print / Export PDF
                </button>
            </div>
        </div>
    </div>

    <!-- PDF Preview -->
    <div id="pdfContainer">
        @php
            $chunks = $admitCards->chunk($cardsPerPage);
        @endphp

        @foreach($chunks as $chunk)
            <div class="a4-page">
                <div class="grid-{{ $cardsPerPage }}">
                    @foreach($chunk as $admitCard)
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

                                    @if($showExamSchedule && $admitCard->exam_details)
                                    @php
                                        $examDetails = is_string($admitCard->exam_details) ? json_decode($admitCard->exam_details, true) : $admitCard->exam_details;
                                        // Deduplicate by subject name
                                        $seenSubjects = [];
                                        $uniqueDetails = [];
                                        foreach($examDetails as $detail) {
                                            $subject = $detail['subject'] ?? 'N/A';
                                            if (!in_array($subject, $seenSubjects)) {
                                                $seenSubjects[] = $subject;
                                                $uniqueDetails[] = $detail;
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
                                    @if($showQrCode)
                                    <div class="qr-code">
                                        @if($admitCard->qr_code)
                                            <img src="data:image/png;base64,{{ $admitCard->qr_code }}" alt="QR Code">
                                        @else
                                            QR Code
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @if($showPrincipalStamp || $showClassTeacherSign || $showSupervisorSign)
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
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <form id="downloadForm" action="{{ url('/admin/examinations/admit-cards/bulk-export') }}" method="POST" style="display: none;">
        @csrf
        @foreach($filters as $key => $value)
            @if(is_array($value))
                @foreach($value as $item)
                    <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                @endforeach
            @else
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach
        <input type="hidden" name="cards_per_page" value="{{ $cardsPerPage }}">
        <input type="hidden" name="show_exam_schedule" value="{{ $showExamSchedule ? '1' : '0' }}">
        <input type="hidden" name="show_qr_code" value="{{ $showQrCode ? '1' : '0' }}">
        <input type="hidden" name="show_principal_stamp" value="{{ $showPrincipalStamp ? '1' : '0' }}">
        <input type="hidden" name="show_class_teacher_sign" value="{{ $showClassTeacherSign ? '1' : '0' }}">
        <input type="hidden" name="show_supervisor_sign" value="{{ $showSupervisorSign ? '1' : '0' }}">
    </form>

    <script>
        function downloadPDF() {
            document.getElementById('downloadForm').submit();
        }
    </script>
</body>
</html>
