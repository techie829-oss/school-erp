<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->integer('capacity')->nullable()->after('description');
            $table->string('room_number')->nullable()->after('capacity');
            $table->foreignId('class_teacher_id')->nullable()->after('room_number')->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['class_teacher_id']);
            $table->dropColumn(['capacity', 'room_number', 'class_teacher_id']);
        });
    }
};
