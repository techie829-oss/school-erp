<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Department;
use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\TeacherQualification;
use App\Models\TeacherDocument;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of teachers
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $query = Teacher::forTenant($tenant->id)
            ->with(['department']);

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by department
        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by employment type
        if ($request->has('employment_type') && $request->employment_type) {
            $query->where('employment_type', $request->employment_type);
        }

        // Filter by gender
        if ($request->has('gender') && $request->gender) {
            $query->where('gender', $request->gender);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $teachers = $query->paginate(15)->withQueryString();

        // Get filter options
        $departments = Department::forTenant($tenant->id)->active()->get();

        // Statistics
        $stats = [
            'total' => Teacher::forTenant($tenant->id)->count(),
            'active' => Teacher::forTenant($tenant->id)->active()->count(),
            'on_leave' => Teacher::forTenant($tenant->id)->where('status', 'on_leave')->count(),
            'by_department' => Teacher::forTenant($tenant->id)
                ->select('department_id', DB::raw('count(*) as count'))
                ->whereNotNull('department_id')
                ->groupBy('department_id')
                ->get()
                ->pluck('count', 'department_id')
                ->toArray(),
        ];

        return view('tenant.admin.teachers.index', compact(
            'teachers',
            'departments',
            'stats',
            'tenant'
        ));
    }

    /**
     * Show the form for creating a new teacher
     */
    public function create(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $departments = Department::forTenant($tenant->id)->active()->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        // Generate employee ID
        $employeeId = Teacher::generateEmployeeId($tenant->id);

        return view('tenant.admin.teachers.create', compact(
            'departments',
            'subjects',
            'classes',
            'employeeId',
            'tenant'
        ));
    }

    /**
     * Store a newly created teacher in storage
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|string|unique:teachers,employee_id',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'blood_group' => 'nullable|string|max:10',
            'nationality' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:50',
            'category' => 'nullable|in:general,obc,sc,st,other',
            'email' => 'nullable|email|max:255|unique:teachers,email',
            'phone' => 'nullable|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            // Employment
            'department_id' => 'nullable|exists:departments,id',
            'designation' => 'nullable|string|max:100',
            'employment_type' => 'required|in:permanent,contract,temporary,visiting',
            'date_of_joining' => 'required|date',
            'highest_qualification' => 'nullable|string|max:100',
            'experience_years' => 'nullable|numeric|min:0|max:50',

            // Financial
            'salary_amount' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:100',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc_code' => 'nullable|string|max:20',

            // Address
            'current_address' => 'nullable|string|max:500',
            'current_city' => 'nullable|string|max:100',
            'current_state' => 'nullable|string|max:100',
            'current_pincode' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('teachers/photos', 'public');
            }

            // Prepare address data
            $currentAddress = null;
            if ($request->filled('current_address')) {
                $currentAddress = [
                    'address' => $request->current_address,
                    'city' => $request->current_city,
                    'state' => $request->current_state,
                    'pincode' => $request->current_pincode,
                    'country' => 'India',
                ];
            }

            $permanentAddress = null;
            if ($request->has('same_as_current') && $request->same_as_current) {
                $permanentAddress = $currentAddress;
            } elseif ($request->filled('permanent_address')) {
                $permanentAddress = [
                    'address' => $request->permanent_address,
                    'city' => $request->permanent_city,
                    'state' => $request->permanent_state,
                    'pincode' => $request->permanent_pincode,
                    'country' => 'India',
                ];
            }

            // Create teacher
            $teacher = Teacher::create([
                'tenant_id' => $tenant->id,
                'employee_id' => $request->employee_id,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'blood_group' => $request->blood_group,
                'nationality' => $request->nationality ?? 'Indian',
                'religion' => $request->religion,
                'category' => $request->category,
                'email' => $request->email,
                'phone' => $request->phone,
                'alternate_phone' => $request->alternate_phone,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relation' => $request->emergency_contact_relation,
                'current_address' => $currentAddress,
                'permanent_address' => $permanentAddress,
                'department_id' => $request->department_id,
                'designation' => $request->designation,
                'employment_type' => $request->employment_type,
                'date_of_joining' => $request->date_of_joining,
                'highest_qualification' => $request->highest_qualification,
                'experience_years' => $request->experience_years,
                'salary_amount' => $request->salary_amount,
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_ifsc_code' => $request->bank_ifsc_code,
                'pan_number' => $request->pan_number,
                'aadhar_number' => $request->aadhar_number,
                'photo' => $photoPath,
                'notes' => $request->notes,
                'is_active' => true,
                'status' => 'active',
            ]);

            // Assign subjects if provided
            if ($request->has('subjects') && is_array($request->subjects)) {
                foreach ($request->subjects as $subjectId) {
                    $teacher->subjects()->attach($subjectId, [
                        'tenant_id' => $tenant->id,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('tenant.admin.teachers.show', ['subdomain' => $tenant->id, 'teacherId' => $teacher->id])
                ->with('success', 'Teacher created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Delete uploaded photo if exists
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return back()
                ->with('error', 'Failed to create teacher: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified teacher
     */
    public function show(Request $request, $teacherId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $teacher = Teacher::with([
            'department',
            'qualifications',
            'subjects',
            'documents',
            'classesTaught.schoolClass'
        ])->findOrFail($teacherId);

        // Ensure teacher belongs to tenant
        if ($teacher->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        $departments = Department::forTenant($tenant->id)->active()->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.teachers.show', compact(
            'teacher',
            'departments',
            'subjects',
            'classes',
            'tenant'
        ));
    }

    /**
     * Show the form for editing the specified teacher
     */
    public function edit(Request $request, $teacherId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $teacher = Teacher::with(['subjects'])->findOrFail($teacherId);

        // Ensure teacher belongs to tenant
        if ($teacher->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        $departments = Department::forTenant($tenant->id)->active()->get();
        $subjects = Subject::forTenant($tenant->id)->active()->get();
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();

        return view('tenant.admin.teachers.edit', compact(
            'teacher',
            'departments',
            'subjects',
            'classes',
            'tenant'
        ));
    }

    /**
     * Update the specified teacher in storage
     */
    public function update(Request $request, $teacherId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $teacher = Teacher::findOrFail($teacherId);

        // Ensure teacher belongs to tenant
        if ($teacher->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'email' => 'nullable|email|max:255|unique:teachers,email,' . $teacher->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'employment_type' => 'required|in:permanent,contract,temporary,visiting',
            'date_of_joining' => 'required|date',
            'salary_amount' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Handle photo upload
            $photoPath = $teacher->photo;
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                    Storage::disk('public')->delete($photoPath);
                }
                $photoPath = $request->file('photo')->store('teachers/photos', 'public');
            }

            // Prepare address data
            $currentAddress = null;
            if ($request->filled('current_address')) {
                $currentAddress = [
                    'address' => $request->current_address,
                    'city' => $request->current_city,
                    'state' => $request->current_state,
                    'pincode' => $request->current_pincode,
                    'country' => 'India',
                ];
            }

            $permanentAddress = null;
            if ($request->has('same_as_current') && $request->same_as_current) {
                $permanentAddress = $currentAddress;
            } elseif ($request->filled('permanent_address')) {
                $permanentAddress = [
                    'address' => $request->permanent_address,
                    'city' => $request->permanent_city,
                    'state' => $request->permanent_state,
                    'pincode' => $request->permanent_pincode,
                    'country' => 'India',
                ];
            }

            // Update teacher
            $teacher->update([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'blood_group' => $request->blood_group,
                'nationality' => $request->nationality ?? 'Indian',
                'religion' => $request->religion,
                'category' => $request->category,
                'email' => $request->email,
                'phone' => $request->phone,
                'alternate_phone' => $request->alternate_phone,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relation' => $request->emergency_contact_relation,
                'current_address' => $currentAddress,
                'permanent_address' => $permanentAddress,
                'department_id' => $request->department_id,
                'designation' => $request->designation,
                'employment_type' => $request->employment_type,
                'date_of_joining' => $request->date_of_joining,
                'date_of_leaving' => $request->date_of_leaving,
                'highest_qualification' => $request->highest_qualification,
                'experience_years' => $request->experience_years,
                'salary_amount' => $request->salary_amount,
                'bank_name' => $request->bank_name,
                'bank_account_number' => $request->bank_account_number,
                'bank_ifsc_code' => $request->bank_ifsc_code,
                'pan_number' => $request->pan_number,
                'aadhar_number' => $request->aadhar_number,
                'photo' => $photoPath,
                'notes' => $request->notes,
                'status' => $request->status ?? $teacher->status,
            ]);

            // Update subjects if provided
            if ($request->has('subjects')) {
                $teacher->subjects()->sync([]);
                if (is_array($request->subjects)) {
                    foreach ($request->subjects as $subjectId) {
                        $teacher->subjects()->attach($subjectId, [
                            'tenant_id' => $tenant->id,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('tenant.admin.teachers.show', ['subdomain' => $tenant->id, 'teacherId' => $teacher->id])
                ->with('success', 'Teacher updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Failed to update teacher: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified teacher from storage
     */
    public function destroy(Request $request, $teacherId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $teacher = Teacher::findOrFail($teacherId);

        // Ensure teacher belongs to tenant
        if ($teacher->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        try {
            $teacher->delete(); // Soft delete

            return redirect()
                ->route('tenant.admin.teachers.index', ['subdomain' => $tenant->id])
                ->with('success', 'Teacher deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete teacher: ' . $e->getMessage());
        }
    }

    /**
     * Add qualification to teacher
     */
    public function addQualification(Request $request, $teacherId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);
        $teacher = Teacher::findOrFail($teacherId);

        if ($teacher->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'qualification_type' => 'required|in:academic,professional,certification,training',
            'degree_name' => 'required|string|max:255',
            'institution_name' => 'required|string|max:255',
            'year_of_passing' => 'required|integer|min:1950|max:' . date('Y'),
            'certificate_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $documentPath = null;
            if ($request->hasFile('certificate_document')) {
                $documentPath = $request->file('certificate_document')->store('teachers/qualifications', 'public');
            }

            TeacherQualification::create([
                'tenant_id' => $tenant->id,
                'teacher_id' => $teacher->id,
                'qualification_type' => $request->qualification_type,
                'degree_name' => $request->degree_name,
                'specialization' => $request->specialization,
                'institution_name' => $request->institution_name,
                'university_board' => $request->university_board,
                'year_of_passing' => $request->year_of_passing,
                'grade_percentage' => $request->grade_percentage,
                'certificate_number' => $request->certificate_number,
                'certificate_document' => $documentPath,
            ]);

            return back()->with('success', 'Qualification added successfully!');

        } catch (\Exception $e) {
            if ($documentPath && Storage::disk('public')->exists($documentPath)) {
                Storage::disk('public')->delete($documentPath);
            }

            return back()->with('error', 'Failed to add qualification: ' . $e->getMessage());
        }
    }

    /**
     * Upload document for teacher
     */
    public function uploadDocument(Request $request, $teacherId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);
        $teacher = Teacher::findOrFail($teacherId);

        if ($teacher->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|in:resume,certificate,experience_letter,id_proof,address_proof,photo,other',
            'document_file' => 'required|file|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('document_file');
            $filePath = $file->store('teachers/documents', 'public');

            TeacherDocument::create([
                'tenant_id' => $tenant->id,
                'teacher_id' => $teacher->id,
                'document_name' => $request->document_name,
                'document_type' => $request->document_type,
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => auth()->id(),
            ]);

            return back()->with('success', 'Document uploaded successfully!');

        } catch (\Exception $e) {
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return back()->with('error', 'Failed to upload document: ' . $e->getMessage());
        }
    }

    /**
     * Delete a document
     */
    public function deleteDocument(Request $request, $documentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);
        $document = TeacherDocument::findOrFail($documentId);

        if ($document->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        try {
            // Delete file from storage
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            $document->delete();

            return back()->with('success', 'Document deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete document: ' . $e->getMessage());
        }
    }
}

