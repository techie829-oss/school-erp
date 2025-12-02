<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('exam_name');
            $table->string('exam_type'); // mid_term, final, unit_test, quiz, assignment
            $table->string('academic_year')->nullable();
            $table->unsignedBigInteger('class_id')->nullable(); // If null, applies to all classes
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'ongoing', 'completed', 'published', 'archived'])->default('draft');
            $table->boolean('is_published')->default(false);
            $table->date('publish_date')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->index(['tenant_id', 'exam_type']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};

