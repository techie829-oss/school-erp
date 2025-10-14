<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of students
     */
    public function index(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $query = Student::forTenant($tenant->id)
            ->with(['currentEnrollment.schoolClass', 'currentEnrollment.section']);

        // Search
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by class
        if ($request->has('class_id') && $request->class_id) {
            $query->inClass($request->class_id);
        }

        // Filter by section
        if ($request->has('section_id') && $request->section_id) {
            $query->inSection($request->section_id);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('overall_status', $request->status);
        }

        // Filter by academic year
        if ($request->has('academic_year') && $request->academic_year) {
            $query->where('academic_year', $request->academic_year);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $students = $query->paginate(15)->withQueryString();

        // Get filter options
        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $sections = Section::forTenant($tenant->id)->active()->get();

        return view('tenant.admin.students.index', compact(
            'students',
            'classes',
            'sections',
            'tenant'
        ));
    }

    /**
     * Show the form for creating a new student
     */
    public function create(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $sections = Section::forTenant($tenant->id)->active()->get();

        // Generate admission number
        $admissionNumber = Student::generateAdmissionNumber($tenant->id);

        return view('tenant.admin.students.create', compact(
            'classes',
            'sections',
            'admissionNumber',
            'tenant'
        ));
    }

    /**
     * Store a newly created student in storage
     */
    public function store(Request $request)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'blood_group' => 'nullable|string|max:10',
            'nationality' => 'nullable|string|max:100',
            'religion' => 'nullable|string|max:100',
            'category' => 'required|in:general,obc,sc,st,other',
            'email' => 'nullable|email|max:255|unique:students,email',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'current_class_id' => 'required|exists:classes,id',
            'current_section_id' => 'nullable|exists:sections,id',
            'roll_number' => 'nullable|string|max:50',
            'admission_date' => 'required|date',
            'academic_year' => 'required|string|max:20',
            'admission_number' => 'required|string|unique:students,admission_number',

            // Parent details
            'father_name' => 'nullable|string|max:255',
            'father_phone' => 'nullable|string|max:20',
            'father_email' => 'nullable|email|max:255',
            'mother_name' => 'nullable|string|max:255',
            'mother_phone' => 'nullable|string|max:20',
            'mother_email' => 'nullable|email|max:255',

            // Address
            'current_address' => 'nullable|string|max:500',
            'current_city' => 'nullable|string|max:100',
            'current_state' => 'nullable|string|max:100',
            'current_pincode' => 'nullable|string|max:10',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('student-photos', 'public');
        }

        // Prepare address data
        $currentAddress = null;
        if ($request->current_address) {
            $currentAddress = [
                'address' => $request->current_address,
                'city' => $request->current_city,
                'state' => $request->current_state,
                'pincode' => $request->current_pincode,
                'country' => $request->current_country ?? 'India',
            ];
        }

        $permanentAddress = $request->same_as_current ? $currentAddress : null;
        if (!$request->same_as_current && $request->permanent_address) {
            $permanentAddress = [
                'address' => $request->permanent_address,
                'city' => $request->permanent_city,
                'state' => $request->permanent_state,
                'pincode' => $request->permanent_pincode,
                'country' => $request->permanent_country ?? 'India',
            ];
        }

        // Create student
        $student = Student::create([
            'tenant_id' => $tenant->id,
            'admission_number' => $request->admission_number,
            'admission_date' => $request->admission_date,
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
            'photo' => $photoPath,
            'current_address' => $currentAddress,
            'permanent_address' => $permanentAddress,
            'same_as_current' => $request->boolean('same_as_current'),
            'father_name' => $request->father_name,
            'father_occupation' => $request->father_occupation,
            'father_phone' => $request->father_phone,
            'father_email' => $request->father_email,
            'mother_name' => $request->mother_name,
            'mother_occupation' => $request->mother_occupation,
            'mother_phone' => $request->mother_phone,
            'mother_email' => $request->mother_email,
            'guardian_name' => $request->guardian_name,
            'guardian_relation' => $request->guardian_relation,
            'guardian_phone' => $request->guardian_phone,
            'guardian_email' => $request->guardian_email,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relation' => $request->emergency_contact_relation,
            'previous_school_name' => $request->previous_school_name,
            'previous_class' => $request->previous_class,
            'tc_number' => $request->tc_number,
            'overall_status' => 'active',
            'is_active' => true,
        ]);

        // Create first enrollment (current class)
        $student->enrollInClass(
            $request->current_class_id,
            $request->current_section_id,
            $request->academic_year,
            $request->roll_number
        );

        return redirect('/admin/students/' . $student->id)
            ->with('success', 'Student added successfully!');
    }

    /**
     * Display the specified student
     */
    public function show(Request $request, $studentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $student = Student::where('tenant_id', $tenant->id)
            ->where('id', $studentId)
            ->with(['currentEnrollment.schoolClass', 'currentEnrollment.section', 'enrollments.schoolClass', 'enrollments.section', 'documents'])
            ->firstOrFail();

        return view('tenant.admin.students.show', compact('student', 'tenant'));
    }

    /**
     * Show the form for editing the specified student
     */
    public function edit(Request $request, $studentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            abort(404, 'Tenant not found');
        }

        $student = Student::where('tenant_id', $tenant->id)->where('id', $studentId)->firstOrFail();

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $sections = Section::forTenant($tenant->id)->active()->get();

        return view('tenant.admin.students.edit', compact(
            'student',
            'classes',
            'sections',
            'tenant'
        ));
    }

    /**
     * Update the specified student in storage
     */
    public function update(Request $request, $studentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $student = Student::where('tenant_id', $tenant->id)->where('id', $studentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'email' => 'nullable|email|max:255|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'current_class_id' => 'required|exists:classes,id',
            'current_section_id' => 'nullable|exists:sections,id',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please fix the validation errors.');
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $student->photo = $request->file('photo')->store('student-photos', 'public');
        }

        // Update student basic data
        $student->update($request->except(['photo', '_token', '_method', 'current_class_id', 'current_section_id', 'roll_number']));

        // Handle enrollment changes
        if ($request->has('current_class_id') && $request->current_class_id) {
            $currentEnrollment = $student->currentEnrollment;

            if ($currentEnrollment) {
                // Update existing enrollment
                $currentEnrollment->update([
                    'class_id' => $request->current_class_id,
                    'section_id' => $request->current_section_id,
                    'roll_number' => $request->roll_number,
                ]);
            } else {
                // Create new enrollment if none exists
                $student->enrollInClass(
                    $request->current_class_id,
                    $request->current_section_id,
                    date('Y') . '-' . (date('Y') + 1),
                    $request->roll_number
                );
            }
        } elseif ($request->has('roll_number') && $student->currentEnrollment) {
            // Just update roll number
            $student->currentEnrollment->update(['roll_number' => $request->roll_number]);
        }

        return back()->with('success', 'Student updated successfully!');
    }

    /**
     * Remove the specified student from storage
     */
    public function destroy(Request $request, $studentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $student = Student::where('tenant_id', $tenant->id)->where('id', $studentId)->firstOrFail();

        // Delete photo
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        // Soft delete
        $student->delete();

        return redirect('/admin/students')
            ->with('success', 'Student deleted successfully!');
    }
}
