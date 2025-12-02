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
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('exam_id')->nullable(); // Optional: link to exam if needed
            $table->string('mark_type')->default('assignment'); // assignment, quiz, project, test, exam, etc.
            $table->string('title')->nullable(); // Title of the assessment
            $table->date('assessment_date')->nullable();
            
            // Marks
            $table->decimal('marks_obtained', 8, 2)->default(0);
            $table->decimal('max_marks', 8, 2);
            $table->decimal('percentage', 5, 2)->nullable(); // Calculated: (marks_obtained / max_marks) * 100
            
            // Grades
            $table->string('grade', 10)->nullable(); // Letter grade (A+, A, B+, etc.)
            $table->decimal('gpa', 3, 2)->nullable(); // GPA value
            
            // Status
            $table->enum('status', ['pass', 'fail', 'pending'])->default('pending');
            $table->boolean('is_absent')->default(false);
            
            // Entry details
            $table->unsignedBigInteger('entered_by');
            $table->timestamp('entered_at')->useCurrent();
            $table->text('remarks')->nullable();
            
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('set null');
            $table->foreign('entered_by')->references('id')->on('users')->onDelete('restrict');
            
            $table->index(['tenant_id', 'student_id']);
            $table->index(['tenant_id', 'class_id', 'section_id']);
            $table->index(['tenant_id', 'subject_id']);
            $table->index(['tenant_id', 'exam_id']);
            $table->index(['assessment_date']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marks');
    }
};
