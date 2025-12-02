<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LibraryController extends Controller
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

        $query = Book::forTenant($tenant->id)->with('category');

        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('category_id') && $request->category_id) {
            $query->byCategory($request->category_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('available_only') && $request->available_only) {
            $query->available();
        }

        $books = $query->orderBy('title')->paginate(20)->withQueryString();
        $categories = BookCategory::forTenant($tenant->id)->active()->orderBy('name')->get();

        return view('tenant.admin.library.books.index', compact('books', 'categories', 'tenant'));
    }

    public function create(Request $request)
    {
        $tenant = $this->getTenant($request);
        $categories = BookCategory::forTenant($tenant->id)->active()->orderBy('name')->get();
        return view('tenant.admin.library.books.create', compact('categories', 'tenant'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant($request);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:book_categories,id',
            'edition' => 'nullable|string|max:50',
            'copies' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'language' => 'nullable|string|max:50',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'rack_number' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:100|unique:books,barcode',
            'status' => 'required|in:available,unavailable,lost,damaged',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $book = Book::create([
            'tenant_id' => $tenant->id,
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'publisher' => $request->publisher,
            'category_id' => $request->category_id,
            'edition' => $request->edition,
            'copies' => $request->copies,
            'available_copies' => $request->copies,
            'price' => $request->price,
            'description' => $request->description,
            'language' => $request->language ?? 'English',
            'publication_year' => $request->publication_year,
            'rack_number' => $request->rack_number,
            'barcode' => $request->barcode,
            'status' => $request->status,
        ]);

        return redirect(url('/admin/library/books'))->with('success', 'Book added successfully.');
    }

    public function show(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $book = Book::forTenant($tenant->id)
            ->with(['category', 'issues.student', 'activeIssues.student'])
            ->findOrFail($id);

        return view('tenant.admin.library.books.show', compact('book', 'tenant'));
    }

    public function edit(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $book = Book::forTenant($tenant->id)->findOrFail($id);
        $categories = BookCategory::forTenant($tenant->id)->active()->orderBy('name')->get();
        return view('tenant.admin.library.books.edit', compact('book', 'categories', 'tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $book = Book::forTenant($tenant->id)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:50',
            'publisher' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:book_categories,id',
            'edition' => 'nullable|string|max:50',
            'copies' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'language' => 'nullable|string|max:50',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'rack_number' => 'nullable|string|max:50',
            'barcode' => 'nullable|string|max:100|unique:books,barcode,' . $id,
            'status' => 'required|in:available,unavailable,lost,damaged',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Calculate available copies difference
        $copiesDiff = $request->copies - $book->copies;
        $newAvailableCopies = max(0, $book->available_copies + $copiesDiff);

        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'isbn' => $request->isbn,
            'publisher' => $request->publisher,
            'category_id' => $request->category_id,
            'edition' => $request->edition,
            'copies' => $request->copies,
            'available_copies' => $newAvailableCopies,
            'price' => $request->price,
            'description' => $request->description,
            'language' => $request->language ?? 'English',
            'publication_year' => $request->publication_year,
            'rack_number' => $request->rack_number,
            'barcode' => $request->barcode,
            'status' => $request->status,
        ]);

        return redirect(url('/admin/library/books'))->with('success', 'Book updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $tenant = $this->getTenant($request);
        $book = Book::forTenant($tenant->id)->withCount('activeIssues')->findOrFail($id);

        if ($book->active_issues_count > 0) {
            return back()->with('error', 'Cannot delete book with active issues.');
        }

        $book->delete();

        return redirect(url('/admin/library/books'))->with('success', 'Book deleted successfully.');
    }
}
