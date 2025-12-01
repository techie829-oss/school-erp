<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('grade_name', 10); // A+, A, B+, B, etc.
            $table->decimal('min_percentage', 5, 2); // Minimum percentage for this grade
            $table->decimal('max_percentage', 5, 2); // Maximum percentage for this grade
            $table->decimal('gpa_value', 3, 2)->nullable(); // GPA value (e.g., 4.0, 3.5)
            $table->string('description')->nullable();
            $table->boolean('is_pass')->default(true); // Whether this grade is passing
            $table->integer('order')->default(0); // Display order
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'grade_name']);
            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
    }
};

