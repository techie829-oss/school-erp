<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TeacherManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder runs all teacher management related seeders in the correct order
     */
    public function run(): void
    {
        $this->command->info("\n🎓 Starting Teacher Management System Seeding...\n");

        // Run in order: Departments -> Subjects -> Teachers
        $this->call([
            DepartmentSeeder::class,
            SubjectSeeder::class,
            TeacherSeeder::class,
        ]);

        $this->command->info("\n✅ Teacher Management System seeding completed successfully!");
        $this->command->info("📊 Summary:");
        $this->command->info("   - 8 Departments");
        $this->command->info("   - 24 Subjects (Core, Elective, Optional, Extra Curricular)");
        $this->command->info("   - 10 Teachers with qualifications and subject assignments");
        $this->command->info("\n🚀 You can now access: http://{tenant}.test/admin/teachers\n");
    }
}

