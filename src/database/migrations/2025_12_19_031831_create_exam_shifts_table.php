<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('exam_shifts')) {
            return; // Table already exists
        }

        Schema::create('exam_shifts', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('shift_name'); // e.g., "First Shift", "Second Shift", "Morning Shift"
            $table->string('shift_code')->nullable(); // e.g., "SHIFT_1", "SHIFT_2"
            $table->time('start_time'); // e.g., 09:30:00
            $table->time('end_time'); // e.g., 11:30:00
            $table->integer('duration_minutes'); // Calculated duration
            $table->text('class_ranges')->nullable(); // JSON: [{"min": 9, "max": 12}, {"min": 1, "max": 8}]
            $table->text('description')->nullable();
            $table->integer('display_order')->default(0); // For ordering shifts
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'is_active']);
            $table->unique(['tenant_id', 'shift_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_shifts');
    }
};
