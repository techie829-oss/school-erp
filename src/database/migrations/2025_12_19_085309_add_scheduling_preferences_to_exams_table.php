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
        Schema::table('exams', function (Blueprint $table) {
            $table->integer('max_exams_per_day')->nullable()->after('end_date')->comment('Maximum number of exams per day for each class');
            $table->enum('shift_selection_mode', ['class_wise', 'subject_wise', 'both'])->nullable()->after('max_exams_per_day')->comment('How shifts are selected');
            $table->unsignedBigInteger('default_shift_id')->nullable()->after('shift_selection_mode');
            $table->boolean('skip_weekends')->default(true)->after('default_shift_id')->comment('Whether to skip weekends when scheduling');
            $table->decimal('default_max_marks', 8, 2)->nullable()->after('skip_weekends')->comment('Default max marks for schedules');
            $table->decimal('default_passing_marks', 8, 2)->nullable()->after('default_max_marks')->comment('Default passing marks for schedules');
            $table->integer('default_duration_minutes')->nullable()->after('default_passing_marks')->comment('Default exam duration in minutes');
            $table->json('scheduling_preferences')->nullable()->after('default_duration_minutes')->comment('For class-specific or subject-specific preferences');

            // Add foreign key for default_shift_id
            $table->foreign('default_shift_id')->references('id')->on('exam_shifts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['default_shift_id']);

            // Drop columns
            $table->dropColumn([
                'max_exams_per_day',
                'shift_selection_mode',
                'default_shift_id',
                'skip_weekends',
                'default_max_marks',
                'default_passing_marks',
                'default_duration_minutes',
                'scheduling_preferences'
            ]);
        });
    }
};
