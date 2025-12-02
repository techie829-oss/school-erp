<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('section_id')->nullable(); // If null, applies to all sections
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('duration_minutes'); // Duration in minutes
            $table->string('room_number')->nullable();
            $table->string('hall_ticket_prefix')->nullable(); // For admit card generation
            $table->decimal('max_marks', 8, 2); // Maximum marks for this subject
            $table->decimal('passing_marks', 8, 2)->nullable(); // Passing marks
            $table->text('instructions')->nullable();
            $table->unsignedBigInteger('supervisor_id')->nullable(); // Teacher supervising
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('supervisor_id')->references('id')->on('teachers')->onDelete('set null');
            $table->index(['tenant_id', 'exam_id']);
            $table->index(['tenant_id', 'exam_date']);
            $table->index(['tenant_id', 'class_id', 'section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_schedules');
    }
};

