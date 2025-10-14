# 🧪 Testing Guide - Student Management System

## ✅ Setup Complete!

### **Database Created:**
- ✅ 12 Classes (Class 1 to Class 12)
- ✅ 36 Sections (A, B, C for each class)
- ✅ 5 Sample Students (in Class 1 Section A)

---

## 🎯 Test Checklist

### **1. View Students List**
**URL:** `https://svps.myschool.test/admin/students`

**Expected:**
- ✅ Should show 5 sample students
- ✅ Table with student names, photos (placeholder), admission numbers
- ✅ Class & Section displayed (Class 1 - A)
- ✅ Status badges (New Admission)
- ✅ Contact information
- ✅ Action buttons (View, Edit, Delete)

**Test:**
- [ ] Click on student names
- [ ] Try search box (search by name)
- [ ] Use filters (class, section, status)
- [ ] Check pagination works
- [ ] Click "Add Student" button

---

### **2. Add New Student**
**URL:** `https://svps.myschool.test/admin/students/create`

**Expected:**
- ✅ Form with all sections:
  * Admission Info (auto-generated number)
  * Personal Information
  * Contact Information
  * Parent/Guardian Details
  * Academic Information

**Test:**
- [ ] Fill required fields (marked with *)
- [ ] Upload a photo (optional)
- [ ] Select Class 2 or Class 3
- [ ] Select a section
- [ ] Add father's details
- [ ] Add mother's details
- [ ] Click "Add Student"
- [ ] Should redirect to student profile
- [ ] Should show success message

**Sample Data:**
```
First Name: Ananya
Last Name: Reddy
DOB: 2018-05-15
Gender: Female
Category: General
Class: Class 2
Section: A
Roll Number: 1
Father Name: Mr. Reddy
Father Phone: 9876543210
```

---

### **3. View Student Profile**
**URL:** Click "View" on any student

**Expected:**
- ✅ Student header with photo/initials
- ✅ Admission number & class displayed
- ✅ Status banner with color
- ✅ Quick stats (Age, Roll No, Academic Year, Documents)
- ✅ 3 Tabs:
  * Overview (personal, contact, parent details)
  * Academic History (classes attended)
  * Documents (upload ready)
- ✅ Edit and Back buttons

**Test:**
- [ ] Click all 3 tabs
- [ ] Verify all information displays correctly
- [ ] Check academic history shows Class 1
- [ ] Click "Edit" button

---

### **4. Edit Student**
**URL:** Click "Edit" on any student

**Expected:**
- ✅ Form pre-filled with student data
- ✅ Can update name, DOB, gender
- ✅ Can change class/section
- ✅ Can upload new photo
- ✅ Current photo shown if exists

**Test:**
- [ ] Change class from Class 1 to Class 2
- [ ] Change section from A to B
- [ ] Update roll number
- [ ] Click "Update Student"
- [ ] Should show success message
- [ ] Verify changes saved

---

### **5. Search & Filter**
**URL:** `https://svps.myschool.test/admin/students`

**Test Search:**
- [ ] Search by first name: "Rahul"
- [ ] Search by admission number: "STU-2025-001"
- [ ] Search by partial name: "Sha"

**Test Filters:**
- [ ] Filter by Class: "Class 1"
- [ ] Filter by Section: "Class 1 - A"
- [ ] Filter by Status: "New Admission"
- [ ] Combine filters (Class 1 + Status Active)
- [ ] Click "Clear Filters"

---

### **6. Delete Student**
**Expected:**
- [ ] Click "Delete" button
- [ ] Confirmation dialog appears
- [ ] Click "OK"
- [ ] Student removed from list (soft delete)
- [ ] Success message shown

⚠️ **Note:** Student is soft-deleted, not permanently removed!

---

## 🔍 What to Check

### **UI/UX:**
- [ ] All pages load without errors
- [ ] Forms are responsive (mobile & desktop)
- [ ] Buttons and links work correctly
- [ ] Colors match tenant theme
- [ ] Status badges show correct colors
- [ ] Navigation sidebar highlights "Students"
- [ ] No broken images or icons

### **Functionality:**
- [ ] Admission number auto-generates (STU-2025-XXX)
- [ ] Full name auto-generates from first + middle + last
- [ ] Photo upload works (or shows placeholder)
- [ ] Class and section dropdowns populate
- [ ] Search filters results correctly
- [ ] Pagination shows correct counts
- [ ] Edit updates data correctly
- [ ] Delete removes student (soft delete)

### **Data Integrity:**
- [ ] Admission numbers are unique
- [ ] Can't create duplicate emails
- [ ] Academic history created on new student
- [ ] Tenant isolation works (no cross-tenant data)
- [ ] Required fields enforced
- [ ] Date validations work (DOB must be in past)

---

## 🐛 Common Issues & Solutions

### **Issue: "Class not found"**
**Solution:** Make sure classes were created via Tinker

### **Issue: "Section dropdown empty"**
**Solution:** Sections are created, check database

### **Issue: "Admission number already exists"**
**Solution:** Each tenant gets unique sequence

### **Issue: "Photo not uploading"**
**Solution:** Check storage/app/public is linked:
```bash
php artisan storage:link
```

### **Issue: "Route not found"**
**Solution:** Clear route cache:
```bash
php artisan optimize:clear
```

---

## 📝 Test Data Created

### **Classes (12):**
- Class 1, Class 2, Class 3... Class 12
- All active, numeric ordering

### **Sections (36):**
- Each class has sections A, B, C
- Capacity: 50 students each

### **Students (5):**
1. Rahul Sharma (Roll 1)
2. Priya Verma (Roll 2)
3. Amit Kumar (Roll 3)
4. Sneha Patel (Roll 4)
5. Rohan Singh (Roll 5)

All in: **Class 1 - Section A**
Status: **New Admission**
Year: **2024-2025**

---

## 🚀 Next Steps After Testing

If everything works:
1. ✅ Test all CRUD operations
2. ✅ Verify search and filters
3. ✅ Check UI/UX on mobile
4. ✅ Report any bugs or issues
5. ✅ Once verified, commit the code

If you want more features:
- Add bulk import from Excel
- Add ID card generation
- Add document upload UI
- Add class/section management UI
- Add reports

---

## 🎉 START TESTING!

**Login URL:** `https://svps.myschool.test/login`
**Username:** Your school admin email
**Then navigate to:** Students → View the list!

Let me know how it goes! 🚀

