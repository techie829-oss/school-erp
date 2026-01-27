<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CompleteSchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder populates the entire school management system with realistic test data
     * Run this seeder to get a complete demo-ready school system
     */
    public function run(): void
    {
        $this->command->info("\n" . str_repeat('=', 70));
        $this->command->info("🏫 COMPLETE SCHOOL ERP SYSTEM - TEST DATA SEEDER");
        $this->command->info(str_repeat('=', 70) . "\n");

        // 1. Create Demo Tenant (if not exists)
        $tenant = \App\Models\Tenant::firstOrCreate(
            ['id' => 'demo-school'],
            [
                'data' => [
                    'name' => 'Demo Public School',
                    'email' => 'admin@demo.com',
                    'type' => 'school',
                    'database_strategy' => 'shared',
                    'subdomain' => 'demo',
                    'full_domain' => 'demo.myschool.test',
                    'custom_domain' => null,
                    'active' => true,
                    'description' => 'Demo school for testing and showcase',
                    'student_count' => 500,
                    'location' => 'Demo City, Cloud',
                    'established' => '2025',
                    'curriculum' => 'CBSE',
                    'is_active' => true,
                ]
            ]
        );
        $this->command->info("Tenant '{$tenant->name}' ready.");

        // 2. Create Admin User for this Tenant
        $admin = \App\Models\AdminUser::firstOrCreate(
            ['email' => 'admin@demo.com'],
            [
                'name' => 'Demo Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'tenant_id' => $tenant->id,
                'admin_type' => \App\Models\AdminUser::TYPE_SCHOOL_ADMIN,
                'is_active' => true,
                'notes' => 'Admin for Demo Tenant',
            ]
        );
        $this->command->info("Admin '{$admin->email}' ready.");

        $this->command->newLine();

        $this->command->newLine();

        // Run all seeders in order
        $this->call([
                // Step 1: Teacher Management
            Demo\DepartmentSeeder::class,        // 8 departments
            Demo\SubjectSeeder::class,           // 24 subjects
            Demo\TeacherSeeder::class,           // 10 teachers with qualifications

                // Step 2: Student Management
            Demo\ClassSectionSeeder::class,      // 10 classes, 25+ sections (assigns class teachers)
            Demo\StudentSeeder::class,           // 200+ students with enrollments

                // Step 3: Examinations
            Demo\ExaminationSeeder::class,       // Exams, schedules, results
        ]);

        $this->command->newLine();
        $this->command->info(str_repeat('=', 70));
        $this->command->info("✅ COMPLETE SCHOOL ERP SEEDING FINISHED!");
        $this->command->info(str_repeat('=', 70));

        $this->command->info("\n📊 Your School ERP Now Has:");
        $this->command->info("   ✅ 8 Departments");
        $this->command->info("   ✅ 24 Subjects (Core, Elective, Optional, Extra Curricular)");
        $this->command->info("   ✅ 10 Teachers (with qualifications and subject assignments)");
        $this->command->info("   ✅ 10 Classes (Class 1-10)");
        $this->command->info("   ✅ 25+ Sections (with Class Teachers assigned)");
        $this->command->info("   ✅ 200+ Students (with current enrollments)");
        $this->command->info("   ✅ Grade Scales (A+ to F)");
        $this->command->info("   ✅ Exams (Unit Tests, Mid-term, Final)");
        $this->command->info("   ✅ Exam Schedules with timetables");
        $this->command->info("   ✅ Exam Results with grades");

        $this->command->info("\n🚀 Ready to Explore:");
        $this->command->info("   👨‍🏫 Teachers: http://{tenant}.test/admin/teachers");
        $this->command->info("   👨‍🎓 Students: http://{tenant}.test/admin/students");
        $this->command->info("   🏢 Departments: http://{tenant}.test/admin/departments");
        $this->command->info("   📚 Subjects: http://{tenant}.test/admin/subjects");
        $this->command->info("   📖 Classes: http://{tenant}.test/admin/classes");
        $this->command->info("   📋 Sections: http://{tenant}.test/admin/sections");
        $this->command->info("   📝 Exams: http://{tenant}.test/admin/examinations/exams");
        $this->command->info("   📅 Schedules: http://{tenant}.test/admin/examinations/schedules");
        $this->command->info("   📊 Results: http://{tenant}.test/admin/examinations/results");

        $this->command->newLine();
        $this->command->info("🎉 Your School ERP is fully populated and ready for testing!");
        $this->command->newLine();
    }
}

