<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guard against running this migration multiple times
        if (!Schema::hasColumn('sections', 'class_teacher_id')) {
            Schema::table('sections', function (Blueprint $table) {
                $table->unsignedBigInteger('class_teacher_id')->nullable()->after('capacity');
                $table->foreign('class_teacher_id')
                    ->references('id')
                    ->on('teachers')
                    ->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sections', 'class_teacher_id')) {
            Schema::table('sections', function (Blueprint $table) {
                $table->dropForeign(['class_teacher_id']);
                $table->dropColumn('class_teacher_id');
            });
        }
    }
};

