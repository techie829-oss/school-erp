<?php

namespace Database\Seeders;

use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Tenant;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class ClassSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenant = Tenant::first();

        if (!$tenant) {
            $this->command->error('No tenant found! Please create a tenant first.');
            return;
        }

        $this->command->info("Creating classes and sections for tenant: {$tenant->id}");

        // Get teachers for class teacher assignment
        $teachers = Teacher::forTenant($tenant->id)->active()->get();

        $classes = [
            ['name' => 'Class 1', 'numeric' => 1, 'sections' => ['A', 'B']],
            ['name' => 'Class 2', 'numeric' => 2, 'sections' => ['A', 'B']],
            ['name' => 'Class 3', 'numeric' => 3, 'sections' => ['A', 'B']],
            ['name' => 'Class 4', 'numeric' => 4, 'sections' => ['A', 'B']],
            ['name' => 'Class 5', 'numeric' => 5, 'sections' => ['A', 'B']],
            ['name' => 'Class 6', 'numeric' => 6, 'sections' => ['A', 'B', 'C']],
            ['name' => 'Class 7', 'numeric' => 7, 'sections' => ['A', 'B', 'C']],
            ['name' => 'Class 8', 'numeric' => 8, 'sections' => ['A', 'B', 'C']],
            ['name' => 'Class 9', 'numeric' => 9, 'sections' => ['A', 'B', 'C']],
            ['name' => 'Class 10', 'numeric' => 10, 'sections' => ['A', 'B', 'C']],
        ];

        $teacherIndex = 0;
        $totalSections = 0;

        foreach ($classes as $classData) {
            // Check if class already exists
            $class = SchoolClass::where('tenant_id', $tenant->id)
                ->where('class_name', $classData['name'])
                ->first();

            if (!$class) {
                $class = SchoolClass::create([
                    'tenant_id' => $tenant->id,
                    'class_name' => $classData['name'],
                    'class_numeric' => $classData['numeric'],
                    'class_type' => 'school',
                    'is_active' => true,
                ]);
                $this->command->info("✓ Created class: {$classData['name']}");
            } else {
                $this->command->info("  Class already exists: {$classData['name']}");
            }

            // Create sections
            foreach ($classData['sections'] as $sectionName) {
                $section = Section::where('tenant_id', $tenant->id)
                    ->where('class_id', $class->id)
                    ->where('section_name', $sectionName)
                    ->first();

                if (!$section) {
                    // Assign a class teacher if available
                    $classTeacherId = null;
                    if ($teachers->isNotEmpty() && $teacherIndex < $teachers->count()) {
                        $classTeacherId = $teachers[$teacherIndex]->id;
                        $teacherIndex++;
                    }

                    Section::create([
                        'tenant_id' => $tenant->id,
                        'class_id' => $class->id,
                        'section_name' => $sectionName,
                        'capacity' => 40,
                        'room_number' => 'R-' . $classData['numeric'] . $sectionName,
                        'class_teacher_id' => $classTeacherId,
                        'is_active' => true,
                    ]);

                    $totalSections++;
                    $this->command->info("  ✓ Created section: {$classData['name']}-{$sectionName}" . ($classTeacherId ? " (Class Teacher assigned)" : ""));
                }
            }
        }

        $this->command->info("\n✅ Successfully created/verified " . count($classes) . " classes and {$totalSections} new sections!");
    }
}

