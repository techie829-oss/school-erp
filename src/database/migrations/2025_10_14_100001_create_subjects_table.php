<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();

            $table->string('subject_name', 100);
            $table->string('subject_code', 20)->nullable();
            $table->enum('subject_type', ['core', 'elective', 'optional', 'extra_curricular'])->default('core');
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Foreign Keys & Indexes
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'subject_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};

