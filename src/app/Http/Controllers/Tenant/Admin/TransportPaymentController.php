<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportPayment;
use App\Models\TransportBill;
use App\Models\Student;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TransportPaymentController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    protected function getTenant(Request $request)
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

    public function index(Request $request)
    {
        $tenant = $this->getTenant($request);

        $query = TransportPayment::forTenant($tenant->id)
            ->with(['student', 'bill']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('student_id') && $request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('payment_method') && $request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->has('search') && $request->search) {
            $query->where('payment_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('student', function($q) use ($request) {
                      $q->where('full_name', 'like', '%' . $request->search . '%')
                        ->orWhere('admission_number', 'like', '%' . $request->search . '%');
                  });
        }

        $payments = $query->latest('payment_date')->paginate(20)->withQueryString();
        $students = Student::forTenant($tenant->id)->active()->orderBy('full_name')->get();

        return view('tenant.admin.transport.payments.index', compact('payments', 'students', 'tenant'));
    }

    public function collect(Request $request, $studentId = null)
    {
        $tenant = $this->getTenant($request);

        if ($studentId) {
            $student = Student::forTenant($tenant->id)->findOrFail($studentId);
            $bills = TransportBill::forTenant($tenant->id)
                ->where('student_id', $studentId)
                ->unpaid()
                ->with('assignment.route')
                ->orderBy('due_date')
                ->get();
        } else {
            $student = null;
            $bills = collect();
        }

        $students = Student::forTenant($tenant->id)->active()->orderBy('full_name')->get();

        return view('tenant.admin.transport.payments.collect', compact('student', 'bills', 'students', 'tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'bill_id' => 'nullable|exists:transport_bills,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,cheque,bank_transfer,online,card,other',
            'payment_type' => 'nullable|string|max:100',
            'transaction_id' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'cheque_number' => 'nullable|string|max:100',
            'cheque_date' => 'nullable|date',
            'bank_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Create payment
            $payment = TransportPayment::create([
                'tenant_id' => $tenant->id,
                'student_id' => $request->student_id,
                'bill_id' => $request->bill_id,
                'payment_number' => TransportPayment::generatePaymentNumber($tenant->id),
                'payment_date' => $request->payment_date,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_type' => $request->payment_type,
                'transaction_id' => $request->transaction_id,
                'reference_number' => $request->reference_number,
                'cheque_number' => $request->cheque_number,
                'cheque_date' => $request->cheque_date,
                'bank_name' => $request->bank_name,
                'status' => 'success',
                'notes' => $request->notes,
                'collected_by' => auth()->id(),
            ]);

            // Update bill if payment is against a bill
            if ($request->bill_id) {
                $bill = TransportBill::forTenant($tenant->id)->findOrFail($request->bill_id);
                $bill->paid_amount += $request->amount;

                if ($bill->paid_amount >= $bill->net_amount) {
                    $bill->status = 'paid';
                } else {
                    $bill->status = 'partial';
                }

                // Check if overdue
                if ($bill->due_date < now() && $bill->status !== 'paid') {
                    $bill->status = 'overdue';
                }

                $bill->save();
            }

            DB::commit();

            return redirect(url('/admin/transport/payments'))->with('success', 'Payment collected successfully. Payment Number: ' . $payment->payment_number);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to process payment: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $payment = TransportPayment::forTenant($tenant->id)
            ->with(['student', 'bill', 'collectedBy'])
            ->findOrFail($id);

        return view('tenant.admin.transport.payments.show', compact('payment', 'tenant'));
    }

    public function receipt(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $payment = TransportPayment::forTenant($tenant->id)
            ->with(['student', 'bill'])
            ->findOrFail($id);

        $pdf = Pdf::loadView('tenant.admin.transport.payments.receipt', compact('payment', 'tenant'));
        return $pdf->download('transport-payment-receipt-' . $payment->payment_number . '.pdf');
    }
}
