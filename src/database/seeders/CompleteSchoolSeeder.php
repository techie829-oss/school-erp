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

        $this->command->warn("⚠️  This will create comprehensive test data for:");
        $this->command->warn("   - Departments, Subjects, Teachers");
        $this->command->warn("   - Classes, Sections (with Class Teachers)");
        $this->command->warn("   - Students with Enrollments");
        $this->command->warn("   - Examinations (Exams, Schedules, Results)");
        $this->command->warn("\n   Make sure you have a tenant in the database!");

        if (!$this->command->confirm("\nDo you want to continue?", true)) {
            $this->command->info("Seeding cancelled.");
            return;
        }

        $this->command->newLine();

        // Run all seeders in order
        $this->call([
            // Step 1: Teacher Management
            DepartmentSeeder::class,        // 8 departments
            SubjectSeeder::class,           // 24 subjects
            TeacherSeeder::class,           // 10 teachers with qualifications

            // Step 2: Student Management
            ClassSectionSeeder::class,      // 10 classes, 25+ sections (assigns class teachers)
            StudentSeeder::class,           // 200+ students with enrollments

            // Step 3: Examinations
            ExaminationSeeder::class,       // Exams, schedules, results
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

