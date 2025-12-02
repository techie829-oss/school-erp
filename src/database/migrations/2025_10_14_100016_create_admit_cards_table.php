<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admit_cards', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('section_id');

            // Hall ticket details
            $table->string('hall_ticket_number')->unique();
            $table->string('qr_code')->nullable(); // QR code data for verification
            $table->string('barcode')->nullable(); // Barcode data

            // Student details (snapshot at time of generation)
            $table->string('student_name');
            $table->string('admission_number');
            $table->string('roll_number')->nullable();
            $table->string('photo_path')->nullable();

            // Exam details
            $table->text('exam_details_json')->nullable(); // JSON of all exam schedules for this student

            // Generation details
            $table->unsignedBigInteger('generated_by');
            $table->timestamp('generated_at')->useCurrent();
            $table->boolean('is_printed')->default(false);
            $table->timestamp('printed_at')->nullable();

            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('generated_by')->references('id')->on('users')->onDelete('restrict');

            $table->unique(['exam_id', 'student_id'], 'unique_exam_student_admit');
            $table->index(['tenant_id', 'exam_id']);
            $table->index(['tenant_id', 'student_id']);
            $table->index(['hall_ticket_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admit_cards');
    }
};

