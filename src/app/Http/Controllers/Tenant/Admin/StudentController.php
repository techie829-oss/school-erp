<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\StudentSubject;
use App\Models\TenantSetting;
use App\Services\TenantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
        // Note: Sections are optional - not all classes use sections
        // Some classes (e.g., 0-8) may not have sections, while others (e.g., 9-12) do
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

        // Get subject assignment settings
        $academicSettings = TenantSetting::getAllForTenant($tenant->id, 'academic');
        $classSubjectMode = $academicSettings['class_subject_assignment_mode'] ?? 'class_wise';
        $sectionSubjectMode = $academicSettings['section_subject_assignment_mode'] ?? 'section_wise';

        // Get all subjects for selection
        $allSubjects = Subject::forTenant($tenant->id)->active()->get();

        // Generate admission number
        $admissionNumber = Student::generateAdmissionNumber($tenant->id);

        return view('tenant.admin.students.create', compact(
            'classes',
            'sections',
            'admissionNumber',
            'tenant',
            'classSubjectMode',
            'sectionSubjectMode',
            'allSubjects'
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
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('students', 'email')->where('tenant_id', $tenant->id),
            ],
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'current_class_id' => 'required|exists:classes,id',
            'current_section_id' => 'nullable|exists:sections,id',
            'roll_number' => 'nullable|string|max:50',
            'admission_date' => 'required|date',
            'academic_year' => 'required|string|max:20',
            'admission_number' => [
                'required',
                'string',
                Rule::unique('students', 'admission_number')->where('tenant_id', $tenant->id),
            ],

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
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
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

        // Handle subject assignment (if student-wise is enabled)
        if ($request->has('subjects') && $request->academic_year) {
            $academicYear = $request->academic_year;
            $subjectIds = $request->input('subjects', []);

            // Get subject assignment settings
            $academicSettings = TenantSetting::getAllForTenant($tenant->id, 'academic');
            $classSubjectMode = $academicSettings['class_subject_assignment_mode'] ?? 'class_wise';
            $sectionSubjectMode = $academicSettings['section_subject_assignment_mode'] ?? 'section_wise';

            // Check if student-wise is enabled
            $currentClass = SchoolClass::find($request->current_class_id);
            $currentSection = $request->current_section_id ? Section::find($request->current_section_id) : null;

            $allowStudentWise = false;
            if ($currentSection && $currentClass && $currentClass->has_sections) {
                $allowStudentWise = ($sectionSubjectMode === 'student_wise');
            } elseif ($currentClass) {
                $allowStudentWise = ($classSubjectMode === 'student_wise');
            }

            if ($allowStudentWise && !empty($subjectIds)) {
                // Add subject assignments
                foreach ($subjectIds as $subjectId) {
                    StudentSubject::create([
                        'tenant_id' => $tenant->id,
                        'student_id' => $student->id,
                        'subject_id' => $subjectId,
                        'academic_year' => $academicYear,
                        'is_active' => true,
                    ]);
                }
            }
        }

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

        // Get classes and sections for promotion form
        $classes = SchoolClass::forTenant($tenant->id)->orderBy('class_name')->get();
        $sections = Section::forTenant($tenant->id)->with('schoolClass')->orderBy('section_name')->get();

        // Attendance calendar data (per-student)
        $month = (int) $request->get('attendance_month', now()->month);
        $year = (int) $request->get('attendance_year', now()->year);

        // Clamp month/year to valid values
        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }
        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        $monthStart = now()->setDate($year, $month, 1)->startOfDay();
        $daysInMonth = $monthStart->daysInMonth;
        $monthEnd = $monthStart->copy()->endOfMonth();

        $attendanceRecords = \App\Models\StudentAttendance::forTenant($tenant->id)
            ->forStudent($student->id)
            ->whereBetween('attendance_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->get()
            ->keyBy(function ($record) {
                return $record->attendance_date->day;
            });

        $attendanceCalendar = [];
        $summary = [
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'half_day' => 0,
            'on_leave' => 0,
            'holiday' => 0,
            'total_marked' => 0,
        ];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = $monthStart->copy()->day($day);
            $record = $attendanceRecords->get($day);

            if ($record) {
                $status = $record->status;
                $color = $record->status_color;
                if (isset($summary[$status])) {
                    $summary[$status]++;
                }
                $summary['total_marked']++;
            } else {
                $status = null;
                $color = 'gray';
            }

            $attendanceCalendar[] = [
                'day' => $day,
                'date' => $date,
                'status' => $status,
                'color' => $color,
            ];
        }

        // Get exam results for this student
        $examResults = \App\Models\ExamResult::forTenant($tenant->id)
            ->where('student_id', $student->id)
            ->with(['exam', 'subject', 'examSchedule', 'schoolClass', 'section'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('exam_id');

        // Get admit cards and report cards
        $admitCards = \App\Models\AdmitCard::forTenant($tenant->id)
            ->where('student_id', $student->id)
            ->with('exam')
            ->orderBy('created_at', 'desc')
            ->get();

        $reportCards = \App\Models\ReportCard::forTenant($tenant->id)
            ->where('student_id', $student->id)
            ->with('exam')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tenant.admin.students.show', compact(
            'student',
            'tenant',
            'classes',
            'sections',
            'attendanceCalendar',
            'summary',
            'month',
            'year',
            'examResults',
            'admitCards',
            'reportCards'
        ));
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

        $student = Student::where('tenant_id', $tenant->id)
            ->with(['currentEnrollment', 'studentSubjects'])
            ->where('id', $studentId)
            ->firstOrFail();

        $classes = SchoolClass::forTenant($tenant->id)->active()->ordered()->get();
        $sections = Section::forTenant($tenant->id)->active()->get();

        // Get subject assignment settings
        $academicSettings = TenantSetting::getAllForTenant($tenant->id, 'academic');
        $classSubjectMode = $academicSettings['class_subject_assignment_mode'] ?? 'class_wise';
        $sectionSubjectMode = $academicSettings['section_subject_assignment_mode'] ?? 'section_wise';

        // Get current enrollment info
        $currentEnrollment = $student->currentEnrollment;
        $currentClass = $currentEnrollment ? $currentEnrollment->schoolClass : null;
        $currentSection = $currentEnrollment ? $currentEnrollment->section : null;
        $academicYear = $currentEnrollment ? $currentEnrollment->academic_year : null;

        // Determine if student-wise assignment is enabled
        $allowStudentWise = false;
        $subjectsFrom = null; // 'class' or 'section'

        if ($currentSection && $currentClass && $currentClass->has_sections) {
            // Student has section - check section setting
            $allowStudentWise = ($sectionSubjectMode === 'student_wise');
            $subjectsFrom = 'section';
        } elseif ($currentClass) {
            // Student has class but no section - check class setting
            $allowStudentWise = ($classSubjectMode === 'student_wise');
            $subjectsFrom = 'class';
        }

        // Get all subjects for selection
        $allSubjects = Subject::forTenant($tenant->id)->active()->get();

        // Get subjects from class/section if not student-wise
        $classOrSectionSubjects = collect();
        if (!$allowStudentWise) {
            if ($subjectsFrom === 'section' && $currentSection) {
                $classOrSectionSubjects = $currentSection->subjects;
            } elseif ($subjectsFrom === 'class' && $currentClass) {
                $classOrSectionSubjects = $currentClass->subjects;
            }
        }

        // Get student's assigned subjects for current academic year
        $studentSubjectIds = [];
        if ($academicYear && $allowStudentWise) {
            $studentSubjectIds = StudentSubject::forTenant($tenant->id)
                ->forStudent($student->id)
                ->forAcademicYear($academicYear)
                ->active()
                ->pluck('subject_id')
                ->toArray();
        }

        return view('tenant.admin.students.edit', compact(
            'student',
            'classes',
            'sections',
            'tenant',
            'allowStudentWise',
            'subjectsFrom',
            'allSubjects',
            'classOrSectionSubjects',
            'studentSubjectIds',
            'academicYear',
            'currentClass',
            'currentSection'
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
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('students', 'email')->where('tenant_id', $tenant->id)->ignore($student->id),
            ],
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'current_class_id' => 'required|exists:classes,id',
            'current_section_id' => 'nullable|exists:sections,id',
            'subjects' => 'nullable|array',
            'subjects.*' => 'exists:subjects,id',
            'academic_year' => 'nullable|string|max:20',
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

        // Handle subject assignment (if student-wise is enabled)
        if ($request->has('subjects') && $request->has('academic_year') && $request->academic_year) {
            $academicYear = $request->academic_year;
            $subjectIds = $request->input('subjects', []);

            // Get subject assignment settings
            $academicSettings = TenantSetting::getAllForTenant($tenant->id, 'academic');
            $classSubjectMode = $academicSettings['class_subject_assignment_mode'] ?? 'class_wise';
            $sectionSubjectMode = $academicSettings['section_subject_assignment_mode'] ?? 'section_wise';

            // Check if student-wise is enabled
            $currentEnrollment = $student->currentEnrollment;
            $currentClass = $currentEnrollment ? $currentEnrollment->schoolClass : null;
            $currentSection = $currentEnrollment ? $currentEnrollment->section : null;

            $allowStudentWise = false;
            if ($currentSection && $currentClass && $currentClass->has_sections) {
                $allowStudentWise = ($sectionSubjectMode === 'student_wise');
            } elseif ($currentClass) {
                $allowStudentWise = ($classSubjectMode === 'student_wise');
            }

            if ($allowStudentWise) {
                // Remove existing subjects for this academic year
                StudentSubject::forTenant($tenant->id)
                    ->forStudent($student->id)
                    ->forAcademicYear($academicYear)
                    ->delete();

                // Add new subject assignments
                foreach ($subjectIds as $subjectId) {
                    StudentSubject::create([
                        'tenant_id' => $tenant->id,
                        'student_id' => $student->id,
                        'subject_id' => $subjectId,
                        'academic_year' => $academicYear,
                        'is_active' => true,
                    ]);
                }
            }
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

    /**
     * Promote student to next class
     */
    public function promote(Request $request, $studentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $student = Student::where('tenant_id', $tenant->id)->where('id', $studentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'to_class_id' => 'required|exists:classes,id',
            'to_section_id' => 'nullable|exists:sections,id',
            'academic_year' => 'required|string|max:20',
            'roll_number' => 'nullable|string|max:50',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'grade' => 'nullable|string|max:10',
            'remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $student->promoteToClass(
                $request->to_class_id,
                $request->to_section_id,
                $request->academic_year,
                $request->percentage,
                $request->grade,
                $request->remarks,
                $request->roll_number
            );

            return back()->with('success', 'Student promoted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to promote student: ' . $e->getMessage());
        }
    }

    /**
     * Update academic status
     */
    public function updateAcademicStatus(Request $request, $studentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $student = Student::where('tenant_id', $tenant->id)->where('id', $studentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'overall_status' => 'required|in:active,alumni,transferred,dropped_out',
            'status_remarks' => 'nullable|string|max:500',
            'is_active' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $student->update([
            'overall_status' => $request->overall_status,
            'status_remarks' => $request->status_remarks,
            'is_active' => $request->is_active,
        ]);

        // If student is marked as inactive or not active status, deactivate current enrollment
        if (!$request->is_active || $request->overall_status !== 'active') {
            $currentEnrollment = $student->currentEnrollment;
            if ($currentEnrollment) {
                $currentEnrollment->update([
                    'is_current' => false,
                    'end_date' => now(),
                    'enrollment_status' => $request->overall_status,
                ]);
            }
        }

        return back()->with('success', 'Academic status updated successfully!');
    }

    /**
     * Complete current enrollment (pass/fail/transfer)
     */
    public function completeEnrollment(Request $request, $studentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);

        if (!$tenant) {
            return back()->with('error', 'Tenant not found');
        }

        $student = Student::where('tenant_id', $tenant->id)->where('id', $studentId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'result' => 'required|in:passed,failed,transferred,dropped',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'grade' => 'nullable|string|max:10',
            'remarks' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $currentEnrollment = $student->currentEnrollment;
        if (!$currentEnrollment) {
            return back()->with('error', 'No active enrollment found for this student');
        }

        $currentEnrollment->markAsCompleted(
            $request->result,
            $request->percentage,
            $request->grade,
            $request->remarks
        );

        return back()->with('success', 'Enrollment completed successfully!');
    }

    /**
     * Upload document for student
     */
    public function uploadDocument(Request $request, $studentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);
        $student = Student::findOrFail($studentId);

        if ($student->tenant_id !== $tenant->id) {
            abort(403, 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'document_name' => 'required|string|max:255',
            'document_type' => 'required|in:birth_certificate,id_proof,address_proof,previous_marksheet,transfer_certificate,medical_certificate,photo,other',
            'document_file' => 'required|file|max:10240', // 10MB
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $file = $request->file('document_file');
            $filePath = $file->store('students/documents', 'public');

            \App\Models\StudentDocument::create([
                'tenant_id' => $tenant->id,
                'student_id' => $student->id,
                'document_name' => $request->document_name,
                'document_type' => $request->document_type,
                'file_path' => $filePath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => auth()->id(),
                'uploaded_at' => now(),
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
     * Delete a student document
     */
    public function deleteDocument(Request $request, $documentId)
    {
        $tenant = $this->tenantService->getCurrentTenant($request);
        $document = \App\Models\StudentDocument::findOrFail($documentId);

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
