# 📚 School ERP - User Guide

## Quick Navigation
- [Student Management](#student-management)
- [Class & Section Management](#class--section-management)
- [Settings Configuration](#settings-configuration)
- [Common Tasks](#common-tasks)
- [Troubleshooting](#troubleshooting)

---

## 🎓 Student Management

### Adding a Student
1. Navigate to **Students** → **Add New**
2. Fill in personal details (name, DOB, gender, etc.)
3. Add contact information and parent details
4. Upload photo (optional)
5. Select current class and section
6. Click **Save Student**

### Viewing Student Profile
- Go to **Students** → Click on student name
- Tabs available:
  - **Overview** - Personal & contact info
  - **Academic History** - All enrollments with duration
  - **Documents** - Uploaded files
  - **Actions** - Promote, change status, complete enrollment

### Promoting a Student
1. Open student profile → **Actions** tab
2. Use **Promote Student** form:
   - Select next class
   - Choose section
   - Enter academic year (e.g., 2025-2026)
   - Add percentage and grade
   - Add remarks
3. Click **Promote Student**

**Result:** Previous enrollment completed, new enrollment created

### Changing Student Status
1. Open student profile → **Actions** tab
2. Use **Update Academic Status** form:
   - Select status: Active, Alumni, Transferred, Dropped Out
   - Set active/inactive
   - Add remarks
3. Click **Update Status**

**Note:** Changing to Alumni/Transferred/Dropped Out ends current enrollment

### Searching & Filtering Students
- **Search:** By name, admission number, or roll number
- **Filters:**
  - Class
  - Section
  - Status (Active, Alumni, etc.)
  - Academic Year

---

## 📚 Class & Section Management

### Creating a Class
1. Navigate to **Classes** → **Add New**
2. Enter class name (e.g., "Class 5")
3. Add description
4. Set capacity
5. Click **Save Class**

### Creating a Section
1. Navigate to **Sections** → **Add New**
2. Select class
3. Enter section name (e.g., "A", "B")
4. Set room number and capacity
5. Assign class teacher (optional)
6. Click **Save Section**

---

## ⚙️ Settings Configuration

### General Settings
1. Go to **Settings** → **General** tab
2. Update:
   - Institution name
   - Logo (upload/delete)
   - Platform type (School/College/Both)
   - Contact information (email, phone, address)
3. Click **Save General Settings**

### Managing Modules
1. Go to **Settings** → **Features** tab
2. Enable/disable modules as needed:
   - Students, Teachers, Classes
   - Attendance, Exams, Grades
   - Fees, Library, Transport
   - And more...
3. Click **Save Feature Settings**

### Academic Configuration
1. Go to **Settings** → **Academic** tab
2. Set:
   - Academic year start/end dates
   - Default session/term
   - Week start day
3. Click **Save Academic Settings**

---

## 🎯 Common Tasks

### End of Year Mass Promotion
**Scenario:** Promote all Class 5 students to Class 6

**Steps:**
1. Go to **Students**
2. Filter by Class 5
3. For each student:
   - Open profile → **Actions** tab
   - Use **Promote Student**
   - Select Class 6
   - Enter marks/grade
   - Submit

**Future:** Bulk promotion feature coming soon

### Marking a Student as Graduated
1. Open student profile → **Actions** tab
2. Use **Update Academic Status**
3. Set:
   - Overall Status: **Alumni**
   - Active Status: **Inactive**
   - Remarks: "Graduated - Batch 2025"
4. Submit

### Handling Mid-Year Transfer
1. Open student profile → **Actions** tab
2. First, **Complete Current Enrollment**:
   - Result: **Transferred**
   - Add transfer date in remarks
3. Then, **Update Academic Status**:
   - Overall Status: **Transferred**
   - Active Status: **Inactive**
   - Add new school name in remarks

### Student Failed & Repeating Class
1. Open student profile → **Actions** tab
2. **Complete Current Enrollment**:
   - Result: **Failed**
   - Enter marks
   - Remarks: "Repeating Class 5"
3. Edit student to re-enroll in same class

---

## 🔍 Quick Access URLs

Replace `[tenant]` with your school subdomain:

- **Dashboard:** `http://[tenant].myschool.test/admin/dashboard`
- **Students:** `http://[tenant].myschool.test/admin/students`
- **Classes:** `http://[tenant].myschool.test/admin/classes`
- **Sections:** `http://[tenant].myschool.test/admin/sections`
- **Settings:** `http://[tenant].myschool.test/admin/settings`

**Super Admin:**
- **Admin Panel:** `http://app.myschool.test/admin/dashboard`
- **Tenants:** `http://app.myschool.test/admin/tenants`

---

## 🆘 Troubleshooting

### "No active enrollment found"
**Problem:** Cannot promote student  
**Solution:** Student must have a current enrollment. Edit student and assign to a class first.

### Cannot see promoted class
**Problem:** Student shows old class after promotion  
**Solution:** Refresh the page. Check **Academic History** tab to verify promotion.

### Academic history missing dates
**Problem:** Old enrollments show "Not specified"  
**Solution:** This is normal for legacy data. New enrollments will have proper dates.

### Session lost between domains
**Problem:** Login on one subdomain logs out another  
**Solution:** This is by design. Each tenant has isolated sessions for security.

### Cannot upload documents
**Problem:** Document upload fails  
**Solution:** Check file size (max 10MB) and format. Ensure storage permissions are correct.

---

## 💡 Best Practices

1. ✅ **Always add remarks** when changing student status
2. ✅ **Record performance data** during promotions
3. ✅ **Check academic history** before making changes
4. ✅ **Use consistent academic year format** (YYYY-YYYY)
5. ✅ **Test with one student** before bulk operations
6. ✅ **Keep parents informed** about status changes
7. ✅ **Backup data regularly** (via super admin panel)
8. ✅ **Review filters** before exporting reports

---

## 📖 Additional Resources

- **Feature List:** See `CURRENT_FEATURES.md`
- **Teacher Management:** See `TEACHER_MANAGEMENT_PLAN.md`
- **Requirements:** See `requirements_document.md`
- **Technical Docs:** See `src/README.md`

---

**Last Updated:** October 14, 2025  
**Version:** 1.0  
**For Support:** Contact your system administrator

