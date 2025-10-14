# School ERP - Quick Reference Guide

## 📋 How to Promote Students & Change Status

### **Method 1: Promote Student (Recommended for Year-End)**
**Use When:** Moving students to the next class at end of academic year

**Steps:**
1. Go to: `Students` → Click on student name → `Actions` tab
2. Find "Promote Student" section (blue header)
3. Fill in:
   - **Promote To Class**: Select next class (e.g., Class 6)
   - **Section**: Choose section (optional)
   - **Academic Year**: Auto-filled (e.g., 2025-2026)
   - **Roll Number**: New roll number (optional)
   - **Percentage**: Student's marks (e.g., 85.5)
   - **Grade**: Grade achieved (e.g., A+)
   - **Remarks**: Any notes
4. Click "Promote Student"

**What Happens:**
- ✅ Old class enrollment marked as "Promoted"
- ✅ New enrollment created for next class
- ✅ Academic history preserved
- ✅ Student's performance recorded

---

### **Method 2: Update Academic Status**
**Use When:** Student is leaving, graduated, or changing overall status

**Steps:**
1. Go to: `Students` → Click on student name → `Actions` tab
2. Find "Update Academic Status" section (purple header)
3. Fill in:
   - **Overall Status**: Choose one:
     - `Active` - Currently studying
     - `Alumni` - Graduated
     - `Transferred` - Moved to another school
     - `Dropped Out` - Left school
   - **Active Status**: Active or Inactive
   - **Status Remarks**: Reason for change
4. Click "Update Status"

**What Happens:**
- ✅ Student status updated
- ✅ Current enrollment ended (if marking inactive)
- ✅ Status change logged with timestamp

---

### **Method 3: Complete Current Enrollment**
**Use When:** Ending current class without promoting (e.g., student failed, transferred)

**Steps:**
1. Go to: `Students` → Click on student name → `Actions` tab
2. Find "Complete Current Enrollment" section (green header)
3. Fill in:
   - **Result**: Passed, Failed, Transferred, or Dropped
   - **Percentage**: Final marks
   - **Grade**: Final grade
   - **Remarks**: Additional notes
4. Click "Complete Enrollment"

**What Happens:**
- ✅ Current enrollment marked as completed
- ✅ No new enrollment created
- ✅ Student ready for next action

---

## 🎯 Common Scenarios

### **Scenario 1: Year-End Mass Promotion**
**Goal:** Promote all Class 5 students to Class 6

**Quick Steps:**
1. Filter students by Class 5
2. For each student:
   - Open student profile → Actions tab
   - Use "Promote Student"
   - Select Class 6, enter marks/grade
   - Submit

**Future Feature:** Bulk promotion (coming soon)

---

### **Scenario 2: Student Graduated**
**Goal:** Mark Class 12 student as Alumni

**Quick Steps:**
1. Open student profile → Actions tab
2. Use "Update Academic Status"
3. Set:
   - Overall Status: `Alumni`
   - Active Status: `Inactive`
   - Remarks: "Graduated - Batch 2025"
4. Submit

---

### **Scenario 3: Student Transferred Mid-Year**
**Goal:** Mark student as transferred

**Quick Steps:**
1. Open student profile → Actions tab
2. First, use "Complete Current Enrollment":
   - Result: `Transferred`
   - Add remarks with transfer date
3. Then use "Update Academic Status":
   - Overall Status: `Transferred`
   - Active Status: `Inactive`
   - Add remarks with new school name

---

### **Scenario 4: Student Failed & Repeating Class**
**Goal:** Mark student as failed, keep in same class

**Quick Steps:**
1. Open student profile → Actions tab
2. Use "Complete Current Enrollment":
   - Result: `Failed`
   - Percentage: Enter marks
   - Remarks: "Repeating Class 5"
3. Then manually create new enrollment for same class (Edit Student)

---

## 📊 Where to Find Academic History

**View Complete History:**
1. Go to student profile
2. Click "Academic History" tab
3. See all enrollments with:
   - ✅ Class & Section
   - ✅ Academic Year
   - ✅ Duration (start to end dates)
   - ✅ Result & Percentage
   - ✅ Current vs Completed status

---

## 🔍 Quick Filters

**Find Students by Status:**
- Navigate to: `Students` → Use filter dropdown
- Options:
  - All Status
  - Active
  - Alumni
  - Transferred
  - Dropped Out

**Find Students by Class:**
- Navigate to: `Students` → Class filter
- Select specific class to view all students

---

## ⚠️ Important Notes

1. **Promotion is Permanent**: Once promoted, previous enrollment is completed (cannot undo)
2. **Status Changes**: Marking as Alumni/Transferred/Dropped Out will end current enrollment
3. **Academic Year Format**: Use YYYY-YYYY format (e.g., 2024-2025)
4. **Remarks are Important**: Always add remarks for audit trail
5. **Check Before Promotion**: Review "Academic History" tab first

---

## 🆘 Troubleshooting

**Problem:** "No active enrollment found"
- **Solution:** Student must be enrolled in a class first (Edit Student → set current class)

**Problem:** Cannot promote student
- **Solution:** Ensure target class exists (check Classes Management)

**Problem:** Academic history not showing dates
- **Solution:** This is normal for old enrollments; new ones will have dates

**Problem:** Student shows in wrong class after promotion
- **Solution:** Refresh page; check Academic History tab to verify

---

## 📱 Quick Access URLs

- **Student List**: `http://[tenant].myschool.test/admin/students`
- **Classes**: `http://[tenant].myschool.test/admin/classes`
- **Sections**: `http://[tenant].myschool.test/admin/sections`
- **Settings**: `http://[tenant].myschool.test/admin/settings`

---

## 📚 Additional Resources

- **Full Documentation**: See `STUDENT_PROMOTION_GUIDE.md`
- **Testing Guide**: See `TESTING_GUIDE.md`
- **Student Management Plan**: See `STUDENT_MANAGEMENT_PLAN.md`

---

## 🎓 Best Practices

1. ✅ **Record all data** during promotion (percentage, grade, remarks)
2. ✅ **Add meaningful remarks** for future reference
3. ✅ **Check academic history** before making changes
4. ✅ **Use consistent naming** for academic years
5. ✅ **Test with one student** before bulk operations
6. ✅ **Keep parents informed** about status changes

---

**Last Updated:** October 14, 2025  
**Version:** 1.0

