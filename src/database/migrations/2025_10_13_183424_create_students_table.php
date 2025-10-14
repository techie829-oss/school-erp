<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();

            // Admission Details
            $table->string('admission_number')->unique(); // STU-2024-001
            $table->date('admission_date');

            // Personal Information
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('full_name'); // Auto-generated
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->string('blood_group')->nullable();
            $table->string('nationality')->default('Indian');
            $table->string('religion')->nullable();
            $table->enum('category', ['general', 'obc', 'sc', 'st', 'other'])->default('general');

            // Contact Information
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('photo')->nullable();

            // Address
            $table->json('current_address')->nullable();
            $table->json('permanent_address')->nullable();
            $table->boolean('same_as_current')->default(false);

            // Parent/Guardian Information
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_phone')->nullable();
            $table->string('father_email')->nullable();

            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_phone')->nullable();
            $table->string('mother_email')->nullable();

            $table->string('guardian_name')->nullable();
            $table->string('guardian_relation')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->string('guardian_email')->nullable();

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();

            // Medical Information
            $table->json('medical_info')->nullable();

            // Previous School Info
            $table->string('previous_school_name')->nullable();
            $table->string('previous_class')->nullable();
            $table->string('tc_number')->nullable();

            // Overall Status (not class-specific)
            $table->enum('overall_status', ['active', 'alumni', 'transferred', 'dropped_out'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->text('status_remarks')->nullable();

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Foreign Keys & Indexes
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'admission_number']);
            $table->index(['full_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
