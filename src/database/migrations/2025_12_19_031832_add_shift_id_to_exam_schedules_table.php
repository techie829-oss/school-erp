<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_schedules', 'shift_id')) {
                $table->unsignedBigInteger('shift_id')->nullable()->after('exam_id');
            }

            // Check if foreign key already exists
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'exam_schedules'
                AND COLUMN_NAME = 'shift_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            if (empty($foreignKeys)) {
                $table->foreign('shift_id')->references('id')->on('exam_shifts')->onDelete('set null');
            }

            // Check if index already exists
            $indexes = DB::select("
                SELECT INDEX_NAME
                FROM information_schema.STATISTICS
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'exam_schedules'
                AND INDEX_NAME = 'exam_schedules_tenant_id_shift_id_index'
            ");

            if (empty($indexes)) {
                $table->index(['tenant_id', 'shift_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->dropForeign(['shift_id']);
            $table->dropIndex(['tenant_id', 'shift_id']);
            $table->dropColumn('shift_id');
        });
    }
};
