<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first tenant (or you can specify your tenant ID)
        $tenant = Tenant::first();

        if (!$tenant) {
            $this->command->error('No tenant found! Please create a tenant first.');
            return;
        }

        $this->command->info("Creating subjects for tenant: {$tenant->id}");

        $subjects = [
            // Core Subjects
            ['name' => 'Mathematics', 'code' => 'MATH', 'type' => 'core', 'desc' => 'Algebra, Geometry, Calculus'],
            ['name' => 'Physics', 'code' => 'PHY', 'type' => 'core', 'desc' => 'Mechanics, Thermodynamics, Optics'],
            ['name' => 'Chemistry', 'code' => 'CHEM', 'type' => 'core', 'desc' => 'Organic, Inorganic, Physical Chemistry'],
            ['name' => 'Biology', 'code' => 'BIO', 'type' => 'core', 'desc' => 'Botany, Zoology, Human Biology'],
            ['name' => 'English', 'code' => 'ENG', 'type' => 'core', 'desc' => 'Grammar, Literature, Composition'],
            ['name' => 'Hindi', 'code' => 'HIN', 'type' => 'core', 'desc' => 'Hindi Language and Literature'],
            ['name' => 'Social Studies', 'code' => 'SST', 'type' => 'core', 'desc' => 'History, Geography, Civics'],
            ['name' => 'History', 'code' => 'HIST', 'type' => 'core', 'desc' => 'World History, Indian History'],
            ['name' => 'Geography', 'code' => 'GEO', 'type' => 'core', 'desc' => 'Physical and Human Geography'],
            ['name' => 'Economics', 'code' => 'ECO', 'type' => 'core', 'desc' => 'Micro and Macro Economics'],

            // Elective Subjects
            ['name' => 'Computer Science', 'code' => 'CS', 'type' => 'elective', 'desc' => 'Programming, Data Structures, Algorithms'],
            ['name' => 'Information Technology', 'code' => 'IT', 'type' => 'elective', 'desc' => 'Web Development, Databases, Networks'],
            ['name' => 'Commerce', 'code' => 'COM', 'type' => 'elective', 'desc' => 'Accountancy, Business Studies'],
            ['name' => 'Psychology', 'code' => 'PSY', 'type' => 'elective', 'desc' => 'Human Behavior and Mental Processes'],
            ['name' => 'Political Science', 'code' => 'POL', 'type' => 'elective', 'desc' => 'Political Theory and Indian Politics'],

            // Optional Subjects
            ['name' => 'Sanskrit', 'code' => 'SAN', 'type' => 'optional', 'desc' => 'Sanskrit Language and Literature'],
            ['name' => 'French', 'code' => 'FR', 'type' => 'optional', 'desc' => 'French Language'],
            ['name' => 'German', 'code' => 'GER', 'type' => 'optional', 'desc' => 'German Language'],
            ['name' => 'Home Science', 'code' => 'HS', 'type' => 'optional', 'desc' => 'Nutrition, Textiles, Family Studies'],

            // Extra Curricular
            ['name' => 'Music', 'code' => 'MUS', 'type' => 'extra_curricular', 'desc' => 'Vocal and Instrumental Music'],
            ['name' => 'Dance', 'code' => 'DAN', 'type' => 'extra_curricular', 'desc' => 'Classical and Modern Dance'],
            ['name' => 'Art & Craft', 'code' => 'ART', 'type' => 'extra_curricular', 'desc' => 'Drawing, Painting, Sculpture'],
            ['name' => 'Physical Education', 'code' => 'PE', 'type' => 'extra_curricular', 'desc' => 'Sports and Physical Fitness'],
            ['name' => 'Drama', 'code' => 'DRAM', 'type' => 'extra_curricular', 'desc' => 'Theater and Performance Arts'],
        ];

        foreach ($subjects as $subj) {
            Subject::create([
                'tenant_id' => $tenant->id,
                'subject_name' => $subj['name'],
                'subject_code' => $subj['code'],
                'subject_type' => $subj['type'],
                'description' => $subj['desc'],
                'is_active' => true,
            ]);

            $this->command->info("✓ Created: {$subj['name']} ({$subj['type']})");
        }

        $this->command->info("\n✅ Successfully created " . count($subjects) . " subjects!");
    }
}

