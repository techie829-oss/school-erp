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

            // School Timing
            $table->time('school_start_time')->default('09:00:00');
            $table->time('school_end_time')->default('17:00:00');
            $table->time('late_arrival_after')->default('09:15:00');
            $table->integer('grace_period_minutes')->default(15);

            // Attendance Policies
            $table->decimal('minimum_working_hours', 3, 1)->default(8.0);
            $table->decimal('half_day_threshold_hours', 3, 1)->default(4.0);
            $table->json('weekend_days')->nullable(); // ['sunday', 'saturday', ...]

            // Notification Settings
            $table->boolean('auto_mark_absent')->default(false);
            $table->boolean('require_remarks_for_absent')->default(false);
            $table->integer('allow_edit_after_days')->default(7);

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

