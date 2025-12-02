<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\TransportAssignment;
use App\Models\TransportBill;
use App\Models\TransportBillItem;
use App\Models\TransportPayment;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransportSeeder extends Seeder
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
            $this->command->info("\n=== Creating transport data for tenant: {$tenant->id} ===");

            // Check prerequisites
            $students = Student::forTenant($tenant->id)->active()->get();

            if ($students->isEmpty()) {
                $this->command->warn("  ⚠ Skipping {$tenant->id}: No students found! Run StudentSeeder first.");
                continue;
            }

            // Step 1: Create Drivers
            $this->command->info("  Creating drivers...");
            $drivers = $this->createDrivers($tenant->id);

            // Step 2: Create Routes with Stops
            $this->command->info("  Creating routes...");
            $routes = $this->createRoutes($tenant->id);

            // Step 3: Create Vehicles
            $this->command->info("  Creating vehicles...");
            $vehicles = $this->createVehicles($tenant->id, $drivers, $routes);

            // Step 4: Create Transport Assignments
            $this->command->info("  Creating transport assignments...");
            $assignments = $this->createAssignments($tenant->id, $students, $routes, $vehicles);

            // Step 5: Create Transport Bills
            $this->command->info("  Creating transport bills...");
            $bills = $this->createBills($tenant->id, $assignments);

            // Step 6: Create Transport Payments
            $this->command->info("  Creating transport payments...");
            $this->createPayments($tenant->id, $bills);

            $this->command->info("  ✅ Transport data created successfully for {$tenant->id}!");
        }

        $this->command->info("\n" . str_repeat('=', 70));
        $this->command->info("✅ TRANSPORT SEEDING FINISHED!");
        $this->command->info(str_repeat('=', 70) . "\n");
    }

    private function createDrivers($tenantId)
    {
        $drivers = [];
        $driverNames = [
            ['name' => 'Rajesh Kumar', 'phone' => '9876543210', 'license' => 'DL-1234567890'],
            ['name' => 'Mohan Singh', 'phone' => '9876543211', 'license' => 'DL-1234567891'],
            ['name' => 'Suresh Patel', 'phone' => '9876543212', 'license' => 'DL-1234567892'],
            ['name' => 'Amit Sharma', 'phone' => '9876543213', 'license' => 'DL-1234567893'],
            ['name' => 'Vikram Yadav', 'phone' => '9876543214', 'license' => 'DL-1234567894'],
        ];

        foreach ($driverNames as $index => $driverData) {
            $drivers[] = Driver::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'license_number' => $driverData['license'],
                ],
                [
                'tenant_id' => $tenantId,
                'name' => $driverData['name'],
                'phone' => $driverData['phone'],
                'email' => strtolower(str_replace(' ', '.', $driverData['name'])) . '@example.com',
                'license_number' => $driverData['license'],
                'license_type' => 'Heavy Vehicle',
                'license_issue_date' => Carbon::now()->subYears(5),
                'license_expiry_date' => Carbon::now()->addYears(5),
                'date_of_birth' => Carbon::now()->subYears(35 + $index),
                'gender' => 'male',
                'joining_date' => Carbon::now()->subYears(2 + $index),
                'salary' => 25000 + ($index * 2000),
                'address' => 'Driver Address ' . ($index + 1) . ', City',
                'emergency_contact_name' => 'Emergency Contact ' . ($index + 1),
                'emergency_contact_phone' => '9876543' . (200 + $index),
                'status' => 'active',
                ]
            );
        }

        return $drivers;
    }

    private function createRoutes($tenantId)
    {
        $routes = [];
        $routeData = [
            [
                'name' => 'North Route',
                'route_number' => 'RT-001',
                'start_location' => 'School Main Gate',
                'end_location' => 'North Area Terminal',
                'distance' => 15.5,
                'base_fare' => 1500,
                'stops' => [
                    ['name' => 'Main Gate', 'order' => 1, 'fare' => 0],
                    ['name' => 'City Center', 'order' => 2, 'fare' => 500],
                    ['name' => 'North Market', 'order' => 3, 'fare' => 1000],
                    ['name' => 'North Terminal', 'order' => 4, 'fare' => 1500],
                ],
            ],
            [
                'name' => 'South Route',
                'route_number' => 'RT-002',
                'start_location' => 'School Main Gate',
                'end_location' => 'South Area Terminal',
                'distance' => 18.2,
                'base_fare' => 1800,
                'stops' => [
                    ['name' => 'Main Gate', 'order' => 1, 'fare' => 0],
                    ['name' => 'South Park', 'order' => 2, 'fare' => 600],
                    ['name' => 'South Market', 'order' => 3, 'fare' => 1200],
                    ['name' => 'South Terminal', 'order' => 4, 'fare' => 1800],
                ],
            ],
            [
                'name' => 'East Route',
                'route_number' => 'RT-003',
                'start_location' => 'School Main Gate',
                'end_location' => 'East Area Terminal',
                'distance' => 12.8,
                'base_fare' => 1200,
                'stops' => [
                    ['name' => 'Main Gate', 'order' => 1, 'fare' => 0],
                    ['name' => 'East Junction', 'order' => 2, 'fare' => 400],
                    ['name' => 'East Terminal', 'order' => 3, 'fare' => 1200],
                ],
            ],
            [
                'name' => 'West Route',
                'route_number' => 'RT-004',
                'start_location' => 'School Main Gate',
                'end_location' => 'West Area Terminal',
                'distance' => 20.0,
                'base_fare' => 2000,
                'stops' => [
                    ['name' => 'Main Gate', 'order' => 1, 'fare' => 0],
                    ['name' => 'West Mall', 'order' => 2, 'fare' => 800],
                    ['name' => 'West Station', 'order' => 3, 'fare' => 1500],
                    ['name' => 'West Terminal', 'order' => 4, 'fare' => 2000],
                ],
            ],
        ];

        foreach ($routeData as $routeInfo) {
            $route = Route::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'route_number' => $routeInfo['route_number'],
                ],
                [
                'name' => $routeInfo['name'],
                'start_location' => $routeInfo['start_location'],
                'end_location' => $routeInfo['end_location'],
                'distance' => $routeInfo['distance'],
                'base_fare' => $routeInfo['base_fare'],
                'status' => 'active',
                'description' => 'Transport route for ' . $routeInfo['name'],
                ]
            );

            // Create stops for this route (only if they don't exist)
            foreach ($routeInfo['stops'] as $stopInfo) {
                RouteStop::firstOrCreate(
                    [
                        'route_id' => $route->id,
                        'stop_order' => $stopInfo['order'],
                    ],
                    [
                    'route_id' => $route->id,
                    'stop_name' => $stopInfo['name'],
                    'stop_order' => $stopInfo['order'],
                    'fare_from_start' => $stopInfo['fare'],
                    'stop_address' => $stopInfo['name'] . ' Stop Address',
                    ]
                );
            }

            $routes[] = $route;
        }

        return $routes;
    }

    private function createVehicles($tenantId, $drivers, $routes)
    {
        $vehicles = [];
        $vehicleData = [
            ['number' => 'BUS-001', 'type' => 'bus', 'capacity' => 50, 'make' => 'Tata', 'model' => 'Starbus'],
            ['number' => 'BUS-002', 'type' => 'bus', 'capacity' => 45, 'make' => 'Ashok Leyland', 'model' => 'Viking'],
            ['number' => 'VAN-001', 'type' => 'van', 'capacity' => 20, 'make' => 'Mahindra', 'model' => 'Traveller'],
            ['number' => 'VAN-002', 'type' => 'van', 'capacity' => 18, 'make' => 'Force', 'model' => 'Traveller'],
            ['number' => 'BUS-003', 'type' => 'bus', 'capacity' => 40, 'make' => 'Tata', 'model' => 'Marcopolo'],
        ];

        // Get tenant prefix for unique vehicle numbers
        $tenantPrefix = strtoupper(substr($tenantId, 0, 3));

        foreach ($vehicleData as $index => $vehicleInfo) {
            $uniqueVehicleNumber = $tenantPrefix . '-' . $vehicleInfo['number'];

            $vehicles[] = Vehicle::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'vehicle_number' => $uniqueVehicleNumber,
                ],
                [
                'vehicle_type' => $vehicleInfo['type'],
                'make' => $vehicleInfo['make'],
                'model' => $vehicleInfo['model'],
                'capacity' => $vehicleInfo['capacity'],
                'color' => ['Blue', 'Red', 'Green', 'Yellow', 'White'][$index],
                'registration_number' => 'REG-' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                'manufacturing_year' => 2020 + $index,
                'driver_id' => $drivers[$index % count($drivers)]->id,
                'route_id' => $routes[$index % count($routes)]->id,
                'registration_date' => Carbon::now()->subYears(2 + $index),
                'insurance_expiry' => Carbon::now()->addMonths(6 + $index),
                'permit_expiry' => Carbon::now()->addMonths(8 + $index),
                'fitness_expiry' => Carbon::now()->addMonths(4 + $index),
                'status' => 'active',
                'notes' => 'Vehicle ' . ($index + 1) . ' notes',
                ]
            );
        }

        return $vehicles;
    }

    private function createAssignments($tenantId, $students, $routes, $vehicles)
    {
        $assignments = [];
        $studentCount = min(50, $students->count()); // Assign transport to first 50 students
        $selectedStudents = $students->take($studentCount);

        foreach ($selectedStudents as $index => $student) {
            $route = $routes[$index % count($routes)];
            $vehicle = $vehicles[$index % count($vehicles)];
            $stops = $route->stops;

            if ($stops->count() >= 2) {
                $pickupStop = $stops->random();
                $dropStop = $stops->where('stop_order', '>', $pickupStop->stop_order)->first() ?? $stops->last();
            } else {
                $pickupStop = $stops->first();
                $dropStop = $stops->last();
            }

            $monthlyFare = $route->base_fare + (rand(0, 500));

            $assignments[] = TransportAssignment::create([
                'tenant_id' => $tenantId,
                'student_id' => $student->id,
                'route_id' => $route->id,
                'vehicle_id' => $vehicle->id,
                'pickup_stop_id' => $pickupStop->id ?? null,
                'drop_stop_id' => $dropStop->id ?? null,
                'start_date' => Carbon::now()->subMonths(2),
                'end_date' => null, // Ongoing
                'booking_date' => Carbon::now()->subMonths(2),
                'booking_status' => ['pending', 'confirmed', 'active'][rand(0, 2)],
                'monthly_fare' => $monthlyFare,
                'status' => 'active',
                'notes' => 'Transport assignment for ' . $student->full_name,
            ]);
        }

        return $assignments;
    }

    private function createBills($tenantId, $assignments)
    {
        $bills = [];
        $currentYear = date('Y');
        $academicYear = $currentYear . '-' . ($currentYear + 1);

        // Create bills for active assignments
        $activeAssignments = collect($assignments)->where('booking_status', 'active')->take(30);

        foreach ($activeAssignments as $index => $assignment) {
            // Generate unique bill number per tenant
            $billNumber = TransportBill::generateBillNumber($tenantId);

            // If bill number already exists, add tenant prefix
            if (TransportBill::where('bill_number', $billNumber)->exists()) {
                $tenantPrefix = strtoupper(substr($tenantId, 0, 3));
                $billNumber = $tenantPrefix . '-' . $billNumber;
            }

            $bill = TransportBill::create([
                'tenant_id' => $tenantId,
                'student_id' => $assignment->student_id,
                'assignment_id' => $assignment->id,
                'bill_number' => $billNumber,
                'bill_date' => Carbon::now()->subMonths(1)->addDays($index % 30),
                'due_date' => Carbon::now()->subMonths(1)->addDays(($index % 30) + 30),
                'academic_year' => $academicYear,
                'term' => 'Monthly',
                'total_amount' => $assignment->monthly_fare,
                'discount_amount' => rand(0, 200),
                'tax_amount' => 0,
                'net_amount' => $assignment->monthly_fare - (rand(0, 200)),
                'paid_amount' => 0,
                'status' => ['sent', 'partial', 'paid'][rand(0, 2)],
                'notes' => 'Monthly transport fee bill',
            ]);

            // Create bill items
            TransportBillItem::create([
                'bill_id' => $bill->id,
                'description' => 'Monthly Transport Fee - ' . $assignment->route->name,
                'quantity' => 1,
                'unit_price' => $assignment->monthly_fare,
                'discount' => $bill->discount_amount,
                'amount' => $bill->net_amount,
            ]);

            // Update paid amount if status is paid or partial
            if ($bill->status === 'paid') {
                $bill->paid_amount = $bill->net_amount;
                $bill->save();
            } elseif ($bill->status === 'partial') {
                $bill->paid_amount = $bill->net_amount * 0.5;
                $bill->save();
            }

            $bills[] = $bill;
        }

        return $bills;
    }

    private function createPayments($tenantId, $bills)
    {
        $payments = [];
        $paymentMethods = ['cash', 'cheque', 'bank_transfer', 'online', 'card'];

        // Get first available user or set to null
        $adminUser = User::first();
        $collectedBy = $adminUser ? $adminUser->id : null;

        // Create payments for paid and partial bills
        foreach ($bills as $bill) {
            if ($bill->status === 'paid' || $bill->status === 'partial') {
                // Generate unique payment number per tenant
                $paymentNumber = TransportPayment::generatePaymentNumber($tenantId);

                // If payment number already exists, add tenant prefix
                if (TransportPayment::where('payment_number', $paymentNumber)->exists()) {
                    $tenantPrefix = strtoupper(substr($tenantId, 0, 3));
                    $paymentNumber = $tenantPrefix . '-' . $paymentNumber;
                }

                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

                $payments[] = TransportPayment::create([
                    'tenant_id' => $tenantId,
                    'student_id' => $bill->student_id,
                    'bill_id' => $bill->id,
                    'payment_number' => $paymentNumber,
                    'payment_date' => $bill->bill_date->copy()->addDays(rand(1, 15)),
                    'amount' => $bill->paid_amount,
                    'payment_method' => $paymentMethod,
                    'payment_type' => 'monthly_fare',
                    'transaction_id' => $paymentMethod !== 'cash' ? 'TXN-' . strtoupper(uniqid()) : null,
                    'reference_number' => $paymentMethod === 'cheque' ? 'CHQ-' . str_pad(rand(1000, 9999), 6, '0', STR_PAD_LEFT) : null,
                    'status' => 'success',
                    'notes' => 'Payment for ' . $bill->bill_number,
                    'collected_by' => $collectedBy,
                ]);
            }
        }

        return $payments;
    }
}

