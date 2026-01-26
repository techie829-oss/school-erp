<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_schedules', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_schedules', 'is_done')) {
                $table->boolean('is_done')->default(false)->after('supervisor_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exam_schedules', function (Blueprint $table) {
            if (Schema::hasColumn('exam_schedules', 'is_done')) {
                $table->dropColumn('is_done');
            }
        });
    }
};
