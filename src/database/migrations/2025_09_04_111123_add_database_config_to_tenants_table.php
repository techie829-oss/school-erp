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
        Schema::table('tenants', function (Blueprint $table) {
            // Database configuration for separate database tenants
            $table->string('database_name')->nullable()->after('id');
            $table->string('database_host')->nullable()->after('database_name');
            $table->integer('database_port')->default(3306)->after('database_host');
            $table->string('database_username')->nullable()->after('database_port');
            $table->string('database_password')->nullable()->after('database_username');
            $table->string('database_charset')->default('utf8mb4')->after('database_password');
            $table->string('database_collation')->default('utf8mb4_unicode_ci')->after('database_charset');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'database_name',
                'database_host',
                'database_port',
                'database_username',
                'database_password',
                'database_charset',
                'database_collation'
            ]);
        });
    }
};
