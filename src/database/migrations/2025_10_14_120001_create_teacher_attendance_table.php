<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_attendance', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('teacher_id');

            $table->date('attendance_date');
            $table->enum('status', ['present', 'absent', 'late', 'half_day', 'on_leave', 'holiday']);

            // Timing
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->decimal('total_hours', 4, 2)->nullable();
            $table->decimal('working_hours', 4, 2)->default(8.00);

            // Leave Details
            $table->string('leave_type', 50)->nullable(); // Sick, Casual, Earned, etc.
            $table->unsignedBigInteger('leave_id')->nullable(); // Reference to leave request
            $table->text('leave_reason')->nullable();

            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('marked_by');
            $table->timestamp('marked_at')->useCurrent();

            $table->timestamps();

            // Foreign Keys & Indexes
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('marked_by')->references('id')->on('users')->onDelete('restrict');

            $table->unique(['teacher_id', 'attendance_date'], 'unique_teacher_date');
            $table->index(['attendance_date']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_attendance');
    }
};

