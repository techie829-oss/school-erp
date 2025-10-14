<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_qualifications', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('teacher_id');

            $table->enum('qualification_type', ['academic', 'professional', 'certification', 'training'])->default('academic');
            $table->string('degree_name'); // B.Ed, M.Ed, B.Sc, M.Sc, etc.
            $table->string('specialization')->nullable(); // Mathematics, Physics, etc.
            $table->string('institution_name');
            $table->string('university_board')->nullable();

            $table->year('year_of_passing');
            $table->string('grade_percentage', 20)->nullable();
            $table->string('certificate_number', 100)->nullable();

            // Document
            $table->string('certificate_document')->nullable();

            // Verification
            $table->boolean('is_verified')->default(false);
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            // Foreign Keys & Indexes
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->index(['teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_qualifications');
    }
};

