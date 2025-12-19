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
        Schema::table('student_attendance', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['section_id']);

            // Make section_id nullable
            $table->unsignedBigInteger('section_id')->nullable()->change();

            // Re-add foreign key with nullable support
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_attendance', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['section_id']);

            // Make section_id NOT NULL again
            $table->unsignedBigInteger('section_id')->nullable(false)->change();

            // Re-add foreign key
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
        });
    }
};
