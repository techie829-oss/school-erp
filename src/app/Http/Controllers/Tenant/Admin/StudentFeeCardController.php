<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentFeeCard;
use App\Models\StudentFeeItem;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentFeeCardController extends Controller
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
     * Display listing of fee cards
     */
    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);

        $query = Student::forTenant($tenant->id)
            ->whereHas('feeCards')
            ->with(['currentEnrollment.schoolClass', 'currentEnrollment.section', 'feeCards']);

        // Filter by class
        if ($request->has('class_id') && $request->class_id) {
            $query->whereHas('currentEnrollment', function($q) use ($request) {
                $q->where('class_id', $request->class_id)->where('is_current', true);
            });
        }

        // Filter by section
        if ($request->has('section_id') && $request->section_id) {
            $query->whereHas('currentEnrollment', function($q) use ($request) {
                $q->where('section_id', $request->section_id)->where('is_current', true);
            });
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('admission_number', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->orderBy('full_name')->paginate(20)->withQueryString();

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.fees.cards.index', compact('students', 'classes', 'tenant'));
    }

    /**
     * Show bulk actions page (no pagination - all filtered results)
     */
    public function bulkActions(Request $request)
    {
        $tenant = $this->getTenant($request);

        // Require at least class filter to access bulk actions
        if (!$request->has('class_id') || !$request->class_id) {
            return redirect(url('/admin/fees/cards'))
                ->with('error', 'Please select a class filter first before accessing bulk actions.');
        }

        $query = Student::forTenant($tenant->id)
            ->whereHas('feeCards')
            ->with(['currentEnrollment.schoolClass', 'currentEnrollment.section', 'feeCards']);

        // Filter by class
        if ($request->has('class_id') && $request->class_id) {
            $query->whereHas('currentEnrollment', function($q) use ($request) {
                $q->where('class_id', $request->class_id)->where('is_current', true);
            });
        }

        // Filter by section
        if ($request->has('section_id') && $request->section_id) {
            $query->whereHas('currentEnrollment', function($q) use ($request) {
                $q->where('section_id', $request->section_id)->where('is_current', true);
            });
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('admission_number', 'like', '%' . $request->search . '%');
            });
        }

        // Get ALL results without pagination for bulk actions
        $students = $query->orderBy('full_name')->get();

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.fees.cards.bulk-actions', compact('students', 'classes', 'tenant'));
    }

    /**
     * Bulk preview fee cards
     */
    public function bulkPreview(Request $request)
    {
        $tenant = $this->getTenant($request);

        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'cards_per_page' => 'required|in:1,2,4',
            'show_principal_stamp' => 'boolean',
            'show_accountant_sign' => 'boolean',
        ]);

        $students = Student::forTenant($tenant->id)
            ->whereIn('id', $request->student_ids)
            ->with([
                'currentEnrollment.schoolClass',
                'currentEnrollment.section',
                'feeCards.feePlan',
                'feeCards.feeItems.feeComponent'
            ])
            ->orderBy('full_name')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No students selected.');
        }

        $showPrincipalStamp = $request->boolean('show_principal_stamp', false);
        $showAccountantSign = $request->boolean('show_accountant_sign', false);

        return view('tenant.admin.fees.cards.bulk-preview', compact(
            'students',
            'tenant',
            'showPrincipalStamp',
            'showAccountantSign'
        ));
    }

    /**
     * Bulk export fee cards as PDF
     */
    public function bulkExport(Request $request)
    {
        $tenant = $this->getTenant($request);

        $request->validate([
            'student_ids' => 'required|array|min:1',
            'student_ids.*' => 'exists:students,id',
            'cards_per_page' => 'required|in:1,2,4',
            'show_principal_stamp' => 'boolean',
            'show_accountant_sign' => 'boolean',
        ]);

        $students = Student::forTenant($tenant->id)
            ->whereIn('id', $request->student_ids)
            ->with([
                'currentEnrollment.schoolClass',
                'currentEnrollment.section',
                'feeCards.feePlan',
                'feeCards.feeItems.feeComponent'
            ])
            ->orderBy('full_name')
            ->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'No students selected.');
        }

        $showPrincipalStamp = $request->boolean('show_principal_stamp', false);
        $showAccountantSign = $request->boolean('show_accountant_sign', false);

        $pdf = Pdf::loadView('tenant.admin.fees.cards.bulk-print', compact(
            'students',
            'tenant',
            'showPrincipalStamp',
            'showAccountantSign'
        ))->setPaper('a4', 'portrait');

        $filename = 'fee-cards-' . now()->format('Y-m-d-His') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Display student fee card details
     */
    public function show(Request $request, $studentId)
    {
        $tenant = $this->getTenant($request);

        $student = Student::forTenant($tenant->id)
            ->with([
                'currentEnrollment.schoolClass',
                'currentEnrollment.section',
            ])
            ->findOrFail($studentId);

        // Get all fee cards for this student
        $feeCards = StudentFeeCard::forTenant($tenant->id)
            ->where('student_id', $studentId)
            ->with([
                'feePlan.schoolClass',
                'feeItems.feeComponent'
            ])
            ->orderBy('academic_year', 'desc')
            ->get();

        // Get payment history
        $payments = Payment::forTenant($tenant->id)
            ->where('student_id', $studentId)
            ->where('status', 'success')
            ->orderBy('payment_date', 'desc')
            ->get();

        // Get invoices
        $invoices = Invoice::forTenant($tenant->id)
            ->where('student_id', $studentId)
            ->with('items.feeComponent')
            ->orderBy('invoice_date', 'desc')
            ->get();

        return view('tenant.admin.fees.cards.show', compact(
            'student',
            'feeCards',
            'payments',
            'invoices'
        ));
    }

    /**
     * Print fee card
     */
    public function print(Request $request, $studentId)
    {
        $tenant = $this->getTenant($request);

        $student = Student::forTenant($tenant->id)
            ->with([
                'currentEnrollment.schoolClass',
                'currentEnrollment.section',
            ])
            ->findOrFail($studentId);

        $feeCards = StudentFeeCard::forTenant($tenant->id)
            ->where('student_id', $studentId)
            ->with([
                'feePlan.schoolClass',
                'feeItems.feeComponent'
            ])
            ->orderBy('academic_year', 'desc')
            ->get();

        $showPrincipalStamp = $request->boolean('show_principal_stamp', false);
        $showAccountantSign = $request->boolean('show_accountant_sign', false);

        return view('tenant.admin.fees.cards.print', compact(
            'student',
            'feeCards',
            'tenant',
            'showPrincipalStamp',
            'showAccountantSign'
        ));
    }

    /**
     * Generate and download payment receipt
     */
    public function receipt(Request $request, $paymentId)
    {
        $tenant = $this->getTenant($request);

        $payment = Payment::forTenant($tenant->id)
            ->with([
                'student.currentEnrollment.schoolClass',
                'student.currentEnrollment.section',
                'invoice.items.feeComponent'
            ])
            ->findOrFail($paymentId);

        return view('tenant.admin.fees.receipts.show', compact('payment', 'tenant'));
    }

    /**
     * Download payment receipt as PDF
     */
    public function downloadReceipt(Request $request, $paymentId)
    {
        $tenant = $this->getTenant($request);

        $payment = Payment::forTenant($tenant->id)
            ->with([
                'student.currentEnrollment.schoolClass',
                'student.currentEnrollment.section',
                'invoice.items.feeComponent'
            ])
            ->findOrFail($paymentId);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('tenant.admin.fees.receipts.pdf', compact('payment', 'tenant'));

        return $pdf->download('receipt-' . $payment->payment_number . '.pdf');
    }

    /**
     * Apply discount to fee card
     */
    public function applyDiscount(Request $request, $feeCardId)
    {
        $tenant = $this->getTenant($request);

        $feeCard = StudentFeeCard::forTenant($tenant->id)->findOrFail($feeCardId);

        $validator = Validator::make($request->all(), [
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'discount_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $discountAmount = 0;

            if ($request->discount_type === 'percentage') {
                $discountAmount = ($feeCard->total_amount * $request->discount_value) / 100;
            } else {
                $discountAmount = $request->discount_value;
            }

            // Update fee card
            $feeCard->discount_amount = $discountAmount;
            $feeCard->balance_amount = $feeCard->total_amount - $discountAmount - $feeCard->paid_amount;
            $feeCard->save();

            // Distribute discount across fee items proportionally
            $totalOriginal = $feeCard->feeItems->sum('original_amount');

            foreach ($feeCard->feeItems as $item) {
                $itemDiscountRatio = $item->original_amount / $totalOriginal;
                $itemDiscount = $discountAmount * $itemDiscountRatio;

                $item->discount_amount = $itemDiscount;
                $item->discount_reason = $request->discount_reason;
                $item->net_amount = $item->original_amount - $itemDiscount;
                $item->save();
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Discount applied successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to apply discount: ' . $e->getMessage());
        }
    }

    /**
     * Waive specific fee component
     */
    public function waiveFee(Request $request, $feeItemId)
    {
        $tenant = $this->getTenant($request);

        $feeItem = StudentFeeItem::whereHas('studentFeeCard', function ($query) use ($tenant) {
            $query->where('tenant_id', $tenant->id);
        })->findOrFail($feeItemId);

        $validator = Validator::make($request->all(), [
            'waive_reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $feeItem->status = 'waived';
            $feeItem->discount_amount = $feeItem->original_amount;
            $feeItem->discount_reason = $request->waive_reason;
            $feeItem->net_amount = 0;
            $feeItem->save();

            // Update parent fee card
            $feeCard = $feeItem->studentFeeCard;
            $feeCard->discount_amount = $feeCard->feeItems->sum('discount_amount');
            $feeCard->balance_amount = $feeCard->total_amount - $feeCard->discount_amount - $feeCard->paid_amount;
            $feeCard->save();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Fee waived successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to waive fee: ' . $e->getMessage());
        }
    }

    /**
     * Calculate and apply late fee fine
     */
    public function applyLateFee(Request $request, $feeCardId)
    {
        $tenant = $this->getTenant($request);

        $feeCard = StudentFeeCard::forTenant($tenant->id)
            ->with('feeItems')
            ->findOrFail($feeCardId);

        DB::beginTransaction();
        try {
            $totalLateFee = 0;
            $today = now();

            foreach ($feeCard->feeItems as $item) {
                if ($item->due_date && $item->due_date < $today && $item->status !== 'paid' && $item->status !== 'waived') {
                    $daysLate = $today->diffInDays($item->due_date);

                    // Calculate late fee: 1% per month or ₹100, whichever is higher
                    $monthsLate = ceil($daysLate / 30);
                    $percentageFine = ($item->net_amount * $monthsLate) / 100;
                    $lateFee = max($percentageFine, 100 * $monthsLate);

                    $totalLateFee += $lateFee;
                }
            }

            if ($totalLateFee > 0) {
                // Create a late fee item or add to total
                $feeCard->total_amount += $totalLateFee;
                $feeCard->balance_amount += $totalLateFee;
                $feeCard->save();

                DB::commit();

                return redirect()->back()
                    ->with('success', 'Late fee of ₹' . number_format($totalLateFee, 2) . ' applied successfully!');
            } else {
                DB::rollBack();
                return redirect()->back()
                    ->with('info', 'No late fees applicable.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to apply late fee: ' . $e->getMessage());
        }
    }

    /**
     * Send payment reminder
     */
    public function sendReminder(Request $request, $studentId)
    {
        $tenant = $this->getTenant($request);

        $student = Student::forTenant($tenant->id)
            ->with('studentFeeCard')
            ->findOrFail($studentId);

        if (!$student->studentFeeCard || $student->studentFeeCard->balance_amount <= 0) {
            return redirect()->back()
                ->with('error', 'No pending dues for this student.');
        }

        // Use NotificationService to send reminders
        $notificationService = new \App\Services\NotificationService($tenant->id);
        $notificationService->sendPaymentReminder($student, $student->studentFeeCard->balance_amount);

        return redirect()->back()
            ->with('success', 'Payment reminder sent successfully!');
    }
}

