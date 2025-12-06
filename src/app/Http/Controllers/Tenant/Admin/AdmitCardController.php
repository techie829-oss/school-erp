<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmitCard;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdmitCardController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of admit cards
     */
    public function index(Request $request)
    {
        // Try to get tenant from request attributes (set by middleware) first
        $tenant = $request->attributes->get('current_tenant');

        // Fallback to getting from service
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $query = AdmitCard::forTenant($tenant->id)
            ->with(['exam', 'student', 'schoolClass', 'section']);

        // Filter by exam
        if ($request->has('exam_id') && $request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }

        // Filter by class
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by section
        if ($request->has('section_id') && $request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        // Search by hall ticket number or student name
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('hall_ticket_number', 'like', '%' . $request->search . '%')
                  ->orWhere('student_name', 'like', '%' . $request->search . '%')
                  ->orWhere('admission_number', 'like', '%' . $request->search . '%');
            });
        }

        $admitCards = $query->latest('generated_at')->paginate(20)->withQueryString();

        $exams = Exam::forTenant($tenant->id)->where('status', '!=', 'archived')->get();
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.examinations.admit-cards.index', compact('admitCards', 'exams', 'classes', 'tenant'));
    }

    /**
     * Show bulk actions page (no pagination - all filtered results)
     */
    public function bulkActions(Request $request)
    {
        // Try to get tenant from request attributes (set by middleware) first
        $tenant = $request->attributes->get('current_tenant');

        // Fallback to getting from service
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        // Require at least exam filter to access bulk actions
        if (!$request->has('exam_id') || !$request->exam_id) {
            return redirect(url('/admin/examinations/admit-cards'))
                ->with('error', 'Please select an exam filter first before accessing bulk actions.');
        }

        $query = AdmitCard::forTenant($tenant->id)
            ->with(['exam', 'student', 'schoolClass', 'section']);

        // Filter by exam
        if ($request->has('exam_id') && $request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }

        // Filter by class
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by section
        if ($request->has('section_id') && $request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        // Search by hall ticket number or student name
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('hall_ticket_number', 'like', '%' . $request->search . '%')
                  ->orWhere('student_name', 'like', '%' . $request->search . '%')
                  ->orWhere('admission_number', 'like', '%' . $request->search . '%');
            });
        }

        // Get ALL results without pagination for bulk actions
        $admitCards = $query->orderBy('hall_ticket_number')->get();

        $exams = Exam::forTenant($tenant->id)->where('status', '!=', 'archived')->get();
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.examinations.admit-cards.bulk-actions', compact('admitCards', 'exams', 'classes', 'tenant'));
    }

    /**
     * Generate admit cards for an exam
     */
    public function generate(Request $request)
    {
        // Try to get tenant from request attributes (set by middleware) first
        $tenant = $request->attributes->get('current_tenant');

        // Fallback to getting from service
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $examId = $request->get('exam_id');
        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');

        // If no exam_id provided, show exam selection
        if (!$examId) {
            $exams = Exam::forTenant($tenant->id)->where('status', '!=', 'archived')->get();
            return view('tenant.admin.examinations.admit-cards.generate', compact('exams', 'tenant'));
        }

        $exam = Exam::forTenant($tenant->id)->findOrFail($examId);
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.examinations.admit-cards.generate', compact('exam', 'classes', 'tenant'));
    }

    /**
     * Store generated admit cards
     */
    public function store(Request $request)
    {
        // Try to get tenant from request attributes (set by middleware) first
        $tenant = $request->attributes->get('current_tenant');

        // Fallback to getting from service
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $request->validate([
            'exam_id' => [
                'required',
                Rule::exists('exams', 'id')->where('tenant_id', $tenant->id),
            ],
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')->where('tenant_id', $tenant->id),
            ],
            'section_id' => [
                'nullable',
                Rule::exists('sections', 'id')->where('tenant_id', $tenant->id),
            ],
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => Rule::exists('students', 'id')->where('tenant_id', $tenant->id),
        ]);

        try {
            DB::beginTransaction();

            $exam = Exam::forTenant($tenant->id)->findOrFail($request->exam_id);
            $schedules = ExamSchedule::forTenant($tenant->id)
                ->where('exam_id', $exam->id)
                ->where('class_id', $request->class_id)
                ->where(function($q) use ($request) {
                    if ($request->section_id) {
                        $q->where('section_id', $request->section_id)
                          ->orWhereNull('section_id');
                    }
                })
                ->with(['subject'])
                ->get();

            if ($schedules->isEmpty()) {
                return back()->with('error', 'No exam schedules found for the selected exam and class/section. Please create exam schedules first.');
            }

            // Deduplicate by subject_id (keep first occurrence)
            $seenSubjects = [];
            $uniqueSchedules = $schedules->filter(function($schedule) use (&$seenSubjects) {
                $subjectId = $schedule->subject_id;
                if (!in_array($subjectId, $seenSubjects)) {
                    $seenSubjects[] = $subjectId;
                    return true;
                }
                return false;
            })->values();

            $generated = 0;
            $skipped = 0;

            foreach ($request->student_ids as $studentId) {
                // Check if admit card already exists
                $existing = AdmitCard::forTenant($tenant->id)
                    ->where('exam_id', $exam->id)
                    ->where('student_id', $studentId)
                    ->first();

                if ($existing) {
                    $skipped++;
                    continue;
                }

                $student = Student::forTenant($tenant->id)->findOrFail($studentId);
                $enrollment = $student->currentEnrollment;

                if (!$enrollment) {
                    $skipped++;
                    continue;
                }

                // Generate hall ticket number
                $hallTicketNumber = $this->generateHallTicketNumber($tenant->id, $exam->id, $studentId);

                // Prepare exam details JSON from unique schedules
                $examDetails = $uniqueSchedules->map(function($schedule) {
                    $examDate = $schedule->exam_date instanceof \Carbon\Carbon
                        ? $schedule->exam_date
                        : \Carbon\Carbon::parse($schedule->exam_date);

                    $startTime = $schedule->start_time instanceof \Carbon\Carbon
                        ? $schedule->start_time
                        : \Carbon\Carbon::parse($schedule->start_time);

                    $endTime = $schedule->end_time instanceof \Carbon\Carbon
                        ? $schedule->end_time
                        : \Carbon\Carbon::parse($schedule->end_time);

                    return [
                        'subject' => $schedule->subject->subject_name ?? 'N/A',
                        'date' => $examDate->toDateString(),
                        'time' => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
                        'room' => $schedule->room_number ?? 'N/A',
                    ];
                })->toArray();

                // Generate QR code with attendance info
                $qrCodeData = [
                    'hall_ticket' => $hallTicketNumber,
                    'student_id' => $student->id,
                    'admission_number' => $student->admission_number,
                    'exam_id' => $exam->id,
                    'exam_name' => $exam->exam_name,
                    'class_id' => $enrollment->class_id,
                    'section_id' => $enrollment->section_id,
                    'tenant_id' => $tenant->id,
                ];
                $qrCodeBase64 = $this->generateQRCode(json_encode($qrCodeData));

                // Log if QR code generation failed
                if (empty($qrCodeBase64)) {
                    \Log::warning("QR code generation failed for student ID: {$student->id}, Hall Ticket: {$hallTicketNumber}");
                }

                AdmitCard::create([
                    'tenant_id' => $tenant->id,
                    'exam_id' => $exam->id,
                    'student_id' => $studentId,
                    'class_id' => $enrollment->class_id,
                    'section_id' => $enrollment->section_id,
                    'hall_ticket_number' => $hallTicketNumber,
                    'student_name' => $student->full_name,
                    'admission_number' => $student->admission_number,
                    'roll_number' => $enrollment->roll_number,
                    'photo_path' => $student->photo,
                    'exam_details_json' => $examDetails,
                    'qr_code' => $qrCodeBase64,
                    'generated_by' => auth()->id(),
                    'generated_at' => now(),
                ]);

                $generated++;
            }

            DB::commit();

            $message = "Admit cards generated successfully! ";
            if ($generated > 0) {
                $message .= "Generated: {$generated}. ";
            }
            if ($skipped > 0) {
                $message .= "Skipped (already exist): {$skipped}.";
            }

            return redirect(url('/admin/examinations/admit-cards'))->with('success', trim($message));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to generate admit cards: ' . $e->getMessage());
        }
    }

    /**
     * Print admit card
     */
    public function print(Request $request, $id)
    {
        // Try to get tenant from request attributes (set by middleware) first
        $tenant = $request->attributes->get('current_tenant');

        // Fallback to getting from service
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $admitCard = AdmitCard::forTenant($tenant->id)
            ->with(['exam', 'student', 'schoolClass', 'section'])
            ->findOrFail($id);

        // Get signature/stamp options from request (default to false)
        $showPrincipalStamp = $request->boolean('show_principal_stamp', false);
        $showClassTeacherSign = $request->boolean('show_class_teacher_sign', false);
        $showSupervisorSign = $request->boolean('show_supervisor_sign', false);
        $showExamSchedule = $request->boolean('show_exam_schedule', true);
        $showQrCode = $request->boolean('show_qr_code', true);

        // Mark as printed
        if (!$admitCard->is_printed) {
            $admitCard->update([
                'is_printed' => true,
                'printed_at' => now(),
            ]);
        }

        return view('tenant.admin.examinations.admit-cards.print', [
            'admitCard' => $admitCard,
            'tenant' => $tenant,
            'showPrincipalStamp' => $showPrincipalStamp,
            'showClassTeacherSign' => $showClassTeacherSign,
            'showSupervisorSign' => $showSupervisorSign,
            'showExamSchedule' => $showExamSchedule,
            'showQrCode' => $showQrCode,
        ]);
    }

    /**
     * Delete an admit card
     */
    public function destroy(Request $request, $id)
    {
        // Try to get tenant from request attributes (set by middleware) first
        $tenant = $request->attributes->get('current_tenant');

        // Fallback to getting from service
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $admitCard = AdmitCard::forTenant($tenant->id)->findOrFail($id);

        try {
            $admitCard->delete();

            return redirect(url('/admin/examinations/admit-cards'))
                ->with('success', 'Admit card deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete admit card: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete admit cards
     */
    public function bulkDestroy(Request $request)
    {
        // Try to get tenant from request attributes (set by middleware) first
        $tenant = $request->attributes->get('current_tenant');

        // Fallback to getting from service
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $request->validate([
            'admit_card_ids' => 'required|array|min:1',
            'admit_card_ids.*' => Rule::exists('admit_cards', 'id')->where('tenant_id', $tenant->id),
        ]);

        try {
            $admitCards = AdmitCard::forTenant($tenant->id)
                ->whereIn('id', $request->admit_card_ids)
                ->get();

            $deletedCount = 0;
            foreach ($admitCards as $admitCard) {
                $admitCard->delete();
                $deletedCount++;
            }

            return redirect(url('/admin/examinations/admit-cards'))
                ->with('success', $deletedCount . ' admit card(s) deleted successfully.');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Failed to delete admit cards: ' . $e->getMessage());
        }
    }

    /**
     * Preview bulk admit cards before export
     */
    public function bulkPreview(Request $request)
    {
        // Try to get tenant from request attributes (set by middleware) first
        $tenant = $request->attributes->get('current_tenant');

        // Fallback to getting from service
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $request->validate([
            'exam_id' => [
                'nullable',
                Rule::exists('exams', 'id')->where('tenant_id', $tenant->id),
            ],
            'class_id' => [
                'nullable',
                Rule::exists('classes', 'id')->where('tenant_id', $tenant->id),
            ],
            'section_id' => [
                'nullable',
                Rule::exists('sections', 'id')->where('tenant_id', $tenant->id),
            ],
            'admit_card_ids' => 'nullable|array',
            'admit_card_ids.*' => Rule::exists('admit_cards', 'id')->where('tenant_id', $tenant->id),
            'cards_per_page' => 'required|in:2,4',
            'export_scope' => 'required|in:selected,filtered',
            'show_principal_stamp' => 'nullable|boolean',
            'show_class_teacher_sign' => 'nullable|boolean',
            'show_supervisor_sign' => 'nullable|boolean',
        ]);

        $query = AdmitCard::forTenant($tenant->id)
            ->with(['exam', 'student', 'schoolClass', 'section']);

        // If export_scope is 'selected', only use admit_card_ids
        if ($request->export_scope === 'selected') {
            if (!$request->has('admit_card_ids') || !is_array($request->admit_card_ids) || count($request->admit_card_ids) === 0) {
                return back()->with('error', 'Please select at least one admit card to export.');
            }
            $query->whereIn('id', $request->admit_card_ids);
        } else {
            // If export_scope is 'filtered', use filters
            // Filter by exam
            if ($request->has('exam_id') && $request->exam_id) {
                $query->where('exam_id', $request->exam_id);
            }

            // Filter by class
            if ($request->has('class_id') && $request->class_id) {
                $query->where('class_id', $request->class_id);
            }

            // Filter by section
            if ($request->has('section_id') && $request->section_id) {
                $query->where('section_id', $request->section_id);
            }

            // Filter by search
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('hall_ticket_number', 'like', '%' . $request->search . '%')
                      ->orWhere('student_name', 'like', '%' . $request->search . '%')
                      ->orWhere('admission_number', 'like', '%' . $request->search . '%');
                });
            }
        }

        $admitCards = $query->orderBy('hall_ticket_number')->get();

        if ($admitCards->isEmpty()) {
            return back()->with('error', 'No admit cards found for the selected criteria.');
        }

        $cardsPerPage = $request->cards_per_page ?? 4;
        $showExamSchedule = $request->boolean('show_exam_schedule', true);
        $showQrCode = $request->boolean('show_qr_code', true);
        $showPrincipalStamp = $request->boolean('show_principal_stamp', false);
        $showClassTeacherSign = $request->boolean('show_class_teacher_sign', false);
        $showSupervisorSign = $request->boolean('show_supervisor_sign', false);

        return view('tenant.admin.examinations.admit-cards.bulk-preview', [
            'admitCards' => $admitCards,
            'tenant' => $tenant,
            'cardsPerPage' => $cardsPerPage,
            'showExamSchedule' => $showExamSchedule,
            'showQrCode' => $showQrCode,
            'showPrincipalStamp' => $showPrincipalStamp,
            'showClassTeacherSign' => $showClassTeacherSign,
            'showSupervisorSign' => $showSupervisorSign,
            'filters' => $request->only(['exam_id', 'class_id', 'section_id', 'search', 'admit_card_ids', 'export_scope']),
        ]);
    }

    /**
     * Bulk export admit cards to PDF
     */
    public function bulkExport(Request $request)
    {
        // Try to get tenant from request attributes (set by middleware) first
        $tenant = $request->attributes->get('current_tenant');

        // Fallback to getting from service
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $request->validate([
            'exam_id' => [
                'nullable',
                Rule::exists('exams', 'id')->where('tenant_id', $tenant->id),
            ],
            'class_id' => [
                'nullable',
                Rule::exists('classes', 'id')->where('tenant_id', $tenant->id),
            ],
            'section_id' => [
                'nullable',
                Rule::exists('sections', 'id')->where('tenant_id', $tenant->id),
            ],
            'admit_card_ids' => 'nullable|array',
            'admit_card_ids.*' => Rule::exists('admit_cards', 'id')->where('tenant_id', $tenant->id),
            'cards_per_page' => 'required|in:2,4',
            'export_scope' => 'required|in:selected,filtered',
            'show_exam_schedule' => 'nullable|boolean',
            'show_qr_code' => 'nullable|boolean',
            'show_principal_stamp' => 'nullable|boolean',
            'show_class_teacher_sign' => 'nullable|boolean',
            'show_supervisor_sign' => 'nullable|boolean',
        ]);

        $query = AdmitCard::forTenant($tenant->id)
            ->with(['exam', 'student', 'schoolClass', 'section']);

        // If export_scope is 'selected', only use admit_card_ids
        if ($request->export_scope === 'selected') {
            if (!$request->has('admit_card_ids') || !is_array($request->admit_card_ids) || count($request->admit_card_ids) === 0) {
                return back()->with('error', 'Please select at least one admit card to export.');
            }
            $query->whereIn('id', $request->admit_card_ids);
        } else {
            // If export_scope is 'filtered', use filters
            // Filter by exam
            if ($request->has('exam_id') && $request->exam_id) {
                $query->where('exam_id', $request->exam_id);
            }

            // Filter by class
            if ($request->has('class_id') && $request->class_id) {
                $query->where('class_id', $request->class_id);
            }

            // Filter by section
            if ($request->has('section_id') && $request->section_id) {
                $query->where('section_id', $request->section_id);
            }
        }

        $admitCards = $query->orderBy('hall_ticket_number')->get();

        if ($admitCards->isEmpty()) {
            return back()->with('error', 'No admit cards found for the selected criteria.');
        }

        $cardsPerPage = $request->cards_per_page ?? 4;
        $showExamSchedule = $request->boolean('show_exam_schedule', true);
        $showQrCode = $request->boolean('show_qr_code', true);
        $showPrincipalStamp = $request->boolean('show_principal_stamp', false);
        $showClassTeacherSign = $request->boolean('show_class_teacher_sign', false);
        $showSupervisorSign = $request->boolean('show_supervisor_sign', false);

        $pdf = Pdf::loadView('tenant.admin.examinations.admit-cards.bulk-print', [
            'admitCards' => $admitCards,
            'tenant' => $tenant,
            'cardsPerPage' => $cardsPerPage,
            'showExamSchedule' => $showExamSchedule,
            'showQrCode' => $showQrCode,
            'showPrincipalStamp' => $showPrincipalStamp,
            'showClassTeacherSign' => $showClassTeacherSign,
            'showSupervisorSign' => $showSupervisorSign,
        ])->setPaper('a4', 'portrait');

        $filename = 'admit_cards_' . date('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Bulk generate admit cards
     */
    public function bulkGenerate(Request $request)
    {
        // Try to get tenant from request attributes (set by middleware) first
        $tenant = $request->attributes->get('current_tenant');

        // Fallback to getting from service
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $request->validate([
            'exam_id' => [
                'required',
                Rule::exists('exams', 'id')->where('tenant_id', $tenant->id),
            ],
            'class_id' => [
                'required',
                Rule::exists('classes', 'id')->where('tenant_id', $tenant->id),
            ],
            'section_id' => [
                'nullable',
                Rule::exists('sections', 'id')->where('tenant_id', $tenant->id),
            ],
            'generate_type' => 'nullable|in:all,missing',
        ]);

        try {
            DB::beginTransaction();

            $exam = Exam::forTenant($tenant->id)->findOrFail($request->exam_id);
            $selectedClass = SchoolClass::forTenant($tenant->id)->findOrFail($request->class_id);

            // Validation: Check if exam schedules exist for the selected class
            $schedulesCount = ExamSchedule::forTenant($tenant->id)
                ->where('exam_id', $exam->id)
                ->where('class_id', $request->class_id)
                ->count();

            if ($schedulesCount === 0) {
                return back()
                    ->withInput()
                    ->with('error', 'No exam schedules found for "' . $exam->exam_name . '" and "' . $selectedClass->class_name . '". Please verify the exam and class selection.');
            }

            // Get all students for the class/section
            $studentsQuery = Student::forTenant($tenant->id)
                ->whereHas('currentEnrollment', function($q) use ($request) {
                    $q->where('class_id', $request->class_id);
                    if ($request->section_id) {
                        $q->where('section_id', $request->section_id);
                    }
                })
                ->active();

            $students = $studentsQuery->get();

            if ($students->isEmpty()) {
                return back()->with('error', 'No students found for the selected class/section.');
            }

            $schedules = ExamSchedule::forTenant($tenant->id)
                ->where('exam_id', $exam->id)
                ->where('class_id', $request->class_id)
                ->where(function($q) use ($request) {
                    if ($request->section_id) {
                        $q->where('section_id', $request->section_id)
                          ->orWhereNull('section_id');
                    }
                })
                ->with(['subject'])
                ->get();

            // Deduplicate by subject_id (keep first occurrence)
            $seenSubjects = [];
            $uniqueSchedules = $schedules->filter(function($schedule) use (&$seenSubjects) {
                $subjectId = $schedule->subject_id;
                if (!in_array($subjectId, $seenSubjects)) {
                    $seenSubjects[] = $subjectId;
                    return true;
                }
                return false;
            })->values();

            $generated = 0;
            $skipped = 0;

            foreach ($students as $student) {
                // Check if admit card already exists
                $existing = AdmitCard::forTenant($tenant->id)
                    ->where('exam_id', $exam->id)
                    ->where('student_id', $student->id)
                    ->first();

                // If generate_type is 'missing' and card exists, skip
                // If generate_type is 'all' or not set, regenerate (delete old and create new)
                if ($existing) {
                    if ($request->generate_type === 'missing') {
                        $skipped++;
                        continue;
                    } else {
                        // Delete existing to regenerate
                        $existing->delete();
                    }
                }

                $enrollment = $student->currentEnrollment;
                if (!$enrollment) {
                    $skipped++;
                    continue;
                }

                // Generate hall ticket number
                $hallTicketNumber = $this->generateHallTicketNumber($tenant->id, $exam->id, $student->id);

                // Prepare exam details JSON from unique schedules
                $examDetails = $uniqueSchedules->map(function($schedule) {
                    $examDate = $schedule->exam_date instanceof \Carbon\Carbon
                        ? $schedule->exam_date
                        : \Carbon\Carbon::parse($schedule->exam_date);

                    $startTime = $schedule->start_time instanceof \Carbon\Carbon
                        ? $schedule->start_time
                        : \Carbon\Carbon::parse($schedule->start_time);

                    $endTime = $schedule->end_time instanceof \Carbon\Carbon
                        ? $schedule->end_time
                        : \Carbon\Carbon::parse($schedule->end_time);

                    return [
                        'subject' => $schedule->subject->subject_name ?? 'N/A',
                        'date' => $examDate->toDateString(),
                        'time' => $startTime->format('H:i') . ' - ' . $endTime->format('H:i'),
                        'room' => $schedule->room_number ?? 'N/A',
                    ];
                })->toArray();

                // Generate QR code with attendance info
                $qrCodeData = [
                    'hall_ticket' => $hallTicketNumber,
                    'student_id' => $student->id,
                    'admission_number' => $student->admission_number,
                    'exam_id' => $exam->id,
                    'exam_name' => $exam->exam_name,
                    'class_id' => $enrollment->class_id,
                    'section_id' => $enrollment->section_id,
                    'tenant_id' => $tenant->id,
                ];
                $qrCodeBase64 = $this->generateQRCode(json_encode($qrCodeData));

                // Log if QR code generation failed
                if (empty($qrCodeBase64)) {
                    \Log::warning("QR code generation failed for student ID: {$student->id}, Hall Ticket: {$hallTicketNumber}");
                }

                AdmitCard::create([
                    'tenant_id' => $tenant->id,
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'class_id' => $enrollment->class_id,
                    'section_id' => $enrollment->section_id,
                    'hall_ticket_number' => $hallTicketNumber,
                    'student_name' => $student->full_name,
                    'admission_number' => $student->admission_number,
                    'roll_number' => $enrollment->roll_number,
                    'photo_path' => $student->photo,
                    'exam_details_json' => $examDetails,
                    'qr_code' => $qrCodeBase64,
                    'generated_by' => auth()->id(),
                    'generated_at' => now(),
                ]);

                $generated++;
            }

            DB::commit();

            $message = "Bulk admit cards generated successfully! ";
            if ($generated > 0) {
                $message .= "Generated: {$generated}. ";
            }
            if ($skipped > 0) {
                $message .= "Skipped (already exist): {$skipped}.";
            }

            return redirect(url('/admin/examinations/admit-cards'))->with('success', trim($message));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to generate admit cards: ' . $e->getMessage());
        }
    }

    /**
     * Generate QR code as base64 string
     */
    private function generateQRCode($data)
    {
        try {
            // Generate QR code as PNG
            $qrCode = QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->errorCorrection('H')
                ->generate($data);

            // The generate() method returns raw binary data
            // Encode it to base64 for storage
            $base64 = base64_encode($qrCode);

            // Verify the base64 string is valid
            if (empty($base64) || strlen($base64) < 100) {
                \Log::warning('QR Code base64 encoding resulted in invalid data. Length: ' . strlen($base64));
                return '';
            }

            return $base64;
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('QR Code generation failed: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            // Return empty string if QR generation fails
            return '';
        }
    }

    /**
     * Generate unique hall ticket number
     */
    private function generateHallTicketNumber($tenantId, $examId, $studentId)
    {
        $exam = Exam::find($examId);
        $prefix = strtoupper(substr($exam->exam_name ?? 'EXAM', 0, 3));
        $year = date('Y');
        $number = str_pad($studentId, 6, '0', STR_PAD_LEFT);

        $hallTicketNumber = "{$prefix}-{$year}-{$number}";

        // Ensure uniqueness
        $counter = 1;
        while (AdmitCard::where('hall_ticket_number', $hallTicketNumber)->exists()) {
            $hallTicketNumber = "{$prefix}-{$year}-{$number}-{$counter}";
            $counter++;
        }

        return $hallTicketNumber;
    }
}

