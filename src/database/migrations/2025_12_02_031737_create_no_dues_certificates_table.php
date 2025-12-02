<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('no_dues_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('section_id')->nullable();

            // Certificate details
            $table->string('certificate_number')->unique();
            $table->date('issue_date');
            $table->text('remarks')->nullable();

            // Verification
            $table->boolean('library_clearance')->default(false);
            $table->boolean('fee_clearance')->default(false);
            $table->boolean('lab_clearance')->default(false);
            $table->boolean('sports_clearance')->default(false);
            $table->boolean('hostel_clearance')->default(false);
            $table->text('clearance_remarks')->nullable();

            // Status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();

            // Generation details
            $table->unsignedBigInteger('generated_by');
            $table->timestamp('generated_at')->useCurrent();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            $table->index(['tenant_id', 'student_id']);
            $table->index(['tenant_id', 'status']);
            $table->index('certificate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('no_dues_certificates');
    }
};
