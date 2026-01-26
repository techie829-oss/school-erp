# Software Usage Flow & Architecture

This document visualizes the core operational flows of the School ERP system based on the current implementation.

## 1. High-Level Architecture (Multi-Tenancy)
The system is divided into two distinct domains: the Super Admin (SaaS owner) and the Tenant (School).

```mermaid
graph TD
    User((User))
    
    subgraph "Super Admin Domain"
        SA_Auth[Login / Auth]
        SA_Dash[Super Admin Dashboard]
        Tenant_Mgmt[Tenant Management]
        Subdomain_Prov[Subdomain Provisioning]
        
        SA_Auth -->|Credentials| SA_Dash
        SA_Dash --> Tenant_Mgmt
        Tenant_Mgmt -->|Create School| Subdomain_Prov
    end
    
    subgraph "School/Tenant Domain"
        School_Auth[School Login]
        School_Dash[School Dashboard]
        
        Roles{User Role}
        Admin_Panel[Admin Panel]
        Teacher_Portal[Teacher Portal]
        Student_Portal[Student Portal]
        
        School_Auth -->|Credentials| School_Dash
        School_Dash --> Roles
        Roles -->|Admin| Admin_Panel
        Roles -->|Teacher| Teacher_Portal
        Roles -->|Student/Parent| Student_Portal
    end

    User -->|app.myschool.test| SA_Auth
    User -->|school.myschool.test| School_Auth
    Subdomain_Prov -.->|Creates| School_Auth
```

## 2. Student Lifecycle Flow
This flow tracks a student from admission to promotion/graduation.

```mermaid
graph LR
    Start((Start)) --> Admission[New Admission]
    Admission -->|Assign Class & Section| Active[Active Student]
    
    Active --> Daily{Daily Ops}
    Daily --> Attendance[Attendance]
    Daily --> LMS[LMS Learning]
    Daily --> Fee[Fee Payment]
    
    Active --> Exam[Examinations]
    Exam --> Result[Result Generation]
    
    Result --> Promotion{Year End}
    Promotion -->|Pass| Promote[Promote to Next Class]
    Promotion -->|Fail| Repeat[Repeat Class]
    Promotion -->|Leave| Alumni[Alumni / Transfer]
    
    Promote --> Active
    Repeat --> Active
    Alumni --> End((End))
    
    subgraph "Admin Actions"
    Admission
    Promote
    Repeat
    Alumni
    end
```

## 3. Fee Management Flow
How fee structures are created, assigned, and collected.

```mermaid
sequenceDiagram
    participant Admin
    participant System
    participant Parent
    
    rect rgb(240, 248, 255)
    Note over Admin, System: Configuration Phase
    Admin->>System: Create Fee Components (Tuition, Transport, etc.)
    Admin->>System: Create Fee Plan (Standard Class 5)
    Admin->>System: Assign Plan to Class/Student
    System-->>Parent: Generate Fee Card / Invoice
    end
    
    rect rgb(255, 250, 240)
    Note over Parent, System: Collection Phase
    Parent->>System: View Pending Fees
    Parent->>Admin: Pay Fee (Cash/Online)
    Admin->>System: Record Payment
    System->>System: Update Fee Card (Paid)
    System->>Parent: Generate Receipt
    end
```

## 4. Examination & Grading Flow
The process of setting up exams, conducting them, and generating report cards.

```mermaid
graph TD
    Setup[Exam Setup] -->|1. Create Exam| ExamRec[Exam Record]
    Setup -->|2. Create Schedule| Schedule[Time Table]
    
    Prep[Preparation] -->|3. Generate Admit Cards| AdmitCards[Admit Cards]
    
    Execution[Execution] -->|4. Conduct Exams| Grading[Grading]
    
    Grading -->|5. Entry| MarksEntry[Marks Entry]
    MarksEntry -->|6. Processing| GradeCalc[Calculate Grades]
    
    Reporting[Reporting] -->|7. Publish| Results[Exam Results]
    Results -->|8. Generate| ReportCard[Report Card PDF]
    
    Admin((Admin)) --> Setup
    Teacher((Teacher)) --> MarksEntry
```

## 5. LMS (Learning Management System) Flow
The flow for creating and consuming educational content.

```mermaid
graph TD
    Teacher((Teacher))
    Student((Student))
    
    subgraph "Content Creation"
        Course[Create Course]
        Chapters[Add Chapters]
        Topics[Add Topics/Content]
        Assignments[Create Assignments]
        Quiz[Create Quiz]
        
        Teacher --> Course
        Course --> Chapters --> Topics
        Course --> Assignments
        Course --> Quiz
    end
    
    subgraph "Learning & Assessment"
        View[View Content]
        Submit[Submit Assignment]
        TakeQuiz[Attempt Quiz]
        
        Student --> View
        Student --> Submit
        Student --> TakeQuiz
    end
    
    subgraph "Evaluation"
        GradeAssign[Grade Assignment]
        AutoGrade[Auto-Grade Quiz]
        
        Teacher --> GradeAssign
        TakeQuiz --> AutoGrade
    end

    Topics -.-> View
    Assignments -.-> Submit
    Submit -.-> GradeAssign
    Quiz -.-> TakeQuiz
```

## 6. Detailed Attendance Flow
The logic behind marking attendance, handling notifications, and reporting.

```mermaid
graph TD
    User[User (Admin/Teacher)] --> SelectAction{Select Action}
    
    SelectAction -->|Daily Attendance| MarkDaily[Mark Class Attendance]
    SelectAction -->|Period Attendance| MarkPeriod[Mark Period Attendance]
    
    subgraph "Processing Logic"
        MarkDaily -->|Submit| Validate[Validate Input]
        MarkPeriod -->|Submit| Validate
        
        Validate -->|Success| SaveDB[Update DB (student_attendance)]
        
        SaveDB --> CalcSummary[Recalculate Monthly Summary]
        
        CalcSummary --> CheckAbsent{Is Absent?}
        CheckAbsent -->|Yes| NotifyAbsent[Send Absent SMS/Email]
        CheckAbsent -->|No| CheckLow{< 75% Attendance?}
        
        CheckLow -->|Yes| NotifyLow[Send Low Attendance Warning]
        CheckLow -->|No| Finish
    end
    
    subgraph "Reporting"
        Dashboard[Attendance Dashboard] --> DailyStats[Daily Stats]
        Dashboard --> MonthlySummary[Monthly Summary]
        
        ReportGen[Report Generation] --> Types{Report Types}
        Types --> DailyRep[Daily Report]
        Types --> MonthlyRep[Monthly Report]
        Types --> StudentRep[Student Wise]
        Types --> DefaulterRep[Defaulters List]
        
        Types --> Export[Export PDF/Excel]
    end
```
