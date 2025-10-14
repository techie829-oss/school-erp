# 💰 Fee Management System - Implementation Plan

**Date:** October 14, 2025  
**Priority:** HIGH (Critical for school revenue)  
**Estimated Time:** 2-3 weeks

---

## 🎯 Core Objectives

1. **Fee Structure** - Define fee plans by class/term
2. **Fee Components** - Tuition, Transport, Library, etc.
3. **Student Fee Cards** - Personalized fee obligations per student
4. **Invoice Generation** - Auto-generate invoices
5. **Payment Collection** - Online & offline payments
6. **Receipts** - Generate payment receipts
7. **Discounts & Scholarships** - Apply discounts
8. **Installments** - Support partial payments
9. **Outstanding Reports** - Track pending fees
10. **Payment Reminders** - Automated reminders

---

## 📊 Database Schema (From Requirements)

### 1. **fee_components** Table
```sql
- id
- tenant_id
- code (e.g., TUI, TRANS, LIB)
- name (e.g., Tuition Fee, Transport Fee)
- type ENUM('recurring', 'one_time')
- description
- is_active
- created_at, updated_at
```

### 2. **fee_plans** Table
```sql
- id
- tenant_id
- academic_year_id
- class_id
- term (Annual, Semester, Quarterly, Monthly)
- effective_from, effective_to
- name
- description
- is_active
- created_at, updated_at
```

### 3. **fee_plan_items** Table
```sql
- id
- fee_plan_id
- fee_component_id
- amount
- is_mandatory
- due_date
- created_at, updated_at
```

### 4. **student_fee_cards** Table
```sql
- id
- tenant_id
- student_id
- fee_plan_id
- academic_year_id
- ledger_balance (total outstanding)
- status ENUM('paid', 'partial', 'unpaid', 'overdue')
- created_at, updated_at
```

### 5. **student_fee_items** Table
```sql
- id
- student_fee_card_id
- fee_component_id
- original_amount
- discount_amount
- discount_reason
- net_amount
- due_date
- paid_amount
- status ENUM('paid', 'partial', 'unpaid', 'waived')
- created_at, updated_at
```

### 6. **invoices** Table
```sql
- id
- tenant_id
- student_id
- invoice_number
- academic_year_id
- invoice_date
- due_date
- total_amount
- paid_amount
- discount_amount
- net_amount
- status ENUM('draft', 'sent', 'paid', 'partial', 'overdue', 'cancelled')
- notes
- created_at, updated_at
```

### 7. **invoice_items** Table
```sql
- id
- invoice_id
- fee_component_id
- description
- quantity
- unit_price
- discount
- amount
- created_at, updated_at
```

### 8. **payments** Table
```sql
- id
- tenant_id
- student_id
- invoice_id
- payment_number
- payment_date
- amount
- payment_method ENUM('cash', 'cheque', 'card', 'upi', 'net_banking', 'online')
- transaction_id
- reference_number
- status ENUM('pending', 'success', 'failed', 'refunded')
- gateway_response JSON
- notes
- collected_by (user_id)
- created_at, updated_at
```

### 9. **refunds** Table
```sql
- id
- payment_id
- amount
- reason
- refund_date
- status ENUM('pending', 'processed', 'failed')
- processed_by
- created_at, updated_at
```

---

## 🏗️ Implementation Phases

### Phase 1: Fee Structure (Week 1)
**3-4 days**

✅ **Fee Components Management**
- CRUD for fee components
- List all components (grid/table)
- Active/inactive status
- Component types (recurring/one-time)

✅ **Fee Plans Management**
- CRUD for fee plans
- Assign to class & academic year
- Add fee components to plan
- Set amounts and due dates
- Term selection (Annual, Semester, etc.)

**Files to Create:**
- Migrations: `fee_components`, `fee_plans`, `fee_plan_items`
- Models: `FeeComponent`, `FeePlan`, `FeePlanItem`
- Controllers: `FeeComponentController`, `FeePlanController`
- Views: 6-8 pages (list, create, edit for each)
- Routes: 12-16 routes

---

### Phase 2: Student Fee Cards (Week 2)
**4-5 days**

✅ **Fee Card Generation**
- Auto-generate fee cards when student enrolls
- Assign fee plan based on class
- Calculate total fees
- Apply discounts/scholarships
- Track ledger balance

✅ **Fee Card Management**
- View student fee card
- Edit fee amounts (with authorization)
- Apply additional discounts
- Waive specific fees
- Update due dates

**Files to Create:**
- Migrations: `student_fee_cards`, `student_fee_items`
- Models: `StudentFeeCard`, `StudentFeeItem`
- Controller: `StudentFeeCardController`
- Views: 3-4 pages
- Routes: 6-8 routes

---

### Phase 3: Invoices & Payments (Week 2-3)
**5-6 days**

✅ **Invoice Management**
- Auto-generate invoices from fee cards
- Manual invoice creation
- Invoice numbering (INV-YYYY-XXXX)
- Send invoices (email/print)
- Invoice status tracking

✅ **Payment Collection**
- Record offline payments (cash, cheque, card)
- Payment receipt generation
- Partial payment support
- Payment allocation to invoices
- Payment history

✅ **Online Payments** (Optional - can add later)
- Razorpay/Stripe integration
- Payment gateway callbacks
- Auto-update on payment success
- Failed payment handling

**Files to Create:**
- Migrations: `invoices`, `invoice_items`, `payments`, `refunds`
- Models: `Invoice`, `InvoiceItem`, `Payment`, `Refund`
- Controllers: `InvoiceController`, `PaymentController`
- Views: 8-10 pages
- Routes: 15-20 routes

---

### Phase 4: Reports & Analytics (Week 3)
**2-3 days**

✅ **Fee Reports**
- Collection report (daily/monthly)
- Outstanding report (pending fees)
- Class-wise collection
- Payment method wise
- Defaulter list

✅ **Export Functionality**
- Excel export (fee cards, invoices, payments)
- PDF receipts
- Monthly collection summary

**Files to Create:**
- Report views: 5-6 pages
- Export methods in controllers
- PDF templates for receipts

---

## 🎨 Key Features

### Fee Structure Management:
1. Create fee components (Tuition, Transport, Library, etc.)
2. Create fee plans for each class
3. Set amounts and due dates
4. Term-based fees (Annual, Semester, Quarterly, Monthly)

### Student Fee Management:
1. Auto-generate fee cards on enrollment
2. View student's complete fee obligation
3. Apply discounts/scholarships
4. Track payment status
5. Installment support

### Payment Processing:
1. Collect payments (cash, online, cheque, card)
2. Generate receipts
3. Update fee card balance
4. Handle partial payments
5. Refund processing

### Reports & Insights:
1. Daily/monthly collection
2. Outstanding fees
3. Defaulter lists
4. Payment history
5. Class-wise analysis

---

## 🛣️ Routes Structure

```php
// Fee Components
GET    /admin/fees/components
GET    /admin/fees/components/create
POST   /admin/fees/components
GET    /admin/fees/components/{id}/edit
PUT    /admin/fees/components/{id}
DELETE /admin/fees/components/{id}

// Fee Plans
GET    /admin/fees/plans
GET    /admin/fees/plans/create
POST   /admin/fees/plans
GET    /admin/fees/plans/{id}
GET    /admin/fees/plans/{id}/edit
PUT    /admin/fees/plans/{id}
DELETE /admin/fees/plans/{id}

// Student Fee Cards
GET    /admin/fees/students
GET    /admin/fees/students/{id}/card
POST   /admin/fees/students/{id}/discount
POST   /admin/fees/students/{id}/waive

// Invoices
GET    /admin/fees/invoices
POST   /admin/fees/invoices/generate
GET    /admin/fees/invoices/{id}
PUT    /admin/fees/invoices/{id}
DELETE /admin/fees/invoices/{id}

// Payments
GET    /admin/fees/payments
POST   /admin/fees/payments
GET    /admin/fees/payments/{id}/receipt
POST   /admin/fees/payments/{id}/refund

// Reports
GET    /admin/fees/reports/collection
GET    /admin/fees/reports/outstanding
GET    /admin/fees/reports/defaulters
GET    /admin/fees/reports/export
```

---

## 📋 Implementation Checklist

### Database & Models
- [ ] Create fee_components migration
- [ ] Create fee_plans migration
- [ ] Create fee_plan_items migration
- [ ] Create student_fee_cards migration
- [ ] Create student_fee_items migration
- [ ] Create invoices migration
- [ ] Create invoice_items migration
- [ ] Create payments migration
- [ ] Create refunds migration
- [ ] Create all models with relationships
- [ ] Test relationships

### Fee Structure
- [ ] FeeComponentController (CRUD)
- [ ] Fee components views (index, create, edit)
- [ ] FeePlanController (CRUD)
- [ ] Fee plans views (index, create, edit, show)
- [ ] Add fee components to plan
- [ ] Routes for fee structure

### Student Fees
- [ ] StudentFeeCardController
- [ ] Auto-generate cards on enrollment
- [ ] View student fee card
- [ ] Apply discounts
- [ ] Waive fees
- [ ] Views for fee cards

### Invoices
- [ ] InvoiceController
- [ ] Auto-generate invoices
- [ ] Invoice views (list, create, view)
- [ ] Invoice numbering system
- [ ] Send invoice functionality

### Payments
- [ ] PaymentController
- [ ] Payment collection page
- [ ] Payment receipt generation
- [ ] Payment allocation logic
- [ ] Refund processing

### Reports
- [ ] Collection report
- [ ] Outstanding report
- [ ] Defaulter list
- [ ] Excel export
- [ ] PDF receipts

### Navigation & UI
- [ ] Add fees menu to sidebar
- [ ] Fee dashboard
- [ ] Statistics cards
- [ ] Professional UI

---

## 🚀 Quick Start Plan

### Day 1-2: Fee Components & Plans
- Create migrations
- Create models
- Build CRUD for components
- Build CRUD for plans
- Basic UI

### Day 3-4: Student Fee Cards
- Create fee card system
- Auto-generation logic
- Discount application
- Fee card views

### Day 5-7: Invoices & Payments
- Invoice generation
- Payment collection
- Receipt generation
- Payment allocation

### Day 8-10: Reports & Polish
- Collection reports
- Outstanding reports
- Excel export
- Testing & refinement

---

## 💡 Key Decisions

### Approach:
**Offline-first, Online later**
- Start with offline payments
- Add online gateway later
- Simpler, faster to build

### Fee Card Model:
**Product-Order Pattern** (like enrollment)
- Fee Plan = Product (template)
- Student Fee Card = Order (instance)
- Easy to customize per student

### Payment Allocation:
**FIFO (First In, First Out)**
- Pay oldest dues first
- Clear logic
- Easy to understand

---

**Status:** READY TO START  
**Next:** Create database schema and models  
**Goal:** Get basic fee collection working in 1 week

---

*Created: October 14, 2025*

