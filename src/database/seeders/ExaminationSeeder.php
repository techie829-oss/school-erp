<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\ExamResult;
use App\Models\GradeScale;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ExaminationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->command->error('No tenant found! Please create a tenant first.');
            return;
        }

        foreach ($tenants as $tenant) {
            $this->command->info("\n=== Creating examination data for tenant: {$tenant->id} ===");

            // Check prerequisites
            $classes = SchoolClass::forTenant($tenant->id)->with('sections')->get();
            $subjects = Subject::forTenant($tenant->id)->get();
            $students = Student::forTenant($tenant->id)->with('currentEnrollment')->get();
            $teachers = Teacher::forTenant($tenant->id)->get();

            if ($classes->isEmpty()) {
                $this->command->warn("  ⚠ Skipping {$tenant->id}: No classes found! Run ClassSectionSeeder first.");
                continue;
            }

            if ($subjects->isEmpty()) {
                $this->command->warn("  ⚠ Skipping {$tenant->id}: No subjects found! Run SubjectSeeder first.");
                continue;
            }

            if ($students->isEmpty()) {
                $this->command->warn("  ⚠ Skipping {$tenant->id}: No students found! Run StudentSeeder first.");
                continue;
            }

            // Step 1: Create Grade Scales if they don't exist
            $this->command->info("  Creating grade scales...");
            $this->createGradeScales($tenant->id);

            // Step 2: Create Exams
            $this->command->info("  Creating exams...");
            $exams = $this->createExams($tenant->id, $classes);

            // Step 3: Create Exam Schedules
            $this->command->info("  Creating exam schedules...");
            $schedules = $this->createExamSchedules($tenant->id, $exams, $classes, $subjects, $teachers);

            // Step 4: Create Exam Results (for some students)
            $this->command->info("  Creating exam results...");
            $this->createExamResults($tenant->id, $exams, $schedules, $students);

            $this->command->info("  ✅ Completed for {$tenant->id}!");
            $this->command->info("     - Grade Scales: " . GradeScale::forTenant($tenant->id)->count());
            $this->command->info("     - Exams: " . Exam::forTenant($tenant->id)->count());
            $this->command->info("     - Exam Schedules: " . ExamSchedule::forTenant($tenant->id)->count());
            $this->command->info("     - Exam Results: " . ExamResult::forTenant($tenant->id)->count());
        }

        $this->command->info("\n✅ Examination data seeding completed for all tenants!");
    }

    private function createGradeScales($tenantId): void
    {
        if (GradeScale::forTenant($tenantId)->count() > 0) {
            $this->command->info('   Grade scales already exist, skipping...');
            return;
        }

        $gradeScales = [
            ['grade_name' => 'A+', 'min_percentage' => 90, 'max_percentage' => 100, 'gpa_value' => 9.99, 'description' => 'Outstanding', 'is_pass' => true],
            ['grade_name' => 'A', 'min_percentage' => 80, 'max_percentage' => 89, 'gpa_value' => 9.00, 'description' => 'Excellent', 'is_pass' => true],
            ['grade_name' => 'B+', 'min_percentage' => 70, 'max_percentage' => 79, 'gpa_value' => 8.00, 'description' => 'Very Good', 'is_pass' => true],
            ['grade_name' => 'B', 'min_percentage' => 60, 'max_percentage' => 69, 'gpa_value' => 7.00, 'description' => 'Good', 'is_pass' => true],
            ['grade_name' => 'C+', 'min_percentage' => 50, 'max_percentage' => 59, 'gpa_value' => 6.00, 'description' => 'Above Average', 'is_pass' => true],
            ['grade_name' => 'C', 'min_percentage' => 40, 'max_percentage' => 49, 'gpa_value' => 5.00, 'description' => 'Average', 'is_pass' => true],
            ['grade_name' => 'D', 'min_percentage' => 33, 'max_percentage' => 39, 'gpa_value' => 4.00, 'description' => 'Pass', 'is_pass' => true],
            ['grade_name' => 'F', 'min_percentage' => 0, 'max_percentage' => 32, 'gpa_value' => 0.00, 'description' => 'Fail', 'is_pass' => false],
        ];

        foreach ($gradeScales as $index => $scale) {
            $gradeScale = GradeScale::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'grade_name' => $scale['grade_name'],
                ],
                [
                    'min_percentage' => $scale['min_percentage'],
                    'max_percentage' => $scale['max_percentage'],
                    'gpa_value' => $scale['gpa_value'],
                    'description' => $scale['description'],
                    'is_pass' => $scale['is_pass'],
                    'is_active' => true,
                    'order' => $index + 1,
                ]
            );

            if ($gradeScale->wasRecentlyCreated) {
                $this->command->info("   ✓ Created grade scale: {$scale['grade_name']}");
            }
        }
    }

    private function createExams($tenantId, $classes): array
    {
        $currentYear = date('Y');
        $academicYear = $currentYear . '-' . ($currentYear + 1);

        $exams = [];

        // Create different types of exams
        $examTypes = [
            ['name' => 'Unit Test 1', 'type' => 'unit_test', 'status' => 'completed'],
            ['name' => 'Mid-term Examination', 'type' => 'mid_term', 'status' => 'completed'],
            ['name' => 'Unit Test 2', 'type' => 'unit_test', 'status' => 'scheduled'],
            ['name' => 'Final Examination', 'type' => 'final', 'status' => 'scheduled'],
        ];

        foreach ($examTypes as $examData) {
            // Create exam for a few classes (not all)
            $selectedClasses = $classes->random(min(3, $classes->count()));

            foreach ($selectedClasses as $class) {
                $examName = $examData['name'] . ' - ' . $class->class_name;

                // Check if exam already exists
                $existingExam = Exam::forTenant($tenantId)
                    ->where('exam_name', $examName)
                    ->where('academic_year', $academicYear)
                    ->first();

                if ($existingExam) {
                    $exams[] = $existingExam;
                    continue;
                }

                $startDate = Carbon::now()->subMonths(rand(1, 3))->startOfWeek();
                $endDate = $startDate->copy()->addDays(5);

                // Get a user ID for created_by (use first admin user or 1 as default)
                $createdBy = \App\Models\User::first()?->id ?? 1;

                $exam = Exam::create([
                    'tenant_id' => $tenantId,
                    'exam_name' => $examName,
                    'exam_type' => $examData['type'],
                    'academic_year' => $academicYear,
                    'class_id' => $class->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $examData['status'],
                    'description' => $examData['name'] . ' for ' . $class->class_name,
                    'created_by' => $createdBy,
                ]);

                $exams[] = $exam;
            }
        }

        return $exams;
    }

    private function createExamSchedules($tenantId, $exams, $classes, $subjects, $teachers): array
    {
        $schedules = [];

        foreach ($exams as $exam) {
            $class = $classes->find($exam->class_id);
            if (!$class) continue;

            // Get subjects for this class (or use all subjects)
            $classSubjects = $subjects->random(min(5, $subjects->count()));
            $sections = $class->sections;

            $examDate = Carbon::parse($exam->start_date);
            $dayOffset = 0;

            foreach ($classSubjects as $subject) {
                // Schedule for each section
                foreach ($sections as $section) {
                    $scheduleDate = $examDate->copy()->addDays($dayOffset);

                    // Skip weekends
                    while ($scheduleDate->isWeekend()) {
                        $scheduleDate->addDay();
                    }

                    // Check if schedule already exists
                    $existingSchedule = ExamSchedule::forTenant($tenantId)
                        ->where('exam_id', $exam->id)
                        ->where('subject_id', $subject->id)
                        ->where('class_id', $class->id)
                        ->where('section_id', $section->id)
                        ->where('exam_date', $scheduleDate->format('Y-m-d'))
                        ->first();

                    if ($existingSchedule) {
                        $schedules[] = $existingSchedule;
                        continue;
                    }

                    $startTime = Carbon::createFromTime(9, 0, 0);
                    $endTime = $startTime->copy()->addHours(2);
                    $maxMarks = [50, 60, 70, 80, 100][array_rand([50, 60, 70, 80, 100])];
                    $passingMarks = round($maxMarks * 0.33);

                    $schedule = ExamSchedule::create([
                        'tenant_id' => $tenantId,
                        'exam_id' => $exam->id,
                        'subject_id' => $subject->id,
                        'class_id' => $class->id,
                        'section_id' => $section->id,
                        'exam_date' => $scheduleDate->format('Y-m-d'),
                        'start_time' => $startTime->format('H:i:s'),
                        'end_time' => $endTime->format('H:i:s'),
                        'duration_minutes' => 120,
                        'max_marks' => $maxMarks,
                        'passing_marks' => $passingMarks,
                        'room_number' => 'Room ' . rand(101, 120),
                        'supervisor_id' => $teachers->isNotEmpty() ? $teachers->random()->id : null,
                        'instructions' => 'Please bring your own stationery. Mobile phones are not allowed.',
                    ]);

                    $schedules[] = $schedule;
                }

                $dayOffset++;
            }
        }

        return $schedules;
    }

    private function createExamResults($tenantId, $exams, $schedules, $students): void
    {
        $resultsCreated = 0;

        foreach ($schedules as $schedule) {
            // Only create results for completed exams
            if ($schedule->exam->status !== 'completed') {
                continue;
            }

            // Get students for this class/section
            $eligibleStudents = $students->filter(function ($student) use ($schedule) {
                $enrollment = $student->currentEnrollment;
                if (!$enrollment) return false;

                return $enrollment->class_id == $schedule->class_id &&
                       ($schedule->section_id === null || $enrollment->section_id == $schedule->section_id);
            });

            if ($eligibleStudents->isEmpty()) {
                continue;
            }

            // Create results for 70-80% of students (some absent)
            $studentsToCreate = $eligibleStudents->random(
                max(1, (int)($eligibleStudents->count() * rand(70, 80) / 100))
            );

            foreach ($studentsToCreate as $student) {
                // Check if result already exists
                $existingResult = ExamResult::forTenant($tenantId)
                    ->where('exam_schedule_id', $schedule->id)
                    ->where('student_id', $student->id)
                    ->first();

                if ($existingResult) {
                    continue;
                }

                $isAbsent = rand(1, 100) <= 10; // 10% chance of absent

                if ($isAbsent) {
                    $marksObtained = 0;
                    $percentage = 0;
                    $grade = null;
                    $gpa = null;
                    $status = 'absent';
                } else {
                    // Generate realistic marks (some pass, some fail)
                    $passRate = rand(1, 100);
                    if ($passRate <= 80) {
                        // 80% pass rate - marks between passing and max
                        $marksObtained = rand(
                            (int)($schedule->passing_marks),
                            (int)($schedule->max_marks)
                        );
                    } else {
                        // 20% fail rate - marks below passing
                        $marksObtained = rand(0, (int)($schedule->passing_marks) - 1);
                    }

                    $percentage = round(($marksObtained / $schedule->max_marks) * 100, 2);

                    // Initialize grade and gpa
                    $grade = null;
                    $gpa = null;

                    // Get grade from grade scale
                    $gradeScale = GradeScale::forTenant($tenantId)
                        ->where('is_active', true)
                        ->where('min_percentage', '<=', $percentage)
                        ->where('max_percentage', '>=', $percentage)
                        ->first();

                    if ($gradeScale) {
                        $grade = $gradeScale->grade_name;
                        $gpa = $gradeScale->gpa_value;
                    } else {
                        // Fallback: assign grade based on percentage if no grade scale found
                        if ($percentage >= 90) {
                            $grade = 'A+';
                            $gpa = 9.99;
                        } elseif ($percentage >= 80) {
                            $grade = 'A';
                            $gpa = 9.00;
                        } elseif ($percentage >= 70) {
                            $grade = 'B+';
                            $gpa = 8.00;
                        } elseif ($percentage >= 60) {
                            $grade = 'B';
                            $gpa = 7.00;
                        } elseif ($percentage >= 50) {
                            $grade = 'C+';
                            $gpa = 6.00;
                        } elseif ($percentage >= 40) {
                            $grade = 'C';
                            $gpa = 5.00;
                        } elseif ($percentage >= 33) {
                            $grade = 'D';
                            $gpa = 4.00;
                        } else {
                            $grade = 'F';
                            $gpa = 0.00;
                        }
                    }

                    // Determine status
                    if ($percentage < 33) {
                        $status = 'fail';
                    } elseif ($schedule->passing_marks && $marksObtained < $schedule->passing_marks) {
                        $status = 'fail';
                    } else {
                        $status = 'pass';
                    }
                }

                // Get a user ID for entered_by (use first admin user or 1 as default)
                $enteredBy = \App\Models\User::first()?->id ?? 1;

                ExamResult::create([
                    'tenant_id' => $tenantId,
                    'exam_id' => $schedule->exam_id,
                    'exam_schedule_id' => $schedule->id,
                    'student_id' => $student->id,
                    'subject_id' => $schedule->subject_id,
                    'class_id' => $schedule->class_id,
                    'section_id' => $schedule->section_id,
                    'marks_obtained' => $marksObtained,
                    'max_marks' => $schedule->max_marks,
                    'passing_marks' => $schedule->passing_marks,
                    'percentage' => $percentage,
                    'grade' => $grade,
                    'gpa' => $gpa,
                    'status' => $status,
                    'is_absent' => $isAbsent,
                    'is_re_exam' => false,
                    'remarks' => $isAbsent ? 'Absent' : ($status === 'fail' ? 'Needs improvement' : null),
                    'entered_by' => $enteredBy,
                ]);

                $resultsCreated++;
            }
        }

        $this->command->info("   Created {$resultsCreated} exam results");
    }
}

