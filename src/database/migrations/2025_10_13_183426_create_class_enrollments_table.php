<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('tenant_id')->index();

            // Class & Section Assignment
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->string('roll_number')->nullable();

            // Academic Year & Dates
            $table->string('academic_year', 20); // 2024-2025
            $table->date('enrollment_date'); // When student joined this class
            $table->date('start_date');
            $table->date('end_date')->nullable(); // Null if currently in this class

            // Enrollment Status
            $table->enum('enrollment_status', [
                'enrolled',      // Currently enrolled
                'promoted',      // Promoted to next class
                'passed',        // Passed and completed
                'failed',        // Failed, may repeat
                'transferred',   // Transferred out
                'dropped'        // Dropped out
            ])->default('enrolled');

            // Only ONE enrollment per student can be current
            $table->boolean('is_current')->default(false);

            // Results (filled at year end)
            $table->enum('result', ['promoted', 'passed', 'failed', 'transferred', 'dropped'])->nullable();
            $table->decimal('percentage', 5, 2)->nullable(); // 99.99
            $table->string('grade', 10)->nullable(); // A+, A, B, etc.
            $table->text('remarks')->nullable();

            // Promotion Info
            $table->foreignId('promoted_to_class_id')->nullable()->constrained('classes')->onDelete('set null');

            // Timestamps
            $table->timestamps();

            // Foreign Keys & Indexes
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['student_id', 'is_current']); // Fast lookup for current enrollment
            $table->index(['student_id', 'academic_year']);
            $table->index(['tenant_id', 'class_id', 'section_id', 'is_current']);

            // Unique constraint: Only one current enrollment per student
            $table->unique(['student_id', 'is_current'], 'unique_current_enrollment')
                  ->where('is_current', true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_enrollments');
    }
};
