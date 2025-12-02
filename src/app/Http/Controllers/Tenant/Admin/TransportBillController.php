<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportBill;
use App\Models\TransportBillItem;
use App\Models\TransportAssignment;
use App\Models\TransportPayment;
use App\Models\Route;
use App\Models\Student;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TransportBillController extends Controller
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

        $query = TransportBill::forTenant($tenant->id)
            ->with(['student', 'assignment.route']);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('student_id') && $request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('search') && $request->search) {
            $query->where('bill_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('student', function($q) use ($request) {
                      $q->where('full_name', 'like', '%' . $request->search . '%')
                        ->orWhere('admission_number', 'like', '%' . $request->search . '%');
                  });
        }

        $bills = $query->latest('bill_date')->paginate(20)->withQueryString();
        $students = Student::forTenant($tenant->id)->active()->orderBy('full_name')->get();

        return view('tenant.admin.transport.bills.index', compact('bills', 'students', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        $students = Student::forTenant($tenant->id)->active()->orderBy('full_name')->get();
        $assignments = TransportAssignment::forTenant($tenant->id)
            ->where('status', 'active')
            ->where('booking_status', 'active')
            ->with(['student', 'route'])
            ->orderBy('booking_date')
            ->get();

        return view('tenant.admin.transport.bills.create', compact('students', 'assignments', 'tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'assignment_id' => 'nullable|exists:transport_assignments,id',
            'bill_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:bill_date',
            'academic_year' => 'nullable|string|max:50',
            'term' => 'nullable|string|max:50',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Calculate totals
            $totalAmount = 0;
            foreach ($request->items as $item) {
                $itemAmount = ($item['unit_price'] * $item['quantity']) - ($item['discount'] ?? 0);
                $totalAmount += $itemAmount;
            }

            $discountAmount = $request->discount_amount ?? 0;
            $taxAmount = $request->tax_amount ?? 0;
            $netAmount = $totalAmount - $discountAmount + $taxAmount;

            $bill = TransportBill::create([
                'tenant_id' => $tenant->id,
                'student_id' => $request->student_id,
                'assignment_id' => $request->assignment_id,
                'bill_number' => TransportBill::generateBillNumber($tenant->id),
                'bill_date' => $request->bill_date,
                'due_date' => $request->due_date,
                'academic_year' => $request->academic_year,
                'term' => $request->term,
                'total_amount' => $totalAmount,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'net_amount' => $netAmount,
                'paid_amount' => 0,
                'status' => 'sent',
                'notes' => $request->notes,
            ]);

            // Create bill items
            foreach ($request->items as $item) {
                $itemAmount = ($item['unit_price'] * $item['quantity']) - ($item['discount'] ?? 0);
                TransportBillItem::create([
                    'bill_id' => $bill->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item['discount'] ?? 0,
                    'amount' => $itemAmount,
                ]);
            }

            DB::commit();

            return redirect(url('/admin/transport/bills'))->with('success', 'Transport bill created successfully. Bill Number: ' . $bill->bill_number);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create bill: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $bill = TransportBill::forTenant($tenant->id)
            ->with(['student', 'assignment.route', 'items', 'payments.collectedBy'])
            ->findOrFail($id);

        return view('tenant.admin.transport.bills.show', compact('bill', 'tenant'));
    }

    public function print(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $bill = TransportBill::forTenant($tenant->id)
            ->with(['student', 'assignment.route', 'items'])
            ->findOrFail($id);

        // Show preview page - user can print from browser
        return view('tenant.admin.transport.bills.print', compact('bill', 'tenant'));
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $bill = TransportBill::forTenant($tenant->id)
            ->withCount('payments')
            ->findOrFail($id);

        if ($bill->payments_count > 0) {
            return back()->with('error', 'Cannot delete bill with existing payments.');
        }

        $bill->delete();

        return redirect(url('/admin/transport/bills'))->with('success', 'Bill deleted successfully.');
    }

    public function reports(Request $request)
    {
        $tenant = $this->getTenant($request);

        $students = Student::forTenant($tenant->id)->active()->orderBy('full_name')->get();
        $routes = Route::forTenant($tenant->id)->active()->orderBy('name')->get();

        // Check if report generation requested
        if (!$request->has('report_type')) {
            return view('tenant.admin.transport.reports', compact('students', 'routes', 'tenant'));
        }

        $reportType = $request->get('report_type', 'collection');
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        $studentId = $request->get('student_id');
        $routeId = $request->get('route_id');

        $reportData = null;
        $summary = null;

        switch ($reportType) {
            case 'collection':
                list($reportData, $summary) = $this->getCollectionReport($tenant, $fromDate, $toDate, $studentId, $routeId);
                break;
            case 'outstanding':
                list($reportData, $summary) = $this->getOutstandingReport($tenant, $studentId, $routeId);
                break;
            case 'route_wise':
                list($reportData, $summary) = $this->getRouteWiseReport($tenant, $fromDate, $toDate);
                break;
            case 'payment_method':
                list($reportData, $summary) = $this->getPaymentMethodReport($tenant, $fromDate, $toDate);
                break;
        }

        return view('tenant.admin.transport.reports', compact('students', 'routes', 'tenant', 'reportData', 'summary'));
    }

    private function getCollectionReport($tenant, $fromDate, $toDate, $studentId = null, $routeId = null)
    {
        $query = TransportPayment::forTenant($tenant->id)
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->with(['student', 'bill']);

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        if ($routeId) {
            $query->whereHas('bill.assignment', function($q) use ($routeId) {
                $q->where('route_id', $routeId);
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'average_payment' => $payments->count() > 0 ? $payments->sum('amount') / $payments->count() : 0,
        ];

        return [$payments, $summary];
    }

    private function getOutstandingReport($tenant, $studentId = null, $routeId = null)
    {
        $query = TransportBill::forTenant($tenant->id)
            ->whereIn('status', ['sent', 'partial', 'overdue'])
            ->with(['student', 'assignment.route']);

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        if ($routeId) {
            $query->whereHas('assignment', function($q) use ($routeId) {
                $q->where('route_id', $routeId);
            });
        }

        $bills = $query->orderBy('due_date')->get();

        $summary = [
            'total_bills' => $bills->count(),
            'total_outstanding' => $bills->sum('outstanding_amount'),
            'total_amount' => $bills->sum('net_amount'),
            'total_paid' => $bills->sum('paid_amount'),
        ];

        return [$bills, $summary];
    }

    private function getRouteWiseReport($tenant, $fromDate, $toDate)
    {
        $bills = TransportBill::forTenant($tenant->id)
            ->whereBetween('bill_date', [$fromDate, $toDate])
            ->with(['assignment.route'])
            ->get();

        $routeData = $bills->groupBy(function($bill) {
            return $bill->assignment->route->name ?? 'Unassigned';
        })->map(function($bills) {
            return [
                'route_name' => $bills->first()->assignment->route->name ?? 'Unassigned',
                'total_bills' => $bills->count(),
                'total_amount' => $bills->sum('net_amount'),
                'total_collected' => $bills->sum('paid_amount'),
                'total_outstanding' => $bills->sum('outstanding_amount'),
            ];
        })->values();

        $summary = [
            'total_routes' => $routeData->count(),
            'total_bills' => $bills->count(),
            'total_amount' => $bills->sum('net_amount'),
            'total_collected' => $bills->sum('paid_amount'),
            'total_outstanding' => $bills->sum('outstanding_amount'),
        ];

        return [$routeData, $summary];
    }

    private function getPaymentMethodReport($tenant, $fromDate, $toDate)
    {
        $payments = TransportPayment::forTenant($tenant->id)
            ->whereBetween('payment_date', [$fromDate, $toDate])
            ->get();

        $methodData = $payments->groupBy('payment_method')->map(function($payments, $method) {
            return [
                'method' => ucfirst(str_replace('_', ' ', $method)),
                'count' => $payments->count(),
                'total_amount' => $payments->sum('amount'),
                'percentage' => 0, // Will calculate after
            ];
        })->values();

        $totalAmount = $payments->sum('amount');
        $methodData = $methodData->map(function($item) use ($totalAmount) {
            $item['percentage'] = $totalAmount > 0 ? ($item['total_amount'] / $totalAmount) * 100 : 0;
            return $item;
        });

        $summary = [
            'total_payments' => $payments->count(),
            'total_amount' => $totalAmount,
        ];

        return [$methodData, $summary];
    }
}
