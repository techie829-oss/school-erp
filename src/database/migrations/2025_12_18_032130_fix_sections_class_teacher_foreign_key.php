<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing foreign key constraint that points to users table
        // The constraint name is typically: {table}_{column}_foreign
        Schema::table('sections', function (Blueprint $table) {
            // Drop the foreign key - Laravel will find it by column name
            $table->dropForeign(['class_teacher_id']);
        });

        // Add new foreign key constraint that points to teachers table
        Schema::table('sections', function (Blueprint $table) {
            $table->foreign('class_teacher_id')
                ->references('id')
                ->on('teachers')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the foreign key constraint that points to teachers table
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['sections_class_teacher_id_foreign']);
        });

        // Restore the foreign key constraint that points to users table
        Schema::table('sections', function (Blueprint $table) {
            $table->foreign('class_teacher_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }
};
