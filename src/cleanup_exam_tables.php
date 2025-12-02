<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = ['admit_cards', 'report_cards', 'exam_results', 'exam_schedules', 'exams'];

DB::statement('SET FOREIGN_KEY_CHECKS=0');

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        Schema::drop($table);
        echo "Dropped table: $table\n";
    }
}

// Remove migration entries
$migrations = [
    '2025_10_14_100012_create_exams_table',
    '2025_10_14_100013_create_exam_schedules_table',
    '2025_10_14_100014_create_exam_results_table',
    '2025_10_14_100015_create_report_cards_table',
    '2025_10_14_100016_create_admit_cards_table',
];

foreach ($migrations as $migration) {
    DB::table('migrations')->where('migration', $migration)->delete();
    echo "Removed migration entry: $migration\n";
}

DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "\nDone! Now run: php artisan migrate\n";

