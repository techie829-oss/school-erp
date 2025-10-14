<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('section_id');

            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'on_leave', 'holiday']);

            // Period-wise attendance (optional)
            $table->integer('period_number')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();

            // Leave details
            $table->string('leave_reason')->nullable();
            $table->unsignedBigInteger('leave_approved_by')->nullable();

            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('marked_by');
            $table->timestamp('marked_at')->useCurrent();

            $table->timestamps();

            // Foreign Keys & Indexes
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
            $table->foreign('marked_by')->references('id')->on('users')->onDelete('restrict');

            $table->unique(['student_id', 'attendance_date', 'period_number'], 'unique_student_date_period');
            $table->index(['attendance_date']);
            $table->index(['class_id', 'section_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendance');
    }
};

