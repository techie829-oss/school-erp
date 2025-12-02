<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\LibrarySetting;
use App\Models\Student;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookIssueController extends Controller
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

        $query = BookIssue::forTenant($tenant->id)
            ->with(['book', 'student', 'issuedBy']);

        if ($request->has('status') && $request->status !== '') {
            if ($request->status === 'overdue') {
                $query->overdue();
            } else {
                $query->where('status', $request->status);
            }
        }

        if ($request->has('student_id') && $request->student_id) {
            $query->byStudent($request->student_id);
        }

        if ($request->has('book_id') && $request->book_id) {
            $query->byBook($request->book_id);
        }

        if ($request->has('search') && $request->search) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('admission_number', 'like', '%' . $request->search . '%');
            })->orWhereHas('book', function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('isbn', 'like', '%' . $request->search . '%');
            });
        }

        $issues = $query->latest('issue_date')->paginate(20)->withQueryString();

        // Update overdue statuses
        $this->updateOverdueStatuses($tenant->id);

        return view('tenant.admin.library.issues.index', compact('issues', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        $settings = LibrarySetting::getForTenant($tenant->id);

        $books = Book::forTenant($tenant->id)->available()->orderBy('title')->get();
        $students = Student::forTenant($tenant->id)->active()->orderBy('full_name')->get();

        return view('tenant.admin.library.issues.create', compact('books', 'students', 'settings', 'tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);
        $settings = LibrarySetting::getForTenant($tenant->id);

        $validator = Validator::make($request->all(), [
            'book_id' => 'required|exists:books,id',
            'student_id' => 'required|exists:students,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'issue_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $book = Book::forTenant($tenant->id)->findOrFail($request->book_id);

        // Check if book is available
        if (!$book->is_available) {
            return back()->with('error', 'Book is not available.')->withInput();
        }

        // Check student's current issues
        $studentIssues = BookIssue::forTenant($tenant->id)
            ->byStudent($request->student_id)
            ->whereIn('status', ['issued', 'overdue'])
            ->count();

        if ($studentIssues >= $settings->max_books_per_student) {
            return back()->with('error', "Student has reached the maximum limit of {$settings->max_books_per_student} books.")->withInput();
        }

        DB::beginTransaction();
        try {
            $issue = BookIssue::create([
                'tenant_id' => $tenant->id,
                'book_id' => $request->book_id,
                'student_id' => $request->student_id,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'issue_notes' => $request->issue_notes,
                'status' => 'issued',
                'issued_by' => auth()->id(),
            ]);

            // Decrement available copies
            $book->decrementAvailable();

            DB::commit();

            return redirect(url('/admin/library/issues'))->with('success', 'Book issued successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to issue book: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $issue = BookIssue::forTenant($tenant->id)
            ->with(['book', 'student', 'issuedBy', 'returnedBy'])
            ->findOrFail($id);

        // Calculate fine if overdue
        if ($issue->is_overdue) {
            $settings = LibrarySetting::getForTenant($tenant->id);
            $issue->calculateFine($settings->fine_per_day);
        }

        return view('tenant.admin.library.issues.show', compact('issue', 'tenant'));
    }

    public function returnBook(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $issue = BookIssue::forTenant($tenant->id)
            ->whereIn('status', ['issued', 'overdue'])
            ->findOrFail($id);

        $settings = LibrarySetting::getForTenant($tenant->id);

        $validator = Validator::make($request->all(), [
            'return_date' => 'required|date|after_or_equal:' . $issue->issue_date,
            'fine_amount' => 'nullable|numeric|min:0',
            'return_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $returnDate = Carbon::parse($request->return_date);

            // Calculate fine if returned after due date
            if ($returnDate > $issue->due_date) {
                $daysOverdue = $returnDate->diffInDays($issue->due_date);
                $fine = $daysOverdue * $settings->fine_per_day;

                $issue->fine_amount = $fine;
                $issue->paid_fine = $request->fine_amount ?? 0;
            }

            $issue->return_date = $returnDate;
            $issue->return_notes = $request->return_notes;
            $issue->status = 'returned';
            $issue->returned_by = auth()->id();
            $issue->save();

            // Increment available copies
            $issue->book->incrementAvailable();

            DB::commit();

            return redirect(url('/admin/library/issues'))->with('success', 'Book returned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to return book: ' . $e->getMessage());
        }
    }

    public function renewBook(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $issue = BookIssue::forTenant($tenant->id)
            ->whereIn('status', ['issued', 'overdue'])
            ->findOrFail($id);

        $settings = LibrarySetting::getForTenant($tenant->id);

        if ($issue->renewal_count >= $settings->max_renewals) {
            return back()->with('error', 'Maximum renewals limit reached.');
        }

        DB::beginTransaction();
        try {
            $newDueDate = Carbon::parse($issue->due_date)->addDays($settings->renewal_duration_days);

            $issue->renew($newDueDate, $settings->max_renewals);

            // Recalculate fine if it was overdue
            if ($issue->status === 'overdue') {
                $issue->status = 'issued';
                $issue->fine_amount = 0;
            }

            $issue->save();

            DB::commit();

            return back()->with('success', 'Book renewed successfully. New due date: ' . $newDueDate->format('d M Y'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to renew book: ' . $e->getMessage());
        }
    }

    protected function updateOverdueStatuses($tenantId)
    {
        $overdueIssues = BookIssue::forTenant($tenantId)
            ->where('status', 'issued')
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueIssues as $issue) {
            $issue->status = 'overdue';
            $issue->save();
        }
    }
}
