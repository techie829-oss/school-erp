<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();

            $table->string('department_name', 100);
            $table->string('department_code', 20)->nullable();
            $table->text('description')->nullable();

            $table->unsignedBigInteger('head_teacher_id')->nullable(); // Will be linked after teachers table

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Foreign Keys & Indexes
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'department_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};

