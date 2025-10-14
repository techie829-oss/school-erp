# 👨‍🏫 Teacher Management System - Detailed Implementation Plan

## 📋 Overview

Complete Teacher Management System for School ERP with comprehensive teacher profiles, qualifications, subject assignments, attendance tracking, and performance management.

---

## 🎯 Core Objectives

1. **Teacher Profile Management** - Complete professional profiles
2. **Qualification Tracking** - Education and certifications
3. **Subject Assignment** - Link teachers to subjects and classes
4. **Class Teacher Assignment** - Assign class teachers to sections
5. **Employment Details** - Contract, salary, department info
6. **Document Management** - Store important documents
7. **Attendance Tracking** - Daily teacher attendance
8. **Leave Management** - Leave requests and approvals
9. **Performance Tracking** - Reviews and ratings
10. **Timetable Integration** - Teacher schedule management

---

## 📊 Database Schema

### 1. **teachers** Table (Main)

```sql
CREATE TABLE teachers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id VARCHAR(255) NOT NULL,
    user_id BIGINT UNSIGNED NULL, -- Link to users table for login
    
    -- Personal Information
    employee_id VARCHAR(50) UNIQUE NOT NULL, -- Auto-generated: TCH-2025-001
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100) NULL,
    last_name VARCHAR(100) NOT NULL,
    full_name VARCHAR(255) GENERATED ALWAYS AS (CONCAT(first_name, ' ', IFNULL(CONCAT(middle_name, ' '), ''), last_name)),
    
    gender ENUM('male', 'female', 'other') NOT NULL,
    date_of_birth DATE NOT NULL,
    blood_group VARCHAR(10) NULL,
    nationality VARCHAR(100) DEFAULT 'Indian',
    religion VARCHAR(50) NULL,
    category ENUM('general', 'obc', 'sc', 'st', 'other') NULL,
    
    -- Contact Information
    email VARCHAR(255) UNIQUE NULL,
    phone VARCHAR(20) NULL,
    alternate_phone VARCHAR(20) NULL,
    emergency_contact_name VARCHAR(255) NULL,
    emergency_contact_phone VARCHAR(20) NULL,
    emergency_contact_relation VARCHAR(50) NULL,
    
    -- Address
    current_address JSON NULL, -- {address, city, state, pincode, country}
    permanent_address JSON NULL,
    
    -- Employment Details
    department_id BIGINT UNSIGNED NULL,
    designation VARCHAR(100) NULL, -- Principal, Vice Principal, Head Teacher, Teacher, etc.
    employment_type ENUM('permanent', 'contract', 'temporary', 'visiting') DEFAULT 'permanent',
    date_of_joining DATE NOT NULL,
    date_of_leaving DATE NULL,
    
    -- Qualifications (Summary)
    highest_qualification VARCHAR(100) NULL, -- B.Ed, M.Ed, PhD, etc.
    experience_years DECIMAL(4,1) NULL, -- Total experience in years
    
    -- Salary & Financial
    salary_amount DECIMAL(10,2) NULL,
    bank_name VARCHAR(100) NULL,
    bank_account_number VARCHAR(50) NULL,
    bank_ifsc_code VARCHAR(20) NULL,
    pan_number VARCHAR(20) NULL,
    aadhar_number VARCHAR(20) NULL,
    
    -- System Fields
    photo VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    status ENUM('active', 'on_leave', 'resigned', 'retired', 'terminated') DEFAULT 'active',
    status_remarks TEXT NULL,
    notes TEXT NULL,
    
    -- Timestamps
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL, -- Soft delete
    
    -- Indexes
    INDEX idx_tenant_id (tenant_id),
    INDEX idx_employee_id (employee_id),
    INDEX idx_user_id (user_id),
    INDEX idx_department_id (department_id),
    INDEX idx_status (status, is_active),
    
    -- Foreign Keys
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
);
```

### 2. **teacher_qualifications** Table

```sql
CREATE TABLE teacher_qualifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id VARCHAR(255) NOT NULL,
    teacher_id BIGINT UNSIGNED NOT NULL,
    
    qualification_type ENUM('academic', 'professional', 'certification', 'training') DEFAULT 'academic',
    degree_name VARCHAR(255) NOT NULL, -- B.Ed, M.Ed, B.Sc, M.Sc, etc.
    specialization VARCHAR(255) NULL, -- Mathematics, Physics, etc.
    institution_name VARCHAR(255) NOT NULL,
    university_board VARCHAR(255) NULL,
    
    year_of_passing YEAR NOT NULL,
    grade_percentage VARCHAR(20) NULL,
    certificate_number VARCHAR(100) NULL,
    
    -- Document
    certificate_document VARCHAR(255) NULL,
    
    -- Verification
    is_verified BOOLEAN DEFAULT FALSE,
    verified_by BIGINT UNSIGNED NULL,
    verified_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_teacher_id (teacher_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
);
```

### 3. **teacher_subjects** Table (Many-to-Many)

```sql
CREATE TABLE teacher_subjects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id VARCHAR(255) NOT NULL,
    teacher_id BIGINT UNSIGNED NOT NULL,
    subject_id BIGINT UNSIGNED NOT NULL,
    class_id BIGINT UNSIGNED NULL, -- Specific class or NULL for all classes
    
    is_primary BOOLEAN DEFAULT FALSE, -- Primary subject expertise
    years_teaching DECIMAL(3,1) NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_teacher_subject_class (teacher_id, subject_id, class_id),
    INDEX idx_teacher_id (teacher_id),
    INDEX idx_subject_id (subject_id),
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE
);
```

### 4. **teacher_documents** Table

```sql
CREATE TABLE teacher_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id VARCHAR(255) NOT NULL,
    teacher_id BIGINT UNSIGNED NOT NULL,
    
    document_name VARCHAR(255) NOT NULL,
    document_type ENUM('resume', 'certificate', 'experience_letter', 'id_proof', 'address_proof', 'photo', 'other') NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size BIGINT NULL, -- in bytes
    mime_type VARCHAR(100) NULL,
    
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    uploaded_by BIGINT UNSIGNED NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_teacher_id (teacher_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
);
```

### 5. **teacher_attendance** Table

```sql
CREATE TABLE teacher_attendance (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id VARCHAR(255) NOT NULL,
    teacher_id BIGINT UNSIGNED NOT NULL,
    
    attendance_date DATE NOT NULL,
    status ENUM('present', 'absent', 'late', 'half_day', 'on_leave') NOT NULL,
    
    -- Timing
    check_in_time TIME NULL,
    check_out_time TIME NULL,
    total_hours DECIMAL(4,2) NULL,
    
    -- Leave Details (if on_leave)
    leave_type VARCHAR(50) NULL, -- Sick, Casual, Earned, etc.
    leave_id BIGINT UNSIGNED NULL, -- Reference to leave request
    
    remarks TEXT NULL,
    marked_by BIGINT UNSIGNED NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_teacher_date (teacher_id, attendance_date),
    INDEX idx_attendance_date (attendance_date),
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
);
```

### 6. **teacher_leaves** Table

```sql
CREATE TABLE teacher_leaves (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id VARCHAR(255) NOT NULL,
    teacher_id BIGINT UNSIGNED NOT NULL,
    
    leave_type ENUM('sick', 'casual', 'earned', 'maternity', 'paternity', 'compensatory', 'other') NOT NULL,
    
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_days DECIMAL(3,1) NOT NULL, -- Can be 0.5 for half day
    
    reason TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    
    -- Approval workflow
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    approval_remarks TEXT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_teacher_id (teacher_id),
    INDEX idx_dates (start_date, end_date),
    INDEX idx_status (status),
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
);
```

### 7. **departments** Table (Supporting)

```sql
CREATE TABLE departments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id VARCHAR(255) NOT NULL,
    
    department_name VARCHAR(100) NOT NULL,
    department_code VARCHAR(20) NULL,
    description TEXT NULL,
    
    head_teacher_id BIGINT UNSIGNED NULL, -- Department head
    
    is_active BOOLEAN DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tenant_id (tenant_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);
```

### 8. **subjects** Table (Supporting)

```sql
CREATE TABLE subjects (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tenant_id VARCHAR(255) NOT NULL,
    
    subject_name VARCHAR(100) NOT NULL,
    subject_code VARCHAR(20) NULL,
    subject_type ENUM('core', 'elective', 'optional', 'extra_curricular') DEFAULT 'core',
    description TEXT NULL,
    
    is_active BOOLEAN DEFAULT TRUE,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tenant_id (tenant_id),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);
```

---

## 🏗️ Models & Relationships

### 1. **Teacher Model**

```php
class Teacher extends Model
{
    use HasFactory, SoftDeletes, ForTenant;
    
    protected $fillable = [
        'tenant_id', 'user_id', 'employee_id',
        'first_name', 'middle_name', 'last_name',
        'gender', 'date_of_birth', 'blood_group',
        'nationality', 'religion', 'category',
        'email', 'phone', 'alternate_phone',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relation',
        'current_address', 'permanent_address',
        'department_id', 'designation', 'employment_type',
        'date_of_joining', 'date_of_leaving',
        'highest_qualification', 'experience_years',
        'salary_amount', 'bank_name', 'bank_account_number', 'bank_ifsc_code',
        'pan_number', 'aadhar_number',
        'photo', 'is_active', 'status', 'status_remarks', 'notes'
    ];
    
    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_joining' => 'date',
        'date_of_leaving' => 'date',
        'current_address' => 'array',
        'permanent_address' => 'array',
        'experience_years' => 'decimal:1',
        'salary_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];
    
    protected $appends = ['age', 'photo_url', 'years_of_service'];
    
    // Relationships
    public function tenant() { return $this->belongsTo(Tenant::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function department() { return $this->belongsTo(Department::class); }
    
    public function qualifications() { return $this->hasMany(TeacherQualification::class); }
    public function subjects() { return $this->belongsToMany(Subject::class, 'teacher_subjects'); }
    public function documents() { return $this->hasMany(TeacherDocument::class); }
    public function attendance() { return $this->hasMany(TeacherAttendance::class); }
    public function leaves() { return $this->hasMany(TeacherLeave::class); }
    
    // Class Teacher assignments
    public function classesTaught() {
        return $this->hasMany(Section::class, 'class_teacher_id');
    }
    
    // Accessors
    public function getAgeAttribute() {
        return $this->date_of_birth?->age;
    }
    
    public function getPhotoUrlAttribute() {
        return $this->photo ? Storage::url($this->photo) : null;
    }
    
    public function getYearsOfServiceAttribute() {
        $end = $this->date_of_leaving ?? now();
        return $this->date_of_joining->diffInYears($end);
    }
    
    // Scopes
    public function scopeActive($query) {
        return $query->where('is_active', true)->where('status', 'active');
    }
    
    // Helper Methods
    public static function generateEmployeeId($tenantId, $year = null) {
        $year = $year ?? now()->year;
        $count = static::where('tenant_id', $tenantId)
            ->where('employee_id', 'like', "TCH-{$year}-%")
            ->count();
        return sprintf('TCH-%d-%03d', $year, $count + 1);
    }
}
```

### 2. **TeacherQualification Model**

```php
class TeacherQualification extends Model
{
    use HasFactory, ForTenant;
    
    protected $fillable = [
        'tenant_id', 'teacher_id', 'qualification_type',
        'degree_name', 'specialization', 'institution_name', 'university_board',
        'year_of_passing', 'grade_percentage', 'certificate_number',
        'certificate_document', 'is_verified', 'verified_by', 'verified_at'
    ];
    
    protected $casts = [
        'year_of_passing' => 'integer',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
    ];
    
    public function teacher() { return $this->belongsTo(Teacher::class); }
}
```

### 3. **TeacherSubject Model**

```php
class TeacherSubject extends Model
{
    use HasFactory, ForTenant;
    
    protected $fillable = [
        'tenant_id', 'teacher_id', 'subject_id', 'class_id',
        'is_primary', 'years_teaching'
    ];
    
    protected $casts = [
        'is_primary' => 'boolean',
        'years_teaching' => 'decimal:1',
    ];
    
    public function teacher() { return $this->belongsTo(Teacher::class); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function schoolClass() { return $this->belongsTo(SchoolClass::class, 'class_id'); }
}
```

### 4. **Department Model**

```php
class Department extends Model
{
    use HasFactory, ForTenant;
    
    protected $fillable = [
        'tenant_id', 'department_name', 'department_code',
        'description', 'head_teacher_id', 'is_active'
    ];
    
    protected $casts = ['is_active' => 'boolean'];
    
    public function teachers() { return $this->hasMany(Teacher::class); }
    public function headTeacher() { return $this->belongsTo(Teacher::class, 'head_teacher_id'); }
}
```

### 5. **Subject Model**

```php
class Subject extends Model
{
    use HasFactory, ForTenant;
    
    protected $fillable = [
        'tenant_id', 'subject_name', 'subject_code',
        'subject_type', 'description', 'is_active'
    ];
    
    protected $casts = ['is_active' => 'boolean'];
    
    public function teachers() {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects');
    }
}
```

---

## 🎨 Views & Pages

### **1. Teachers Index** (`/admin/teachers`)

**Features:**
- List all teachers with grid/table view
- Advanced search (name, employee ID, email, phone)
- Filters:
  - Department
  - Designation
  - Employment Type
  - Status
  - Gender
- Quick stats cards (Total, Active, On Leave, Department-wise)
- Pagination
- Export to Excel/PDF
- Bulk actions (Activate/Deactivate)

**Layout:**
```
┌─────────────────────────────────────────────────────┐
│  Teachers Management                    [+ Add New] │
├─────────────────────────────────────────────────────┤
│  [📊 Total: 50] [✅ Active: 45] [🏖️ On Leave: 5]    │
├─────────────────────────────────────────────────────┤
│  🔍 Search: [_________]   Department: [All ▼]      │
│  Status: [All ▼]  Employment: [All ▼]  [🔍 Filter] │
├─────────────────────────────────────────────────────┤
│  ┌──────────────────────────────────────────────┐  │
│  │ 📷 Photo | Name | Employee ID | Department   │  │
│  │ Email | Phone | Designation | Status | ⚙️    │  │
│  ├──────────────────────────────────────────────┤  │
│  │ [Teacher cards/rows with all info]           │  │
│  └──────────────────────────────────────────────┘  │
│  Showing 1-20 of 50                    [1][2][3]   │
└─────────────────────────────────────────────────────┘
```

### **2. Teacher Profile** (`/admin/teachers/{id}`)

**Tabbed Interface:**
1. **Overview** - Personal & contact info
2. **Employment** - Job details, salary, bank info
3. **Qualifications** - Education & certifications
4. **Subjects** - Subject assignments
5. **Classes** - Class teacher assignments
6. **Attendance** - Monthly attendance view
7. **Leaves** - Leave history & balance
8. **Documents** - Uploaded files
9. **Actions** - Admin actions (status change, etc.)

**Layout:**
```
┌────────────────────────────────────────────────────┐
│  👨‍🏫 [Photo] John Doe          [✏️ Edit] [🗑️ Delete]│
│  TCH-2025-001 | Mathematics Teacher               │
│  📧 john@school.com | 📱 +91 9876543210           │
│  Status: ✅ Active | Department: Science          │
├────────────────────────────────────────────────────┤
│  [Overview] [Employment] [Qualifications]          │
│  [Subjects] [Classes] [Attendance] [Leaves]        │
│  [Documents] [Actions]                             │
├────────────────────────────────────────────────────┤
│  [Tab Content Here]                                │
└────────────────────────────────────────────────────┘
```

### **3. Add/Edit Teacher** (`/admin/teachers/create`, `/admin/teachers/{id}/edit`)

**Multi-step Form:**
1. **Personal Information**
2. **Contact Details**
3. **Employment Details**
4. **Qualifications** (can add multiple)
5. **Subject Assignments**
6. **Financial Details**
7. **Documents Upload**

**Or Single-page with Sections:**
```
┌────────────────────────────────────────────────────┐
│  Add New Teacher                                   │
├────────────────────────────────────────────────────┤
│  📝 Personal Information                           │
│  [First Name] [Middle] [Last Name]                │
│  [Gender] [DOB] [Blood Group]                     │
│  [Photo Upload]                                    │
├────────────────────────────────────────────────────┤
│  📞 Contact Information                            │
│  [Email] [Phone] [Alternate Phone]                │
│  [Current Address]                                 │
│  [Permanent Address] ☑️ Same as current           │
│  [Emergency Contact Details]                       │
├────────────────────────────────────────────────────┤
│  💼 Employment Details                             │
│  [Department] [Designation] [Employment Type]     │
│  [Date of Joining] [Salary]                       │
├────────────────────────────────────────────────────┤
│  [Cancel]                           [Save Teacher] │
└────────────────────────────────────────────────────┘
```

### **4. Attendance Management** (`/admin/teachers/attendance`)

**Features:**
- Calendar view or list view
- Mark attendance for all teachers
- Quick mark: Present All, Absent, Half Day
- Individual attendance marking
- Filter by date range, department
- Export attendance reports

**Layout:**
```
┌────────────────────────────────────────────────────┐
│  Teacher Attendance - October 2025                 │
│  Date: [📅 14 Oct 2025]    [⬅️ Prev] [Next ➡️]     │
├────────────────────────────────────────────────────┤
│  Quick Actions: [✅ Mark All Present]              │
│                 [❌ Mark All Absent]               │
├────────────────────────────────────────────────────┤
│  Teacher Name | Department | Status | In | Out |⚙️│
│  ──────────────────────────────────────────────    │
│  John Doe | Science | [●Present ▼] | 9:00 | 5:00 │
│  Jane Smith | Math | [●Present ▼] | 9:15 | 5:30  │
│  ...                                               │
├────────────────────────────────────────────────────┤
│  [Cancel]                      [Save Attendance]   │
└────────────────────────────────────────────────────┘
```

### **5. Leave Management** (`/admin/teachers/leaves`)

**Features:**
- Leave request list (Pending, Approved, Rejected)
- Apply leave on behalf of teacher
- Approve/Reject leaves
- Leave balance tracking
- Leave calendar view

---

## 🛣️ Routes

```php
// Teacher Management
Route::prefix('teachers')->name('teachers.')->group(function () {
    Route::get('/', [TeacherController::class, 'index'])->name('index');
    Route::get('/create', [TeacherController::class, 'create'])->name('create');
    Route::post('/', [TeacherController::class, 'store'])->name('store');
    Route::get('/{teacherId}', [TeacherController::class, 'show'])->name('show');
    Route::get('/{teacherId}/edit', [TeacherController::class, 'edit'])->name('edit');
    Route::put('/{teacherId}', [TeacherController::class, 'update'])->name('update');
    Route::delete('/{teacherId}', [TeacherController::class, 'destroy'])->name('destroy');
    
    // Subject Assignment
    Route::post('/{teacherId}/subjects', [TeacherController::class, 'assignSubjects'])->name('assign-subjects');
    Route::delete('/{teacherId}/subjects/{subjectId}', [TeacherController::class, 'removeSubject'])->name('remove-subject');
    
    // Qualifications
    Route::post('/{teacherId}/qualifications', [TeacherController::class, 'addQualification'])->name('add-qualification');
    Route::delete('/qualifications/{qualificationId}', [TeacherController::class, 'removeQualification'])->name('remove-qualification');
    
    // Documents
    Route::post('/{teacherId}/documents', [TeacherController::class, 'uploadDocument'])->name('upload-document');
    Route::delete('/documents/{documentId}', [TeacherController::class, 'deleteDocument'])->name('delete-document');
});

// Attendance
Route::prefix('teachers/attendance')->name('teachers.attendance.')->group(function () {
    Route::get('/', [TeacherAttendanceController::class, 'index'])->name('index');
    Route::get('/mark', [TeacherAttendanceController::class, 'mark'])->name('mark');
    Route::post('/save', [TeacherAttendanceController::class, 'save'])->name('save');
    Route::get('/report', [TeacherAttendanceController::class, 'report'])->name('report');
});

// Leave Management
Route::prefix('teachers/leaves')->name('teachers.leaves.')->group(function () {
    Route::get('/', [TeacherLeaveController::class, 'index'])->name('index');
    Route::post('/', [TeacherLeaveController::class, 'store'])->name('store');
    Route::put('/{leaveId}/approve', [TeacherLeaveController::class, 'approve'])->name('approve');
    Route::put('/{leaveId}/reject', [TeacherLeaveController::class, 'reject'])->name('reject');
    Route::delete('/{leaveId}', [TeacherLeaveController::class, 'destroy'])->name('destroy');
});

// Departments
Route::resource('departments', DepartmentController::class);

// Subjects
Route::resource('subjects', SubjectController::class);
```

---

## 📝 Controller Methods

### **TeacherController**

```php
public function index(Request $request)
- List all teachers with filters
- Search, pagination
- Department-wise stats

public function create()
- Show create form
- Load departments, subjects

public function store(Request $request)
- Validate input
- Generate employee_id
- Create teacher record
- Upload photo if provided
- Redirect with success

public function show($teacherId)
- Display teacher profile
- Load all relationships
- Tabs: Overview, Employment, etc.

public function edit($teacherId)
- Show edit form
- Pre-fill data

public function update(Request $request, $teacherId)
- Validate input
- Update teacher record
- Handle photo upload/change

public function destroy($teacherId)
- Soft delete teacher
- Check dependencies

public function assignSubjects(Request $request, $teacherId)
- Assign subjects to teacher
- Handle class-specific assignments

public function addQualification(Request $request, $teacherId)
- Add new qualification
- Upload certificate

public function uploadDocument(Request $request, $teacherId)
- Upload teacher document
- Store metadata
```

---

## ✅ Implementation Checklist

### Phase 1: Core Teacher Management (Week 1)
- [ ] Create migrations (teachers, departments, subjects)
- [ ] Create models with relationships
- [ ] Create TeacherController with CRUD
- [ ] Create DepartmentController with CRUD
- [ ] Create SubjectController with CRUD
- [ ] Create routes
- [ ] Build teacher index view
- [ ] Build teacher create/edit form
- [ ] Build teacher profile view (Overview tab)
- [ ] Implement employee_id auto-generation
- [ ] Add photo upload functionality
- [ ] Test basic CRUD operations

### Phase 2: Qualifications & Subjects (Week 2)
- [ ] Create teacher_qualifications migration
- [ ] Create teacher_subjects migration
- [ ] Create TeacherQualification model
- [ ] Create TeacherSubject model
- [ ] Add qualification management in profile
- [ ] Add subject assignment in profile
- [ ] Build Qualifications tab
- [ ] Build Subjects tab
- [ ] Implement certificate upload
- [ ] Test relationships

### Phase 3: Documents & Advanced Profile (Week 3)
- [ ] Create teacher_documents migration
- [ ] Create TeacherDocument model
- [ ] Build Documents tab
- [ ] Implement document upload
- [ ] Build Employment tab
- [ ] Build Classes tab (class teacher)
- [ ] Add financial details section
- [ ] Test document management

### Phase 4: Attendance System (Week 4)
- [ ] Create teacher_attendance migration
- [ ] Create TeacherAttendance model
- [ ] Create TeacherAttendanceController
- [ ] Build attendance marking interface
- [ ] Build attendance calendar view
- [ ] Build attendance report
- [ ] Implement bulk marking
- [ ] Test attendance tracking

### Phase 5: Leave Management (Week 5)
- [ ] Create teacher_leaves migration
- [ ] Create TeacherLeave model
- [ ] Create TeacherLeaveController
- [ ] Build leave application form
- [ ] Build leave approval interface
- [ ] Build leave history view
- [ ] Calculate leave balance
- [ ] Implement email notifications
- [ ] Test leave workflow

### Phase 6: UI/UX Polish & Testing (Week 6)
- [ ] Improve all views with modern design
- [ ] Add filters and search everywhere
- [ ] Implement tab state persistence
- [ ] Add loading states
- [ ] Add validation messages
- [ ] Add success/error notifications
- [ ] Add export functionality
- [ ] Create comprehensive documentation
- [ ] Write feature tests
- [ ] Write unit tests
- [ ] Performance optimization

---

## 🎨 Design Principles

1. **Consistent with Student Management**
   - Same modern UI/UX patterns
   - Same color schemes and layouts
   - Tab-based profile pages
   - Gradient headers

2. **Mobile Responsive**
   - Works on tablets and phones
   - Touch-friendly interfaces
   - Collapsible sections

3. **Performance**
   - Eager loading relationships
   - Pagination for large datasets
   - Caching where appropriate

4. **User Experience**
   - Clear navigation
   - Helpful tooltips
   - Error prevention
   - Quick actions

---

## 📊 Expected Deliverables

1. **Database**
   - 8 new tables with proper relationships
   - Seeders for sample data

2. **Models**
   - 6 new models with full relationships
   - Accessors, scopes, helper methods

3. **Controllers**
   - TeacherController (11+ methods)
   - TeacherAttendanceController (5+ methods)
   - TeacherLeaveController (5+ methods)
   - DepartmentController (CRUD)
   - SubjectController (CRUD)

4. **Views**
   - teachers/index.blade.php
   - teachers/create.blade.php
   - teachers/edit.blade.php
   - teachers/show.blade.php (with 9 tabs)
   - attendance/index.blade.php
   - attendance/mark.blade.php
   - leaves/index.blade.php
   - departments (CRUD views)
   - subjects (CRUD views)

5. **Documentation**
   - TEACHER_MANAGEMENT_GUIDE.md
   - API documentation
   - Update CURRENT_FEATURES.md

---

## 🚀 Success Metrics

- ✅ All CRUD operations working
- ✅ Proper tenant isolation
- ✅ Photo upload working
- ✅ Subject assignment working
- ✅ Attendance tracking accurate
- ✅ Leave approval workflow functional
- ✅ Mobile responsive
- ✅ No linting errors
- ✅ Comprehensive documentation
- ✅ Test coverage > 80%

---

**Estimated Time: 6 weeks**  
**Priority: High (Next after Student Management)**  
**Status: Ready to Start**

