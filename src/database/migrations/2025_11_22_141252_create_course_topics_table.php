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
        Schema::create('course_topics', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('chapter_id')->constrained('course_chapters')->onDelete('cascade');
            $table->string('topic_name');
            $table->integer('topic_number')->nullable();
            $table->text('description')->nullable();
            $table->longText('content')->nullable(); // HTML/Text content
            $table->string('video_url')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_topics');
    }
};
