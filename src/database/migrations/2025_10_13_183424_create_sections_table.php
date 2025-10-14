<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('section_name'); // A, B, C, D
            $table->integer('capacity')->default(50);
            $table->string('room_number')->nullable();
            $table->foreignId('class_teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'class_id', 'section_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};
