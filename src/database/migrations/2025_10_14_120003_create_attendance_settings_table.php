<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->unique();

            // Student Attendance Settings
            $table->boolean('student_enable_period_wise')->default(false);
            $table->integer('student_periods_per_day')->default(1);
            $table->decimal('student_half_day_threshold', 3, 1)->default(4.0);
            $table->integer('student_late_threshold_minutes')->default(15);

            // Teacher Attendance Settings
            $table->decimal('teacher_working_hours_per_day', 3, 1)->default(8.0);
            $table->decimal('teacher_half_day_threshold', 3, 1)->default(4.0);
            $table->integer('teacher_late_threshold_minutes')->default(15);
            $table->boolean('teacher_enable_biometric')->default(false);

            // General Settings
            $table->enum('week_start_day', ['sunday', 'monday'])->default('monday');
            $table->json('working_days')->nullable(); // ['monday', 'tuesday', ...]
            $table->json('holidays')->nullable(); // ['2025-10-02', '2025-12-25', ...]

            // Notifications
            $table->boolean('notify_parent_on_absent')->default(true);
            $table->boolean('notify_admin_on_teacher_absent')->default(true);
            $table->decimal('low_attendance_threshold', 3, 1)->default(75.0);

            $table->timestamps();

            // Foreign Keys
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
    }
};

