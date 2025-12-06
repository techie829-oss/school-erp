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
        Schema::table('cms_pages', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('cms_pages', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
            if (!Schema::hasColumn('cms_pages', 'is_published')) {
                $table->boolean('is_published')->default(true)->after('settings');
            }
            // Change content to JSON if it's not already
            // Note: This might need manual migration if content column exists with different type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table) {
            if (Schema::hasColumn('cms_pages', 'meta_keywords')) {
                $table->dropColumn('meta_keywords');
            }
            if (Schema::hasColumn('cms_pages', 'is_published')) {
                $table->dropColumn('is_published');
            }
        });
    }
};
