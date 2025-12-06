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
        Schema::table('cms_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('cms_settings', 'default_language')) {
                $table->string('default_language', 10)->default('en')->after('tenant_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cms_settings', function (Blueprint $table) {
            if (Schema::hasColumn('cms_settings', 'default_language')) {
                $table->dropColumn('default_language');
            }
        });
    }
};
