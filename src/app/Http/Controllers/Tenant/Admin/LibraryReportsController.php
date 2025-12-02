<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookIssue;
use App\Models\BookCategory;
use App\Models\Student;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LibraryReportsController extends Controller
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

        // Check if report generation requested
        if (!$request->has('report_type')) {
            $students = Student::forTenant($tenant->id)->active()->orderBy('full_name')->get();
            $categories = BookCategory::forTenant($tenant->id)->active()->orderBy('name')->get();
            return view('tenant.admin.library.reports', compact('students', 'categories', 'tenant'));
        }

        $reportType = $request->get('report_type');
        $fromDate = $request->get('from_date', now()->startOfMonth()->format('Y-m-d'));
        $toDate = $request->get('to_date', now()->format('Y-m-d'));
        $studentId = $request->get('student_id');
        $categoryId = $request->get('category_id');

        $reportData = null;
        $summary = null;

        switch ($reportType) {
            case 'popular_books':
                list($reportData, $summary) = $this->getPopularBooksReport($tenant, $fromDate, $toDate);
                break;
            case 'overdue_books':
                list($reportData, $summary) = $this->getOverdueBooksReport($tenant);
                break;
            case 'student_history':
                list($reportData, $summary) = $this->getStudentHistoryReport($tenant, $studentId, $fromDate, $toDate);
                break;
            case 'category_wise':
                list($reportData, $summary) = $this->getCategoryWiseReport($tenant, $categoryId);
                break;
            case 'fine_collection':
                list($reportData, $summary) = $this->getFineCollectionReport($tenant, $fromDate, $toDate);
                break;
            case 'issue_statistics':
                list($reportData, $summary) = $this->getIssueStatisticsReport($tenant, $fromDate, $toDate);
                break;
        }

        $students = Student::forTenant($tenant->id)->active()->orderBy('full_name')->get();
        $categories = BookCategory::forTenant($tenant->id)->active()->orderBy('name')->get();

        return view('tenant.admin.library.reports', compact('students', 'categories', 'tenant', 'reportData', 'summary', 'reportType'));
    }

    private function getPopularBooksReport($tenant, $fromDate, $toDate)
    {
        $books = BookIssue::forTenant($tenant->id)
            ->whereBetween('issue_date', [$fromDate, $toDate])
            ->select('book_id', DB::raw('count(*) as issue_count'))
            ->groupBy('book_id')
            ->orderBy('issue_count', 'desc')
            ->with('book.category')
            ->limit(20)
            ->get();

        $summary = [
            'total_issues' => $books->sum('issue_count'),
            'unique_books' => $books->count(),
        ];

        return [$books, $summary];
    }

    private function getOverdueBooksReport($tenant)
    {
        $overdueIssues = BookIssue::forTenant($tenant->id)
            ->whereIn('status', ['issued', 'overdue'])
            ->where('due_date', '<', now())
            ->with(['book', 'student'])
            ->orderBy('due_date')
            ->get();

        $summary = [
            'total_overdue' => $overdueIssues->count(),
            'total_fine' => $overdueIssues->sum('fine_amount'),
        ];

        return [$overdueIssues, $summary];
    }

    private function getStudentHistoryReport($tenant, $studentId, $fromDate, $toDate)
    {
        $query = BookIssue::forTenant($tenant->id)
            ->whereBetween('issue_date', [$fromDate, $toDate])
            ->with(['book', 'book.category']);

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        $issues = $query->orderBy('issue_date', 'desc')->get();

        $summary = [
            'total_issues' => $issues->count(),
            'returned' => $issues->where('status', 'returned')->count(),
            'active' => $issues->whereIn('status', ['issued', 'overdue'])->count(),
            'total_fine' => $issues->sum('fine_amount'),
        ];

        return [$issues, $summary];
    }

    private function getCategoryWiseReport($tenant, $categoryId)
    {
        $query = Book::forTenant($tenant->id)
            ->with(['category', 'issues'])
            ->withCount('issues');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $books = $query->orderBy('issues_count', 'desc')->get();

        $summary = [
            'total_books' => $books->count(),
            'total_issues' => $books->sum('issues_count'),
            'categories' => $books->groupBy('category_id')->count(),
        ];

        return [$books, $summary];
    }

    private function getFineCollectionReport($tenant, $fromDate, $toDate)
    {
        $issues = BookIssue::forTenant($tenant->id)
            ->where('fine_amount', '>', 0)
            ->whereBetween('return_date', [$fromDate, $toDate])
            ->with(['book', 'student'])
            ->orderBy('return_date', 'desc')
            ->get();

        $summary = [
            'total_fine' => $issues->sum('fine_amount'),
            'paid_fine' => $issues->sum('paid_fine'),
            'outstanding_fine' => $issues->sum('fine_amount') - $issues->sum('paid_fine'),
            'total_issues' => $issues->count(),
        ];

        return [$issues, $summary];
    }

    private function getIssueStatisticsReport($tenant, $fromDate, $toDate)
    {
        $issues = BookIssue::forTenant($tenant->id)
            ->whereBetween('issue_date', [$fromDate, $toDate])
            ->get();

        $summary = [
            'total_issues' => $issues->count(),
            'returned' => $issues->where('status', 'returned')->count(),
            'active' => $issues->whereIn('status', ['issued', 'overdue'])->count(),
            'overdue' => $issues->where('status', 'overdue')->count(),
            'total_fine' => $issues->sum('fine_amount'),
            'paid_fine' => $issues->sum('paid_fine'),
        ];

        return [$issues, $summary];
    }
}

