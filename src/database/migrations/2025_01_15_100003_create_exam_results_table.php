<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_results', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('exam_schedule_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('section_id');
            
            // Marks
            $table->decimal('marks_obtained', 8, 2)->default(0);
            $table->decimal('max_marks', 8, 2);
            $table->decimal('passing_marks', 8, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable(); // Calculated: (marks_obtained / max_marks) * 100
            
            // Grades
            $table->string('grade', 10)->nullable(); // Letter grade (A+, A, B+, etc.)
            $table->decimal('gpa', 3, 2)->nullable(); // GPA value
            
            // Status
            $table->enum('status', ['pass', 'fail', 'absent', 're_exam'])->default('pass');
            $table->boolean('is_absent')->default(false);
            $table->boolean('is_re_exam')->default(false);
            
            // Moderation
            $table->decimal('original_marks', 8, 2)->nullable(); // Original marks before moderation
            $table->text('moderation_reason')->nullable();
            $table->unsignedBigInteger('moderated_by')->nullable();
            $table->timestamp('moderated_at')->nullable();
            
            // Entry details
            $table->unsignedBigInteger('entered_by');
            $table->timestamp('entered_at')->useCurrent();
            $table->text('remarks')->nullable();
            
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('exam_schedule_id')->references('id')->on('exam_schedules')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('moderated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('entered_by')->references('id')->on('users')->onDelete('restrict');
            
            $table->unique(['exam_schedule_id', 'student_id'], 'unique_exam_student');
            $table->index(['tenant_id', 'exam_id']);
            $table->index(['tenant_id', 'student_id']);
            $table->index(['tenant_id', 'class_id', 'section_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};

