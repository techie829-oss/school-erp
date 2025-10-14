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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FeeCollectionController extends Controller
{
    /**
     * Display fee collection dashboard
     */
    public function index(Request $request)
    {
        $tenant = session('tenant');

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

        return view('tenant.admin.fees.collection.index', compact('students', 'stats'));
    }

    /**
     * Show fee details for a student
     */
    public function show($studentId)
    {
        $tenant = session('tenant');
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
    public function collect($studentId)
    {
        $tenant = session('tenant');
        $student = Student::forTenant($tenant->id)
            ->with(['schoolClass', 'section', 'studentFeeCard.feeItems.feeComponent'])
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
        $tenant = session('tenant');
        $student = Student::forTenant($tenant->id)->findOrFail($studentId);

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:cash,cheque,bank_transfer,online,razorpay',
            'amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:100',
            'transaction_id' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Create or get invoice
            $invoice = Invoice::forTenant($tenant->id)
                ->where('student_id', $studentId)
                ->where('status', '!=', 'paid')
                ->first();

            if (!$invoice) {
                $invoice = $this->createInvoice($student);
            }

            // Create payment
            $payment = Payment::create([
                'tenant_id' => $tenant->id,
                'student_id' => $studentId,
                'invoice_id' => $invoice->id,
                'payment_number' => Payment::generatePaymentNumber($tenant->id),
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'reference_number' => $request->reference_number,
                'status' => 'success',
                'notes' => $request->notes,
                'collected_by' => auth()->id(),
            ]);

            // Update invoice
            $invoice->paid_amount += $request->amount;
            if ($invoice->paid_amount >= $invoice->net_amount) {
                $invoice->status = 'paid';
            } else {
                $invoice->status = 'partial';
            }
            $invoice->save();

            // Update fee card and items (FIFO allocation)
            $this->allocatePayment($student->studentFeeCard, $request->amount);

            DB::commit();

            return redirect('/admin/fees/collection/' . $studentId)
                ->with('success', 'Payment collected successfully! Payment Number: ' . $payment->payment_number);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to process payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Create invoice for student
     */
    private function createInvoice($student)
    {
        $tenant = session('tenant');
        $feeCard = $student->studentFeeCard;

        $invoice = Invoice::create([
            'tenant_id' => $tenant->id,
            'student_id' => $student->id,
            'invoice_number' => Invoice::generateInvoiceNumber($tenant->id),
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
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'fee_component_id' => $feeItem->fee_component_id,
                'description' => $feeItem->feeComponent->name,
                'quantity' => 1,
                'unit_price' => $feeItem->net_amount,
                'discount' => $feeItem->discount_amount,
                'amount' => $feeItem->net_amount,
            ]);
        }

        return $invoice;
    }

    /**
     * Allocate payment to fee items (FIFO)
     */
    private function allocatePayment($feeCard, $amount)
    {
        $remainingAmount = $amount;

        // Get unpaid fee items ordered by due date
        $unpaidItems = $feeCard->feeItems()
            ->where('status', '!=', 'paid')
            ->orderBy('due_date', 'asc')
            ->get();

        foreach ($unpaidItems as $item) {
            if ($remainingAmount <= 0) break;

            $dueAmount = $item->net_amount - $item->paid_amount;
            $paymentForItem = min($remainingAmount, $dueAmount);

            $item->paid_amount += $paymentForItem;
            $item->updateStatus();

            $remainingAmount -= $paymentForItem;
        }

        // Update fee card totals
        $feeCard->updateBalance();
    }

    /**
     * Generate receipt
     */
    public function receipt($paymentId)
    {
        $tenant = session('tenant');
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
        $tenant = session('tenant');

        $reportType = $request->get('type', 'daily');
        $data = [];

        switch ($reportType) {
            case 'daily':
                $data = $this->getDailyReport($tenant, $request);
                break;
            case 'monthly':
                $data = $this->getMonthlyReport($tenant, $request);
                break;
            case 'defaulters':
                $data = $this->getDefaultersReport($tenant, $request);
                break;
            case 'collection_summary':
                $data = $this->getCollectionSummary($tenant, $request);
                break;
        }

        return view('tenant.admin.fees.reports.index', compact('data', 'reportType'));
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
