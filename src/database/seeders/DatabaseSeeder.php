<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Call other seeders
        $this->call([
            TenantSeeder::class,
            AdminUserSeeder::class,
            ColorPaletteSeeder::class,
        ]);
    }
}
