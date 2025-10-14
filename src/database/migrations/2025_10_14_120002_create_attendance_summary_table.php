<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_summary', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('attendable_type', 50); // 'student' or 'teacher'
            $table->unsignedBigInteger('attendable_id');

            $table->tinyInteger('month'); // 1-12
            $table->year('year');

            $table->integer('total_days')->default(0);
            $table->integer('present_days')->default(0);
            $table->integer('absent_days')->default(0);
            $table->integer('late_days')->default(0);
            $table->integer('half_days')->default(0);
            $table->integer('leave_days')->default(0);
            $table->integer('holiday_days')->default(0);

            $table->decimal('attendance_percentage', 5, 2)->default(0.00);

            $table->timestamps();

            // Foreign Keys & Indexes
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');

            $table->unique(['tenant_id', 'attendable_type', 'attendable_id', 'month', 'year'], 'unique_attendance_summary');
            $table->index(['month', 'year']);
            $table->index(['attendable_type', 'attendable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_summary');
    }
};

