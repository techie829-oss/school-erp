<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('study_materials', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('topic_id')->constrained('course_topics')->onDelete('cascade');
            $table->string('title');
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable(); // pdf, doc, video, link
            $table->string('file_size')->nullable();
            $table->string('url')->nullable(); // For external links
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_materials');
    }
};
