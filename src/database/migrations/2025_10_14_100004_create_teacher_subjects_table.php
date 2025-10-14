<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_subjects', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('class_id')->nullable(); // Specific class or NULL for all classes

            $table->boolean('is_primary')->default(false); // Primary subject expertise
            $table->decimal('years_teaching', 3, 1)->nullable();

            $table->timestamps();

            // Foreign Keys & Indexes
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');

            $table->unique(['teacher_id', 'subject_id', 'class_id'], 'unique_teacher_subject_class');
            $table->index(['teacher_id']);
            $table->index(['subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_subjects');
    }
};

