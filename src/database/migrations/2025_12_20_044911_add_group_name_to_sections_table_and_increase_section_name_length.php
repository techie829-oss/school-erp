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
        Schema::table('sections', function (Blueprint $table) {
            // Add group_name field (optional, nullable)
            $table->string('group_name', 255)->nullable()->after('section_name');

            // Increase section_name length from default to 25 characters
            $table->string('section_name', 25)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            // Remove group_name field
            $table->dropColumn('group_name');

            // Revert section_name length back to default (255)
            $table->string('section_name')->change();
        });
    }
};
