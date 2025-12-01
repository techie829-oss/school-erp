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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('topic_id')->nullable()->constrained('course_topics')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('total_marks')->default(100);
            $table->integer('pass_marks')->default(40);
            $table->integer('duration_minutes')->nullable(); // Null means no time limit
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
