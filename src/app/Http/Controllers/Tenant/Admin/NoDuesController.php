<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\NoDuesCertificate;
use App\Models\Student;
use App\Models\StudentFeeCard;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class NoDuesController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Helper to get tenant consistently
     */
    private function getTenant(Request $request)
    {
        $tenant = $request->attributes->get('current_tenant');
        if (!$tenant) {
            $tenant = $this->tenantService->getCurrentTenant($request);
        }
        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        return $tenant;
    }

    /**
     * Display a listing of no-dues certificates
     */
    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);

        $query = NoDuesCertificate::forTenant($tenant->id)
            ->with(['student', 'schoolClass', 'section']);

        // Filter by class
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by section
        if ($request->has('section_id') && $request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search by certificate number or student name
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('certificate_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('student', function($sq) use ($request) {
                      $sq->where('full_name', 'like', '%' . $request->search . '%')
                        ->orWhere('admission_number', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $certificates = $query->latest('issue_date')->paginate(20)->withQueryString();

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.fees.no-dues.index', compact('certificates', 'classes', 'tenant'));
    }

    /**
     * Show bulk actions page (no pagination - all filtered results)
     */
    public function bulkActions(Request $request)
    {
        $tenant = $this->getTenant($request);

        // Require at least class filter to access bulk actions
        if (!$request->has('class_id') || !$request->class_id) {
            return redirect(url('/admin/fees/no-dues'))
                ->with('error', 'Please select a class filter first before accessing bulk actions.');
        }

        $query = NoDuesCertificate::forTenant($tenant->id)
            ->with(['student', 'schoolClass', 'section']);

        // Filter by class
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by section
        if ($request->has('section_id') && $request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('certificate_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('student', function($sq) use ($request) {
                      $sq->where('full_name', 'like', '%' . $request->search . '%')
                        ->orWhere('admission_number', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Get ALL results without pagination for bulk actions
        $certificates = $query->orderBy('certificate_number')->get();

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.fees.no-dues.bulk-actions', compact('certificates', 'classes', 'tenant'));
    }

    /**
     * Generate no-dues certificates
     */
    public function generate(Request $request)
    {
        $tenant = $this->getTenant($request);

        $classId = $request->get('class_id');
        $sectionId = $request->get('section_id');

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        if ($classId) {
            $class = SchoolClass::forTenant($tenant->id)->findOrFail($classId);
            $sections = Section::forTenant($tenant->id)
                ->where('class_id', $classId)
                ->get();

            return view('tenant.admin.fees.no-dues.generate', compact('class', 'sections', 'classes', 'tenant'));
        }

        return view('tenant.admin.fees.no-dues.generate', compact('classes', 'tenant'));
    }

    /**
     * Store generated no-dues certificates
     */
    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'issue_date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $generated = 0;
            $skipped = 0;

            foreach ($request->student_ids as $studentId) {
                // Check if certificate already exists
                $existing = NoDuesCertificate::forTenant($tenant->id)
                    ->where('student_id', $studentId)
                    ->where('class_id', $request->class_id)
                    ->first();

                if ($existing) {
                    $skipped++;
                    continue;
                }

                // Check fee clearance
                $feeCards = StudentFeeCard::forTenant($tenant->id)
                    ->where('student_id', $studentId)
                    ->where('balance_amount', '>', 0)
                    ->exists();

                $feeClearance = !$feeCards;

                // Generate certificate number
                $certificateNumber = 'ND-' . strtoupper(Str::random(8));

                NoDuesCertificate::create([
                    'tenant_id' => $tenant->id,
                    'student_id' => $studentId,
                    'class_id' => $request->class_id,
                    'section_id' => $request->section_id,
                    'certificate_number' => $certificateNumber,
                    'issue_date' => $request->issue_date,
                    'remarks' => $request->remarks,
                    'fee_clearance' => $feeClearance,
                    'library_clearance' => true, // Default to true, can be updated later
                    'lab_clearance' => true,
                    'sports_clearance' => true,
                    'hostel_clearance' => true,
                    'status' => $feeClearance ? 'approved' : 'pending',
                    'generated_by' => auth()->id(),
                    'generated_at' => now(),
                ]);

                $generated++;
            }

            DB::commit();

            $message = "Generated {$generated} no-dues certificate(s)";
            if ($skipped > 0) {
                $message .= ", {$skipped} already exist(s)";
            }

            return redirect(url('/admin/fees/no-dues'))
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to generate certificates: ' . $e->getMessage());
        }
    }

    /**
     * Bulk generate no-dues certificates
     */
    public function bulkGenerate(Request $request)
    {
        $tenant = $this->getTenant($request);

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'generate_type' => 'required|in:all,missing',
            'issue_date' => 'required|date',
            'remarks' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $class = SchoolClass::forTenant($tenant->id)->findOrFail($request->class_id);

            // Get students
            $studentsQuery = Student::forTenant($tenant->id)
                ->whereHas('currentEnrollment', function($q) use ($request) {
                    $q->where('class_id', $request->class_id);
                    if ($request->section_id) {
                        $q->where('section_id', $request->section_id);
                    }
                });

            $students = $studentsQuery->get();

            if ($students->isEmpty()) {
                return back()->with('error', 'No students found for the selected class/section.');
            }

            $generated = 0;
            $skipped = 0;

            foreach ($students as $student) {
                if ($request->generate_type === 'missing') {
                    $existing = NoDuesCertificate::forTenant($tenant->id)
                        ->where('student_id', $student->id)
                        ->where('class_id', $request->class_id)
                        ->exists();

                    if ($existing) {
                        $skipped++;
                        continue;
                    }
                }

                // Check fee clearance
                $feeCards = StudentFeeCard::forTenant($tenant->id)
                    ->where('student_id', $student->id)
                    ->where('balance_amount', '>', 0)
                    ->exists();

                $feeClearance = !$feeCards;

                // Generate certificate number
                $certificateNumber = 'ND-' . strtoupper(Str::random(8));

                NoDuesCertificate::create([
                    'tenant_id' => $tenant->id,
                    'student_id' => $student->id,
                    'class_id' => $request->class_id,
                    'section_id' => $request->section_id,
                    'certificate_number' => $certificateNumber,
                    'issue_date' => $request->issue_date,
                    'remarks' => $request->remarks,
                    'fee_clearance' => $feeClearance,
                    'library_clearance' => true,
                    'lab_clearance' => true,
                    'sports_clearance' => true,
                    'hostel_clearance' => true,
                    'status' => $feeClearance ? 'approved' : 'pending',
                    'generated_by' => auth()->id(),
                    'generated_at' => now(),
                ]);

                $generated++;
            }

            DB::commit();

            $message = "Generated {$generated} no-dues certificate(s)";
            if ($skipped > 0) {
                $message .= ", {$skipped} already exist(s)";
            }

            return redirect(url('/admin/fees/no-dues'))
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to generate certificates: ' . $e->getMessage());
        }
    }

    /**
     * Print single no-dues certificate
     */
    public function print(Request $request, $id)
    {
        $tenant = $this->getTenant($request);

        $certificate = NoDuesCertificate::forTenant($tenant->id)
            ->with(['student', 'schoolClass', 'section'])
            ->findOrFail($id);

        $showPrincipalStamp = $request->boolean('show_principal_stamp', false);
        $showClassTeacherSign = $request->boolean('show_class_teacher_sign', false);
        $showAccountantSign = $request->boolean('show_accountant_sign', false);

        return view('tenant.admin.fees.no-dues.print', compact(
            'certificate',
            'tenant',
            'showPrincipalStamp',
            'showClassTeacherSign',
            'showAccountantSign'
        ));
    }

    /**
     * Bulk preview no-dues certificates
     */
    public function bulkPreview(Request $request)
    {
        $tenant = $this->getTenant($request);

        $request->validate([
            'certificate_ids' => 'required|array|min:1',
            'certificate_ids.*' => 'exists:no_dues_certificates,id',
            'cards_per_page' => 'required|in:1,2,4',
            'show_principal_stamp' => 'boolean',
            'show_class_teacher_sign' => 'boolean',
            'show_accountant_sign' => 'boolean',
        ]);

        $certificates = NoDuesCertificate::forTenant($tenant->id)
            ->whereIn('id', $request->certificate_ids)
            ->with(['student', 'schoolClass', 'section'])
            ->orderBy('certificate_number')
            ->get();

        if ($certificates->isEmpty()) {
            return back()->with('error', 'No certificates selected.');
        }

        $showPrincipalStamp = $request->boolean('show_principal_stamp', false);
        $showClassTeacherSign = $request->boolean('show_class_teacher_sign', false);
        $showAccountantSign = $request->boolean('show_accountant_sign', false);

        return view('tenant.admin.fees.no-dues.bulk-preview', compact(
            'certificates',
            'tenant',
            'showPrincipalStamp',
            'showClassTeacherSign',
            'showAccountantSign'
        ));
    }

    /**
     * Bulk export no-dues certificates as PDF
     */
    public function bulkExport(Request $request)
    {
        $tenant = $this->getTenant($request);

        $request->validate([
            'certificate_ids' => 'required|array|min:1',
            'certificate_ids.*' => 'exists:no_dues_certificates,id',
            'cards_per_page' => 'required|in:1,2,4',
            'show_principal_stamp' => 'boolean',
            'show_class_teacher_sign' => 'boolean',
            'show_accountant_sign' => 'boolean',
        ]);

        $certificates = NoDuesCertificate::forTenant($tenant->id)
            ->whereIn('id', $request->certificate_ids)
            ->with(['student', 'schoolClass', 'section'])
            ->orderBy('certificate_number')
            ->get();

        if ($certificates->isEmpty()) {
            return back()->with('error', 'No certificates selected.');
        }

        $showPrincipalStamp = $request->boolean('show_principal_stamp', false);
        $showClassTeacherSign = $request->boolean('show_class_teacher_sign', false);
        $showAccountantSign = $request->boolean('show_accountant_sign', false);

        $pdf = Pdf::loadView('tenant.admin.fees.no-dues.bulk-print', compact(
            'certificates',
            'tenant',
            'showPrincipalStamp',
            'showClassTeacherSign',
            'showAccountantSign'
        ))->setPaper('a4', 'portrait');

        $filename = 'no-dues-certificates-' . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Delete single certificate
     */
    public function destroy($id)
    {
        $tenant = $this->getTenant(request());

        $certificate = NoDuesCertificate::forTenant($tenant->id)->findOrFail($id);
        $certificate->delete();

        return redirect(url('/admin/fees/no-dues'))
            ->with('success', 'Certificate deleted successfully.');
    }

    /**
     * Bulk delete certificates
     */
    public function bulkDestroy(Request $request)
    {
        $tenant = $this->getTenant($request);

        $request->validate([
            'certificate_ids' => 'required|array|min:1',
            'certificate_ids.*' => 'exists:no_dues_certificates,id',
        ]);

        $deleted = NoDuesCertificate::forTenant($tenant->id)
            ->whereIn('id', $request->certificate_ids)
            ->delete();

        return redirect(url('/admin/fees/no-dues'))
            ->with('success', "Deleted {$deleted} certificate(s) successfully.");
    }
}
