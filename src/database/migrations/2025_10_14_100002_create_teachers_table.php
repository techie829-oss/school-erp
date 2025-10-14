<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('user_id')->nullable(); // Link to users table for login

            // Admission/Employee Details
            $table->string('employee_id', 50)->unique(); // Auto-generated: TCH-2025-001

            // Personal Information
            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('full_name', 255); // Auto-generated

            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('date_of_birth');
            $table->string('blood_group', 10)->nullable();
            $table->string('nationality', 100)->default('Indian');
            $table->string('religion', 50)->nullable();
            $table->enum('category', ['general', 'obc', 'sc', 'st', 'other'])->nullable();

            // Contact Information
            $table->string('email')->nullable()->unique();
            $table->string('phone', 20)->nullable();
            $table->string('alternate_phone', 20)->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relation', 50)->nullable();

            // Address
            $table->json('current_address')->nullable();
            $table->json('permanent_address')->nullable();

            // Employment Details
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('designation', 100)->nullable(); // Principal, Vice Principal, Teacher, etc.
            $table->enum('employment_type', ['permanent', 'contract', 'temporary', 'visiting'])->default('permanent');
            $table->date('date_of_joining');
            $table->date('date_of_leaving')->nullable();

            // Qualifications (Summary)
            $table->string('highest_qualification', 100)->nullable(); // B.Ed, M.Ed, PhD, etc.
            $table->decimal('experience_years', 4, 1)->nullable(); // Total experience in years

            // Salary & Financial
            $table->decimal('salary_amount', 10, 2)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_ifsc_code', 20)->nullable();
            $table->string('pan_number', 20)->nullable();
            $table->string('aadhar_number', 20)->nullable();

            // System Fields
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['active', 'on_leave', 'resigned', 'retired', 'terminated'])->default('active');
            $table->text('status_remarks')->nullable();
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys & Indexes
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');

            $table->index(['tenant_id', 'employee_id']);
            $table->index(['full_name']);
            $table->index(['status', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};

