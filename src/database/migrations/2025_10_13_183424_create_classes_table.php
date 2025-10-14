<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->string('class_name'); // Class 1, Class 2, Grade 10, Year 1 BSc
            $table->integer('class_numeric')->nullable(); // 1, 2, 3... for ordering
            $table->string('class_type')->default('school'); // school, college
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'class_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
