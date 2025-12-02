<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Cards - Bulk Export</title>
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
            page-break-after: always;
            position: relative;
        }

        .page:last-child {
            page-break-after: auto;
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

        /* For 4 cards per page, reduce sizes */
        .grid-4 .admit-card {
            padding: 15px;
        }

        .grid-4 .school-name {
            font-size: 12px;
        }

        .grid-4 .card-title {
            font-size: 10px;
        }

        .grid-4 .card-content {
            font-size: 10px;
            gap: 8px;
        }

        .grid-4 .info-row {
            margin-bottom: 3px;
        }

        .grid-4 .photo-placeholder {
            width: 28mm;
            height: 35mm;
            font-size: 8px;
        }

        .grid-4 .qr-code {
            width: 20mm;
            height: 20mm;
            font-size: 7px;
        }

        .grid-4 .exam-schedule-title {
            font-size: 9px;
            padding: 3px 6px;
        }

        .grid-4 .exam-schedule table {
            font-size: 8px;
        }

        .grid-4 .exam-schedule th,
        .grid-4 .exam-schedule td {
            padding: 3px;
        }

        .grid-4 .signature-line {
            height: 30px;
        }

        .grid-4 .signature-label {
            font-size: 10px;
        }

        .grid-4 .stamp-placeholder {
            min-height: 30px;
            font-size: 8px;
            padding: 6px;
        }

        .grid-4 .card-footer {
            font-size: 10px;
        }
    </style>
</head>
<body>
    @php
        $cardsPerPage = isset($cardsPerPage) ? $cardsPerPage : 4;
        $chunks = $admitCards->chunk($cardsPerPage);
    @endphp

    @foreach($chunks as $chunk)
        <div class="page">
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

                                @if(isset($showExamSchedule) && $showExamSchedule && $admitCard->exam_details)
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
                                @if(isset($showQrCode) && $showQrCode)
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
</body>
</html>

