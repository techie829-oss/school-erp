<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('section_id');
            
            // Overall performance
            $table->decimal('total_marks', 10, 2)->default(0);
            $table->decimal('max_total_marks', 10, 2)->default(0);
            $table->decimal('overall_percentage', 5, 2)->nullable();
            $table->string('overall_grade', 10)->nullable();
            $table->decimal('overall_gpa', 3, 2)->nullable();
            
            // Rank
            $table->integer('class_rank')->nullable();
            $table->integer('section_rank')->nullable();
            
            // Status
            $table->enum('overall_status', ['pass', 'fail'])->default('pass');
            $table->integer('subjects_passed')->default(0);
            $table->integer('subjects_failed')->default(0);
            $table->integer('subjects_absent')->default(0);
            
            // Remarks
            $table->text('class_teacher_remarks')->nullable();
            $table->text('principal_remarks')->nullable();
            $table->string('attendance_percentage', 5)->nullable();
            
            // Generation details
            $table->unsignedBigInteger('generated_by');
            $table->timestamp('generated_at')->useCurrent();
            $table->boolean('is_published')->default(false);
            $table->date('published_at')->nullable();
            
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('restrict');
            
            $table->unique(['exam_id', 'student_id'], 'unique_exam_student_report');
            $table->index(['tenant_id', 'exam_id']);
            $table->index(['tenant_id', 'student_id']);
            $table->index(['tenant_id', 'class_id', 'section_id']);
            $table->index(['is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_cards');
    }
};

