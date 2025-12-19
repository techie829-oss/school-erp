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
        // Find and drop the existing foreign key constraint that points to users table
        $constraintName = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'classes'
            AND COLUMN_NAME = 'class_teacher_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if ($constraintName) {
            $constraintName = $constraintName->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE `classes` DROP FOREIGN KEY `{$constraintName}`");
        }

        // Add new foreign key constraint that points to teachers table
        Schema::table('classes', function (Blueprint $table) {
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
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['classes_class_teacher_id_foreign']);
        });

        // Restore the foreign key constraint that points to users table
        Schema::table('classes', function (Blueprint $table) {
            $table->foreign('class_teacher_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }
};
