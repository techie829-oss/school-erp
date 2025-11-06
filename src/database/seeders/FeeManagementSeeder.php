<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\SchoolClass;
use App\Models\Student;

class FeeManagementSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Get the first tenant (SVPS)
        $tenant = Tenant::first();

        if (!$tenant) {
            $this->command->error('No tenant found! Please run TenantSeeder first.');
            return;
        }

        $tenantId = $tenant->id;
        $this->command->info("Creating fee management data for tenant: {$tenant->data['name']}");

        // Clear existing fee data
        $this->command->info('Clearing old fee data...');
        DB::table('student_fee_items')->whereIn('student_fee_card_id',
            DB::table('student_fee_cards')->where('tenant_id', $tenantId)->pluck('id')
        )->delete();
        DB::table('student_fee_cards')->where('tenant_id', $tenantId)->delete();
        DB::table('fee_plan_items')->whereIn('fee_plan_id',
            DB::table('fee_plans')->where('tenant_id', $tenantId)->pluck('id')
        )->delete();
        DB::table('fee_plans')->where('tenant_id', $tenantId)->delete();
        DB::table('fee_components')->where('tenant_id', $tenantId)->delete();
        $this->command->info('Old fee data cleared.');

        // Create Fee Components
        $this->command->info('Creating fee components...');
        $components = [
            [
                'tenant_id' => $tenantId,
                'code' => 'TUI',
                'name' => 'Tuition Fee',
                'type' => 'recurring',
                'description' => 'Monthly tuition fee for regular classes',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => $tenantId,
                'code' => 'ADM',
                'name' => 'Admission Fee',
                'type' => 'one_time',
                'description' => 'One-time admission fee for new students',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => $tenantId,
                'code' => 'EXAM',
                'name' => 'Examination Fee',
                'type' => 'recurring',
                'description' => 'Fee for term examinations',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => $tenantId,
                'code' => 'LIB',
                'name' => 'Library Fee',
                'type' => 'recurring',
                'description' => 'Annual library membership and maintenance fee',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => $tenantId,
                'code' => 'SPORT',
                'name' => 'Sports Fee',
                'type' => 'recurring',
                'description' => 'Sports equipment and activities fee',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => $tenantId,
                'code' => 'COMP',
                'name' => 'Computer Lab Fee',
                'type' => 'recurring',
                'description' => 'Computer lab usage and maintenance fee',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => $tenantId,
                'code' => 'TRANS',
                'name' => 'Transport Fee',
                'type' => 'recurring',
                'description' => 'Monthly school bus transportation fee',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tenant_id' => $tenantId,
                'code' => 'EVENT',
                'name' => 'Annual Day Fee',
                'type' => 'one_time',
                'description' => 'Annual day event and celebration fee',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($components as $component) {
            DB::table('fee_components')->insert($component);
        }
        $this->command->info('Created ' . count($components) . ' fee components.');

        // Get component IDs
        $tuitionId = DB::table('fee_components')->where('tenant_id', $tenantId)->where('name', 'Tuition Fee')->value('id');
        $admissionId = DB::table('fee_components')->where('tenant_id', $tenantId)->where('name', 'Admission Fee')->value('id');
        $examId = DB::table('fee_components')->where('tenant_id', $tenantId)->where('name', 'Examination Fee')->value('id');
        $libraryId = DB::table('fee_components')->where('tenant_id', $tenantId)->where('name', 'Library Fee')->value('id');
        $sportsId = DB::table('fee_components')->where('tenant_id', $tenantId)->where('name', 'Sports Fee')->value('id');
        $computerLabId = DB::table('fee_components')->where('tenant_id', $tenantId)->where('name', 'Computer Lab Fee')->value('id');
        $transportId = DB::table('fee_components')->where('tenant_id', $tenantId)->where('name', 'Transport Fee')->value('id');
        $annualDayId = DB::table('fee_components')->where('tenant_id', $tenantId)->where('name', 'Annual Day Fee')->value('id');

        // Get classes
        $classes = SchoolClass::where('tenant_id', $tenantId)->get();

        if ($classes->isEmpty()) {
            $this->command->warn('No classes found. Skipping fee plans creation.');
            return;
        }

        // Create Fee Plans for each class
        $this->command->info('Creating fee plans...');
        $planCount = 0;

        foreach ($classes as $class) {
            // Standard Fee Plan (Primary Classes 1-5)
            if (in_array($class->class_name, ['Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5'])) {
                $plan = [
                    'tenant_id' => $tenantId,
                    'name' => $class->class_name . ' - Standard Plan',
                    'description' => 'Standard fee plan for ' . $class->class_name . ' including all mandatory fees',
                    'class_id' => $class->id,
                    'academic_year' => '2024-2025',
                    'term' => 'annual',
                    'effective_from' => '2024-04-01',
                    'effective_to' => '2025-03-31',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $planId = DB::table('fee_plans')->insertGetId($plan);
                $planCount++;

                // Attach components
                DB::table('fee_plan_items')->insert([
                    ['fee_plan_id' => $planId, 'fee_component_id' => $tuitionId, 'amount' => 2500.00, 'is_mandatory' => true, 'due_date' => '2024-05-10'],
                    ['fee_plan_id' => $planId, 'fee_component_id' => $examId, 'amount' => 1500.00, 'is_mandatory' => true, 'due_date' => '2024-06-15'],
                    ['fee_plan_id' => $planId, 'fee_component_id' => $libraryId, 'amount' => 1200.00, 'is_mandatory' => true, 'due_date' => '2024-05-10'],
                    ['fee_plan_id' => $planId, 'fee_component_id' => $computerLabId, 'amount' => 1000.00, 'is_mandatory' => true, 'due_date' => '2024-05-10'],
                ]);
            }

            // Enhanced Fee Plan (Middle Classes 6-8)
            if (in_array($class->class_name, ['Class 6', 'Class 7', 'Class 8'])) {
                $plan = [
                    'tenant_id' => $tenantId,
                    'name' => $class->class_name . ' - Enhanced Plan',
                    'description' => 'Enhanced fee plan for ' . $class->class_name . ' with additional facilities',
                    'class_id' => $class->id,
                    'academic_year' => '2024-2025',
                    'term' => 'annual',
                    'effective_from' => '2024-04-01',
                    'effective_to' => '2025-03-31',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $planId = DB::table('fee_plans')->insertGetId($plan);
                $planCount++;

                // Attach components
                DB::table('fee_plan_items')->insert([
                    ['fee_plan_id' => $planId, 'fee_component_id' => $tuitionId, 'amount' => 3000.00, 'is_mandatory' => true, 'due_date' => '2024-05-10'],
                    ['fee_plan_id' => $planId, 'fee_component_id' => $examId, 'amount' => 1500.00, 'is_mandatory' => true, 'due_date' => '2024-06-15'],
                    ['fee_plan_id' => $planId, 'fee_component_id' => $libraryId, 'amount' => 1200.00, 'is_mandatory' => true, 'due_date' => '2024-05-10'],
                    ['fee_plan_id' => $planId, 'fee_component_id' => $computerLabId, 'amount' => 1000.00, 'is_mandatory' => true, 'due_date' => '2024-05-10'],
                    ['fee_plan_id' => $planId, 'fee_component_id' => $sportsId, 'amount' => 800.00, 'is_mandatory' => false, 'due_date' => '2024-05-10'],
                ]);
            }

            // Premium Fee Plan (Senior Classes 9-12)
            if (in_array($class->class_name, ['Class 9', 'Class 10', 'Class 11', 'Class 12'])) {
                $plan = [
                    'tenant_id' => $tenantId,
                    'name' => $class->class_name . ' - Premium Plan',
                    'description' => 'Premium fee plan for ' . $class->class_name . ' with all facilities',
                    'class_id' => $class->id,
                    'academic_year' => '2024-2025',
                    'term' => 'annual',
                    'effective_from' => '2024-04-01',
                    'effective_to' => '2025-03-31',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $planId = DB::table('fee_plans')->insertGetId($plan);
                $planCount++;

                // Attach components
                DB::table('fee_plan_items')->insert([
                    ['fee_plan_id' => $planId, 'fee_component_id' => $tuitionId, 'amount' => 3500.00, 'is_mandatory' => true, 'due_date' => '2024-05-10'],
                    ['fee_plan_id' => $planId, 'fee_component_id' => $examId, 'amount' => 2000.00, 'is_mandatory' => true, 'due_date' => '2024-06-15'],
                    ['fee_plan_id' => $planId, 'fee_component_id' => $libraryId, 'amount' => 1200.00, 'is_mandatory' => true, 'due_date' => '2024-05-10'],
                    ['fee_plan_id' => $planId, 'fee_component_id' => $computerLabId, 'amount' => 1000.00, 'is_mandatory' => true, 'due_date' => '2024-05-10'],
                    ['fee_plan_id' => $planId, 'fee_component_id' => $sportsId, 'amount' => 800.00, 'is_mandatory' => false, 'due_date' => '2024-05-10'],
                    ['fee_plan_id' => $planId, 'fee_component_id' => $annualDayId, 'amount' => 500.00, 'is_mandatory' => false, 'due_date' => '2024-07-01'],
                ]);
            }
        }

        $this->command->info('Created ' . $planCount . ' fee plans.');

        // Create sample fee cards for existing students
        $this->command->info('Creating sample fee cards...');
        $students = Student::where('tenant_id', $tenantId)
            ->whereHas('enrollments')
            ->with('enrollments')
            ->take(15)
            ->get();
        $cardCount = 0;
        $itemCount = 0;

        foreach ($students as $student) {
            $enrollment = $student->enrollments->first();
            if (!$enrollment) {
                continue;
            }

            // Get the fee plan for this student's class
            $plan = DB::table('fee_plans')
                ->where('tenant_id', $tenantId)
                ->where('class_id', $enrollment->class_id)
                ->where('is_active', true)
                ->first();

            if (!$plan) {
                continue;
            }

            // Get fee plan items
            $planItems = DB::table('fee_plan_items')
                ->where('fee_plan_id', $plan->id)
                ->get();

            $totalAmount = $planItems->sum('amount');
            $paidAmount = rand(0, 1) ? $totalAmount * 0.5 : $totalAmount; // 50% paid or fully paid
            $balanceAmount = $totalAmount - $paidAmount;

            $status = $balanceAmount == 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'active');

            // Create fee card for student
            $feeCard = [
                'tenant_id' => $tenantId,
                'student_id' => $student->id,
                'fee_plan_id' => $plan->id,
                'academic_year' => '2024-2025',
                'total_amount' => $totalAmount,
                'discount_amount' => 0,
                'paid_amount' => $paidAmount,
                'balance_amount' => $balanceAmount,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $feeCardId = DB::table('student_fee_cards')->insertGetId($feeCard);
            $cardCount++;

            // Create fee items for each component
            foreach ($planItems as $planItem) {
                $itemPaidAmount = $paidAmount > 0 ? min($planItem->amount, $paidAmount) : 0;
                $paidAmount -= $itemPaidAmount;

                $itemStatus = $itemPaidAmount >= $planItem->amount ? 'paid' : ($itemPaidAmount > 0 ? 'partial' : 'unpaid');

                $feeItem = [
                    'student_fee_card_id' => $feeCardId,
                    'fee_component_id' => $planItem->fee_component_id,
                    'original_amount' => $planItem->amount,
                    'discount_amount' => 0,
                    'net_amount' => $planItem->amount,
                    'due_date' => $planItem->due_date,
                    'paid_amount' => $itemPaidAmount,
                    'status' => $itemStatus,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                DB::table('student_fee_items')->insert($feeItem);
                $itemCount++;
            }
        }

        $this->command->info('Created ' . $cardCount . ' fee cards and ' . $itemCount . ' fee items.');
        $this->command->info('✓ Fee management seeder completed successfully!');
    }
}

