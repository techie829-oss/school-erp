<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentFeeCard;
use App\Models\StudentFeeItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\TenantSetting;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FeeCollectionController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }
    /**
     * Display fee collection dashboard
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $query = Student::forTenant($tenant->id)
            ->with(['schoolClass', 'section', 'studentFeeCard']);

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by section
        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        // Search by student
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('admission_number', 'like', '%' . $search . '%')
                  ->orWhere('first_name', 'like', '%' . $search . '%')
                  ->orWhere('last_name', 'like', '%' . $search . '%');
            });
        }

        $students = $query->orderBy('first_name')->paginate(20);

        // Get statistics
        $stats = [
            'total_collected_today' => Payment::forTenant($tenant->id)
                ->whereDate('payment_date', today())
                ->where('status', 'success')
                ->sum('amount'),
            'total_pending' => StudentFeeCard::forTenant($tenant->id)
                ->sum('balance_amount'),
            'students_with_dues' => StudentFeeCard::forTenant($tenant->id)
                ->where('balance_amount', '>', 0)
                ->count(),
        ];

        // Get classes and sections for filters
        $classes = \App\Models\SchoolClass::forTenant($tenant->id)->ordered()->get();
        $sections = \App\Models\Section::forTenant($tenant->id)->with('schoolClass')->get();

        return view('tenant.admin.fees.collection.index', compact('students', 'stats', 'classes', 'sections'));
    }

    /**
     * Display all payments with tracking details
     */
    public function payments(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $query = Payment::forTenant($tenant->id)
            ->with([
                'student.currentEnrollment.schoolClass',
                'student.currentEnrollment.section',
                'collectedBy',
                'invoice'
            ]);

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        // Filter by class
        if ($request->filled('class_id')) {
            $query->whereHas('student.currentEnrollment', function($q) use ($request) {
                $q->where('class_id', $request->class_id)->where('is_current', true);
            });
        }

        // Search by payment number, student name, admission number, reference, transaction ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_number', 'like', '%' . $search . '%')
                  ->orWhere('reference_number', 'like', '%' . $search . '%')
                  ->orWhere('transaction_id', 'like', '%' . $search . '%')
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('admission_number', 'like', '%' . $search . '%')
                        ->orWhere('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%');
                  });
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->orderBy('created_at', 'desc')->paginate(30);

        // Get statistics
        $stats = [
            'total_collected' => Payment::forTenant($tenant->id)
                ->where('status', 'success')
                ->sum('amount'),
            'total_collected_today' => Payment::forTenant($tenant->id)
                ->whereDate('payment_date', today())
                ->where('status', 'success')
                ->sum('amount'),
            'total_collected_this_month' => Payment::forTenant($tenant->id)
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->where('status', 'success')
                ->sum('amount'),
            'by_method' => Payment::forTenant($tenant->id)
                ->where('status', 'success')
                ->select('payment_method', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
                ->groupBy('payment_method')
                ->get(),
        ];

        $classes = \App\Models\SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.fees.collection.payments', compact('payments', 'stats', 'classes', 'tenant'));
    }

    /**
     * Show fee details for a student
     */
    public function show(Request $request, $studentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        $student = Student::forTenant($tenant->id)
            ->with(['schoolClass', 'section', 'studentFeeCard.feeItems.feeComponent',
                    'studentFeeCard.feePlan'])
            ->findOrFail($studentId);

        $invoices = Invoice::forTenant($tenant->id)
            ->where('student_id', $studentId)
            ->with('items.feeComponent')
            ->orderBy('created_at', 'desc')
            ->get();

        $payments = Payment::forTenant($tenant->id)
            ->where('student_id', $studentId)
            ->with('invoice')
            ->orderBy('payment_date', 'desc')
            ->get();

        return view('tenant.admin.fees.collection.show', compact('student', 'invoices', 'payments'));
    }

    /**
     * Show payment collection form
     */
    public function collect(Request $request, $studentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        $student = Student::forTenant($tenant->id)
            ->with([
                'currentEnrollment.schoolClass',
                'currentEnrollment.section',
                'studentFeeCard.feeItems.feeComponent'
            ])
            ->findOrFail($studentId);

        if (!$student->studentFeeCard) {
            return redirect()->back()
                ->with('error', 'No fee card assigned to this student!');
        }

        $paymentSettings = TenantSetting::getAllForTenant($tenant->id, 'payment');

        return view('tenant.admin.fees.collection.collect', compact('student', 'paymentSettings'));
    }

    /**
     * Process payment collection
     */
    public function processPayment(Request $request, $studentId)
    {
        \Log::info('Payment processing started', [
            'student_id' => $studentId,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'all_data' => $request->all()
        ]);

        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            \Log::error('Tenant not found in processPayment');
            abort(404, 'Tenant not found');
        }

        \Log::info('Tenant found', ['tenant_id' => $tenant->id]);

        $student = Student::forTenant($tenant->id)
            ->with(['studentFeeCard.feeItems.feeComponent'])
            ->findOrFail($studentId);

        if (!$student->studentFeeCard) {
            \Log::error('Student has no fee card', ['student_id' => $studentId]);
            return redirect()->back()
                ->with('error', 'No fee card assigned to this student!')
                ->withInput();
        }

        \Log::info('Student and fee card found', [
            'student_id' => $studentId,
            'fee_card_id' => $student->studentFeeCard->id,
            'balance_amount' => $student->studentFeeCard->balance_amount
        ]);

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:cash,cheque,bank_transfer,online,razorpay',
            'payment_type' => 'nullable|string|max:100',
            'amount' => 'required|numeric|min:1|max:' . $student->studentFeeCard->balance_amount,
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'transaction_id' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            \Log::warning('Payment validation failed', [
                'errors' => $validator->errors()->toArray(),
                'student_id' => $studentId
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        \Log::info('Validation passed, starting transaction');

        DB::beginTransaction();
        try {
            // Ensure student fee card and items are loaded before creating invoice
            $student->load('studentFeeCard.feeItems.feeComponent');

            if (!$student->studentFeeCard) {
                throw new \Exception('Student does not have a fee card assigned.');
            }

            // Create or get invoice
            $invoice = Invoice::forTenant($tenant->id)
                ->where('student_id', $studentId)
                ->where('status', '!=', 'paid')
                ->first();

            if (!$invoice) {
                \Log::info('No existing invoice found, creating new invoice');
                try {
                $invoice = $this->createInvoice($student, $tenant);
                    \Log::info('Invoice created successfully', ['invoice_id' => $invoice->id, 'invoice_number' => $invoice->invoice_number]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create invoice: ' . $e->getMessage(), [
                        'student_id' => $studentId,
                        'tenant_id' => $tenant->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                    throw new \Exception('Failed to create invoice: ' . $e->getMessage());
                }
            } else {
                \Log::info('Using existing invoice', ['invoice_id' => $invoice->id, 'invoice_number' => $invoice->invoice_number]);
            }

            if (!$invoice) {
                \Log::error('Invoice is null after creation/retrieval');
                throw new \Exception('Failed to create or retrieve invoice.');
            }

            // Create payment
            \Log::info('Creating payment record');
            $payment = Payment::create([
                'tenant_id' => $tenant->id,
                'student_id' => $studentId,
                'invoice_id' => $invoice->id,
                'payment_number' => Payment::generatePaymentNumber($tenant->id),
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_type' => $request->payment_type,
                'transaction_id' => $request->transaction_id,
                'reference_number' => $request->reference_number,
                'status' => 'success',
                'notes' => $request->notes,
                'collected_by' => auth()->id(),
            ]);
            \Log::info('Payment created', ['payment_id' => $payment->id, 'payment_number' => $payment->payment_number]);

            // Update invoice
            $invoice->paid_amount += $request->amount;
            if ($invoice->paid_amount >= $invoice->net_amount) {
                $invoice->status = 'paid';
            } else {
                $invoice->status = 'partial';
            }
            $invoice->save();

            // Reload student fee card with items to ensure we have fresh data
            $student->load('studentFeeCard.feeItems');

            // Update fee card and items (Allocate based on payment type if provided)
            $this->allocatePayment($student->studentFeeCard, $request->amount, $request->payment_type);

            // Final refresh and balance update to ensure consistency
            $student->studentFeeCard->refresh();
            $student->studentFeeCard->load('feeItems');
            $student->studentFeeCard->updateBalance();

            DB::commit();
            \Log::info('Transaction committed successfully', ['payment_id' => $payment->id]);

            // Send payment confirmation notification
            try {
            $notificationService = new \App\Services\NotificationService($tenant->id);
            $notificationService->sendPaymentConfirmation($payment);
            } catch (\Exception $e) {
                \Log::warning('Failed to send payment notification', ['error' => $e->getMessage()]);
                // Don't fail the payment if notification fails
            }

            \Log::info('Payment processing completed successfully', ['payment_id' => $payment->id]);
            return redirect('/admin/fees/collection/' . $studentId)
                ->with('success', 'Payment collected successfully! Payment Number: ' . $payment->payment_number);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the full error for debugging
            \Log::error('Payment processing failed', [
                'student_id' => $studentId,
                'tenant_id' => $tenant->id ?? null,
                'amount' => $request->amount ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to process payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Create invoice for student
     */
    private function createInvoice($student, $tenant)
    {
        $feeCard = $student->studentFeeCard;

        if (!$feeCard) {
            throw new \Exception('Student does not have a fee card assigned.');
        }

        // Ensure fee items and fee components are loaded
        if (!$feeCard->relationLoaded('feeItems')) {
            $feeCard->load('feeItems.feeComponent');
        } else {
            // If feeItems are loaded but not feeComponent, load it
            foreach ($feeCard->feeItems as $item) {
                if (!$item->relationLoaded('feeComponent')) {
                    $item->load('feeComponent');
                }
            }
        }

        // Generate invoice number (the method now handles uniqueness internally)
        $invoiceNumber = Invoice::generateInvoiceNumber($tenant->id);

        $invoice = Invoice::create([
            'tenant_id' => $tenant->id,
            'student_id' => $student->id,
            'invoice_number' => $invoiceNumber,
            'academic_year' => $feeCard->academic_year,
            'invoice_date' => now(),
            'due_date' => now()->addDays(30),
            'total_amount' => $feeCard->total_amount,
            'discount_amount' => $feeCard->discount_amount,
            'tax_amount' => 0,
            'net_amount' => $feeCard->total_amount - $feeCard->discount_amount,
            'paid_amount' => 0,
            'status' => 'sent',
        ]);

        // Create invoice items from fee card items
        foreach ($feeCard->feeItems as $feeItem) {
            if (!$feeItem->feeComponent) {
                \Log::warning("Fee item {$feeItem->id} has no fee component. Skipping invoice item creation.");
                continue;
            }

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'fee_component_id' => $feeItem->fee_component_id,
                'description' => $feeItem->feeComponent->name ?? 'Fee Component',
                'quantity' => 1,
                'unit_price' => $feeItem->net_amount,
                'discount' => $feeItem->discount_amount,
                'amount' => $feeItem->net_amount,
            ]);
        }

        return $invoice;
    }

    /**
     * Allocate payment to fee items
     * If payment_type is provided, allocate to matching component first
     * Otherwise, use FIFO (oldest due date first)
     */
    private function allocatePayment($feeCard, $amount, $paymentType = null)
    {
        if (!$feeCard) {
            throw new \Exception('Fee card not found for student.');
        }

        $remainingAmount = $amount;

        // Reload fee card with items and component names
        $feeCard->load('feeItems.feeComponent');

        // Get unpaid fee items
        $unpaidItems = $feeCard->feeItems()
            ->with('feeComponent')
            ->where('status', '!=', 'paid')
            ->get();

        // If payment_type is provided, match by exact component name
        if ($paymentType && $paymentType !== 'other') {
            // Find fee item by exact component name match (payment_type now contains the actual component name)
            $matchingItem = null;

            foreach ($unpaidItems as $item) {
                if (!$item->feeComponent) continue;

                // Direct exact match by component name
                if ($item->feeComponent->name === $paymentType) {
                    $matchingItem = $item;
                    break;
                }
            }

            // If found matching item, allocate payment ONLY to that component
            if ($matchingItem) {
                $dueAmount = $matchingItem->net_amount - $matchingItem->paid_amount;

                // Allocate only up to the amount due for this component
                $paymentForItem = min($remainingAmount, $dueAmount);

                $matchingItem->paid_amount += $paymentForItem;
                $matchingItem->updateStatus();

                $remainingAmount -= $paymentForItem;

                // If payment_type is specified and we found a match,
                // allocate ONLY to that component (no FIFO)
                // Refresh fee card to get latest data, then update balance
                $feeCard->refresh();
                $feeCard->updateBalance();
                return;
            }
        }

        // If payment_type was not specified or no match found, use FIFO (oldest due date first)
        if ($remainingAmount > 0 && $unpaidItems->count() > 0) {
            $sortedItems = $unpaidItems->sortBy('due_date')->values();

            foreach ($sortedItems as $item) {
                if ($remainingAmount <= 0) break;

                $dueAmount = $item->net_amount - $item->paid_amount;
                $paymentForItem = min($remainingAmount, $dueAmount);

                $item->paid_amount += $paymentForItem;
                $item->updateStatus();

                $remainingAmount -= $paymentForItem;
            }
        }

        // Refresh fee card to get latest data from database, then update balance
        $feeCard->refresh();
        $feeCard->updateBalance();
    }

    /**
     * Generate receipt
     */
    public function receipt(Request $request, $paymentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }
        $payment = Payment::forTenant($tenant->id)
            ->with(['student.schoolClass', 'student.section', 'invoice.items.feeComponent', 'collectedBy'])
            ->findOrFail($paymentId);

        return view('tenant.admin.fees.collection.receipt', compact('payment', 'tenant'));
    }

    /**
     * Fee reports
     */
    public function reports(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $classes = \App\Models\SchoolClass::forTenant($tenant->id)->orderBy('class_name')->get();

        // Check if report generation requested
        if (!$request->has('report_type')) {
            return view('tenant.admin.fees.reports', compact('classes'));
        }

        $reportType = $request->get('report_type', 'collection');
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        $classId = $request->get('class_id');

        $reportData = null;
        $summary = null;

        switch ($reportType) {
            case 'collection':
                list($reportData, $summary) = $this->getCollectionReport($tenant, $fromDate, $toDate, $classId);
                break;
            case 'outstanding':
                list($reportData, $summary) = $this->getOutstandingReport($tenant, $classId);
                break;
            case 'defaulters':
                list($reportData, $summary) = $this->getDefaultersReport($tenant, $classId);
                break;
            case 'class_wise':
                list($reportData, $summary) = $this->getClassWiseReport($tenant, $fromDate, $toDate);
                break;
            case 'payment_method':
                list($reportData, $summary) = $this->getPaymentMethodReport($tenant, $fromDate, $toDate);
                break;
            default:
                if ($request->get('export') === 'excel') {
                    return redirect()->back()->with('error', 'Invalid report type selected.');
                }
                break;
        }

        // Handle export
        if ($request->get('export') === 'excel') {
            if (!$reportData) {
                return redirect()->back()->with('error', 'No data to export. Please generate a report first.');
            }
            return $this->exportToExcel($reportData, $reportType);
        }

        return view('tenant.admin.fees.reports', compact('classes', 'reportData', 'summary'));
    }

    private function getCollectionReport($tenant, $fromDate, $toDate, $classId = null)
    {
        $query = Payment::forTenant($tenant->id)
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->where('status', 'success')
            ->with([
                'student.currentEnrollment.schoolClass',
                'collectedBy'
            ]);

        if ($classId) {
            $query->whereHas('student.currentEnrollment', function($q) use ($classId) {
                $q->where('class_id', $classId)->where('is_current', true);
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        $data = [
            'headers' => ['Date', 'Receipt No', 'Student Name', 'Class', 'Amount', 'Method', 'Reference', 'Collected By'],
            'data' => $payments->map(function($payment) {
                return [
                    $payment->payment_date->format('d M Y'),
                    $payment->payment_number,
                    $payment->student->full_name,
                    $payment->student->currentEnrollment?->schoolClass?->class_name ?? '-',
                    '₹' . number_format($payment->amount, 2),
                    ucfirst(str_replace('_', ' ', $payment->payment_method)),
                    $payment->reference_number ?? '-',
                    $payment->collectedBy->name ?? 'System',
                ];
            })->toArray()
        ];

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'average_payment' => $payments->count() > 0 ? $payments->avg('amount') : 0,
        ];

        return [$data, $summary];
    }

    private function getOutstandingReport($tenant, $classId = null)
    {
        $query = StudentFeeCard::forTenant($tenant->id)
            ->where('balance_amount', '>', 0)
            ->with(['student.currentEnrollment.schoolClass', 'student.currentEnrollment.section', 'feePlan']);

        if ($classId) {
            $query->whereHas('student.currentEnrollment', function($q) use ($classId) {
                $q->where('class_id', $classId)->where('is_current', true);
            });
        }

        $feeCards = $query->orderBy('balance_amount', 'desc')->get();

        $data = [
            'headers' => ['Student Name', 'Admission No', 'Class', 'Total Amount', 'Paid', 'Balance', 'Status'],
            'data' => $feeCards->map(function($card) {
                return [
                    $card->student->full_name,
                    $card->student->admission_number,
                    $card->student->currentEnrollment?->schoolClass?->class_name ?? '-',
                    '₹' . number_format($card->total_amount, 2),
                    '₹' . number_format($card->paid_amount, 2),
                    '₹' . number_format($card->balance_amount, 2),
                    ucfirst($card->status),
                ];
            })->toArray()
        ];

        $summary = [
            'total_students' => $feeCards->count(),
            'total_outstanding' => $feeCards->sum('balance_amount'),
            'total_amount_due' => $feeCards->sum('total_amount'),
            'total_collected' => $feeCards->sum('paid_amount'),
        ];

        return [$data, $summary];
    }

    private function getClassWiseReport($tenant, $fromDate, $toDate)
    {
        $classes = \App\Models\SchoolClass::forTenant($tenant->id)->get();

        $data = [
            'headers' => ['Class', 'Total Students', 'Total Amount', 'Collected', 'Outstanding', 'Collection %'],
            'data' => []
        ];

        $totalAmount = 0;
        $totalCollected = 0;
        $totalOutstanding = 0;

        foreach ($classes as $class) {
            $feeCards = StudentFeeCard::forTenant($tenant->id)
                ->whereHas('student.currentEnrollment', function($q) use ($class) {
                    $q->where('class_id', $class->id)->where('is_current', true);
                })
                ->get();

            $classTotal = $feeCards->sum('total_amount');
            $classCollected = $feeCards->sum('paid_amount');
            $classOutstanding = $feeCards->sum('balance_amount');
            $collectionPercent = $classTotal > 0 ? ($classCollected / $classTotal) * 100 : 0;

            $data['data'][] = [
                $class->class_name,
                $feeCards->count(),
                '₹' . number_format($classTotal, 2),
                '₹' . number_format($classCollected, 2),
                '₹' . number_format($classOutstanding, 2),
                number_format($collectionPercent, 1) . '%',
            ];

            $totalAmount += $classTotal;
            $totalCollected += $classCollected;
            $totalOutstanding += $classOutstanding;
        }

        $summary = [
            'total_amount' => $totalAmount,
            'total_collected' => $totalCollected,
            'total_outstanding' => $totalOutstanding,
            'collection_percentage' => $totalAmount > 0 ? ($totalCollected / $totalAmount) * 100 : 0,
        ];

        return [$data, $summary];
    }

    private function getPaymentMethodReport($tenant, $fromDate, $toDate)
    {
        $payments = Payment::forTenant($tenant->id)
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->where('status', 'success')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        $data = [
            'headers' => ['Payment Method', 'Number of Payments', 'Total Amount', 'Percentage'],
            'data' => []
        ];

        $grandTotal = $payments->sum('total');

        foreach ($payments as $payment) {
            $percentage = $grandTotal > 0 ? ($payment->total / $grandTotal) * 100 : 0;
            $data['data'][] = [
                ucfirst(str_replace('_', ' ', $payment->payment_method)),
                $payment->count,
                '₹' . number_format($payment->total, 2),
                number_format($percentage, 1) . '%',
            ];
        }

        $summary = [
            'total_payments' => $payments->sum('count'),
            'total_amount' => $grandTotal,
        ];

        return [$data, $summary];
    }

    private function exportToExcel($reportData, $reportType)
    {
        if (!$reportData || !isset($reportData['headers']) || !isset($reportData['data'])) {
            abort(400, 'Invalid report data for export');
        }

        $filename = 'fee-' . $reportType . '-report-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($reportData) {
            $handle = fopen('php://output', 'w');

            // Add BOM for UTF-8 to ensure Excel displays special characters correctly
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, $reportData['headers']);

            foreach ($reportData['data'] ?? [] as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    private function getDailyReport($tenant, $request)
    {
        $date = $request->get('date', today());

        return Payment::forTenant($tenant->id)
            ->whereDate('payment_date', $date)
            ->with(['student', 'collectedBy'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function getMonthlyReport($tenant, $request)
    {
        $month = $request->get('month', now()->format('Y-m'));

        return Payment::forTenant($tenant->id)
            ->whereYear('payment_date', substr($month, 0, 4))
            ->whereMonth('payment_date', substr($month, 5, 2))
            ->with(['student'])
            ->get()
            ->groupBy(function($payment) {
                return $payment->payment_date->format('Y-m-d');
            });
    }

    private function getDefaultersReport($tenant, $request)
    {
        return StudentFeeCard::forTenant($tenant->id)
            ->where('balance_amount', '>', 0)
            ->with(['student.schoolClass', 'student.section'])
            ->orderBy('balance_amount', 'desc')
            ->get();
    }

    private function getCollectionSummary($tenant, $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now());

        return [
            'total_collected' => Payment::forTenant($tenant->id)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->where('status', 'success')
                ->sum('amount'),
            'payment_methods' => Payment::forTenant($tenant->id)
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->where('status', 'success')
                ->select('payment_method', DB::raw('SUM(amount) as total'))
                ->groupBy('payment_method')
                ->get(),
        ];
    }
}

