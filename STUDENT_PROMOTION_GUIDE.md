# Student Promotion & Academic Status Management Guide

## Overview
This guide explains how to promote students to the next class and manage their academic status in the School ERP system.

## Features

### 1. **Promote Student**
Promote a student from their current class to the next class for a new academic year.

**What happens when you promote:**
- Current enrollment is marked as completed with result "promoted"
- Student's previous class performance (percentage, grade) is recorded
- A new enrollment is created for the next class
- Academic year is updated
- Student can be assigned a new roll number and section

**Steps:**
1. Go to Students → Select a student → Click on "Actions" tab
2. Fill in the "Promote Student" form:
   - **Promote To Class** (required): Select the next class
   - **Section** (optional): Select the section
   - **Academic Year** (required): Defaults to current year (e.g., 2025-2026)
   - **New Roll Number** (optional): Assign new roll number
   - **Percentage** (optional): Student's performance percentage
   - **Grade** (optional): Grade achieved (A+, A, B, etc.)
   - **Remarks** (optional): Any additional notes
3. Click "Promote Student"

### 2. **Update Academic Status**
Change the overall status of a student (Active, Alumni, Transferred, Dropped Out).

**Available Statuses:**
- **Active**: Currently enrolled and attending
- **Alumni**: Graduated from the institution
- **Transferred**: Moved to another institution
- **Dropped Out**: Left without completing

**Important Notes:**
- Marking a student as "Inactive" or changing status to Alumni/Transferred/Dropped Out will automatically end their current enrollment
- Status changes are logged with timestamps

**Steps:**
1. Go to Students → Select a student → Click on "Actions" tab
2. Fill in the "Update Academic Status" form:
   - **Overall Status** (required): Select new status
   - **Active Status** (required): Active or Inactive
   - **Status Remarks** (optional): Explain the reason for change
3. Click "Update Status"

### 3. **Complete Current Enrollment**
Mark the current enrollment as completed with a result (without promoting to next class).

**Use Cases:**
- Student passed/failed the current class
- Student is leaving the institution
- Academic year is ending

**Available Results:**
- **Passed**: Successfully completed the class
- **Failed**: Did not meet requirements
- **Transferred**: Leaving for another institution
- **Dropped**: Left without completing

**Steps:**
1. Go to Students → Select a student → Click on "Actions" tab
2. Fill in the "Complete Current Enrollment" form:
   - **Result** (required): Select outcome
   - **Percentage** (optional): Final percentage
   - **Grade** (optional): Final grade
   - **Remarks** (optional): Additional notes
3. Click "Complete Enrollment"

## Technical Details

### Controller Methods

**StudentController Methods:**

```php
// Promote student to next class
public function promote(Request $request, $studentId)

// Update overall academic status
public function updateAcademicStatus(Request $request, $studentId)

// Complete current enrollment without promotion
public function completeEnrollment(Request $request, $studentId)
```

### Routes

```php
POST /admin/students/{studentId}/promote
POST /admin/students/{studentId}/update-status
POST /admin/students/{studentId}/complete-enrollment
```

### Model Methods

**Student Model:**

```php
// Promote to next class
$student->promoteToClass($toClassId, $sectionId, $academicYear, $percentage, $grade, $remarks, $rollNumber);

// Enroll in a class
$student->enrollInClass($classId, $sectionId, $academicYear, $rollNumber);
```

**ClassEnrollment Model:**

```php
// Mark enrollment as completed
$enrollment->markAsCompleted($result, $percentage, $grade, $remarks, $promotedToClassId);
```

## Workflow Examples

### Example 1: End of Year Promotion
```
1. Student is in Class 5, Section A
2. Academic year 2024-2025 is ending
3. Navigate to student profile → Actions tab
4. Use "Promote Student" form:
   - Promote To Class: Class 6
   - Section: Section B
   - Academic Year: 2025-2026
   - Percentage: 85.5
   - Grade: A
   - Remarks: "Excellent performance"
5. Result:
   - Old enrollment (Class 5) marked as completed with "promoted" status
   - New enrollment created for Class 6, Section B
   - Academic history updated
```

### Example 2: Student Transfer
```
1. Student is currently enrolled
2. Student is transferring to another school
3. Navigate to student profile → Actions tab
4. Use "Update Academic Status" form:
   - Overall Status: Transferred
   - Active Status: Inactive
   - Status Remarks: "Transferred to XYZ School on 15 Oct 2025"
5. Result:
   - Student marked as "Transferred"
   - Current enrollment automatically ended
   - Student appears as inactive in system
```

### Example 3: Student Graduation
```
1. Student completed final class (e.g., Class 12)
2. Navigate to student profile → Actions tab
3. Use "Update Academic Status" form:
   - Overall Status: Alumni
   - Active Status: Inactive
   - Status Remarks: "Graduated - Batch 2025"
4. Result:
   - Student marked as "Alumni"
   - Student moves to alumni records
```

## Validation Rules

### Promote Student
- `to_class_id`: Required, must exist in classes table
- `to_section_id`: Optional, must exist if provided
- `academic_year`: Required, max 20 characters
- `roll_number`: Optional, max 50 characters
- `percentage`: Optional, 0-100
- `grade`: Optional, max 10 characters
- `remarks`: Optional, max 500 characters

### Update Academic Status
- `overall_status`: Required, one of: active, alumni, transferred, dropped_out
- `status_remarks`: Optional, max 500 characters
- `is_active`: Required, boolean

### Complete Enrollment
- `result`: Required, one of: passed, failed, transferred, dropped
- `percentage`: Optional, 0-100
- `grade`: Optional, max 10 characters
- `remarks`: Optional, max 500 characters

## Academic History Tracking

All student academic actions are tracked in the `class_enrollments` table:

- **enrollment_date**: When student joined the class
- **start_date**: Academic year start
- **end_date**: When enrollment ended (null if current)
- **is_current**: Only one enrollment can be current
- **enrollment_status**: Current status (enrolled, promoted, passed, etc.)
- **result**: Final result (filled at completion)
- **percentage**: Final percentage
- **grade**: Final grade
- **remarks**: Additional notes

## Best Practices

1. **Always add remarks** when changing status for audit trail
2. **Record percentages and grades** during promotion for complete academic records
3. **Use "Complete Enrollment"** before manually changing to Alumni/Transferred
4. **Verify enrollment history** in "Academic History" tab before promotion
5. **Keep academic years consistent** using YYYY-YYYY format (e.g., 2024-2025)
6. **Assign roll numbers** during promotion to maintain organization

## Troubleshooting

**Issue**: "No active enrollment found for this student"
- **Solution**: Student must have a current enrollment to promote or complete

**Issue**: Promotion button doesn't appear
- **Solution**: Ensure you're on the "Actions" tab in student profile

**Issue**: Cannot select class/section
- **Solution**: Ensure classes and sections are created in Class Management

**Issue**: Academic history not showing dates
- **Solution**: Check that start_date is set in enrollment records

## Future Enhancements

- Bulk promotion for entire class/section
- Automated promotion based on results
- Promotion approval workflow
- PDF certificates for promoted students
- Email notifications to parents
- Promotion reports and analytics

