<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

DB::statement('SET FOREIGN_KEY_CHECKS=0');
if (Schema::hasTable('routes')) {
    Schema::drop('routes');
    echo "Dropped routes table\n";
}
DB::statement('SET FOREIGN_KEY_CHECKS=1');

DB::table('migrations')->where('migration', '2025_12_02_084448_create_routes_table')->delete();
echo "Removed migration entry\n";
echo "Now run: php artisan migrate\n";

