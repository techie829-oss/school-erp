<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Make capacity nullable
        DB::statement('ALTER TABLE sections MODIFY COLUMN capacity INT NULL');
    }

    public function down(): void
    {
        // Revert to NOT NULL with default 50
        DB::statement('ALTER TABLE sections MODIFY COLUMN capacity INT NOT NULL DEFAULT 50');
    }
};
