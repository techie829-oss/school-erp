<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class StudentManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder runs all student management related seeders in the correct order
     */
    public function run(): void
    {
        $this->command->info("\n👨‍🎓 Starting Student Management System Seeding...\n");

        // Run in order: Classes/Sections -> Students
        $this->call([
            ClassSectionSeeder::class,
            StudentSeeder::class,
        ]);

        $this->command->info("\n✅ Student Management System seeding completed successfully!");
        $this->command->info("📊 Summary:");
        $this->command->info("   - 10 Classes (Class 1 to Class 10)");
        $this->command->info("   - 25+ Sections (A, B, C)");
        $this->command->info("   - 200+ Students with enrollments");
        $this->command->info("\n🚀 You can now access: http://{tenant}.test/admin/students\n");
    }
}

