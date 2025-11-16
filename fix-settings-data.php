<?php
/**
 * Comprehensive Database Fix Script
 * 
 * Usage: php fix-settings-data.php
 * 
 * This script checks and fixes:
 * 1. weekend_days in attendance_settings (ensures it's properly JSON encoded)
 * 2. payment_methods in tenant_settings (ensures it's properly JSON encoded)
 * 3. current_address and permanent_address in students table
 * 4. current_address and permanent_address in teachers table
 * 5. gateway_response in payments table
 * 6. data field in tenants table
 * 7. All JSON type settings in tenant_settings
 */

require __DIR__ . '/src/vendor/autoload.php';

$app = require_once __DIR__ . '/src/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "  Comprehensive Database Data Fixer\n";
echo "========================================\n\n";

$totalFixed = 0;

// Fix 1: Attendance Settings - weekend_days
echo "[1/7] Checking attendance_settings.weekend_days...\n";
$attendanceSettings = DB::table('attendance_settings')->get();
$fixed = 0;

foreach ($attendanceSettings as $setting) {
    $weekendDays = $setting->weekend_days;
    
    if ($weekendDays !== null) {
        $decoded = json_decode($weekendDays, true);
        if (!is_array($decoded)) {
            $weekendDays = json_encode(['sunday']);
            DB::table('attendance_settings')
                ->where('id', $setting->id)
                ->update(['weekend_days' => $weekendDays]);
            echo "  ✓ Fixed tenant {$setting->tenant_id}\n";
            $fixed++;
        }
    }
}
echo "  Result: Fixed {$fixed} records\n\n";
$totalFixed += $fixed;

// Fix 2: Tenant Settings - payment_methods and other JSON settings
echo "[2/7] Checking tenant_settings with JSON type...\n";
$jsonSettings = DB::table('tenant_settings')
    ->where('setting_type', 'json')
    ->get();
$fixed = 0;

foreach ($jsonSettings as $setting) {
    $value = $setting->setting_value;
    
    if ($value !== null && $value !== '') {
        $decoded = json_decode($value, true);
        if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
            // Invalid JSON - try to fix based on setting key
            if ($setting->setting_key === 'payment_methods') {
                if (strpos($value, ',') !== false) {
                    $methods = array_map('trim', explode(',', $value));
                    $value = json_encode($methods);
                } else {
                    $value = json_encode(['cash', 'cheque', 'card', 'upi']);
                }
            } else {
                // For other JSON settings, wrap in array if it's a simple value
                $value = json_encode([$value]);
            }
            
            DB::table('tenant_settings')
                ->where('id', $setting->id)
                ->update(['setting_value' => $value]);
            echo "  ✓ Fixed tenant {$setting->tenant_id} - {$setting->setting_key}\n";
            $fixed++;
        }
    }
}
echo "  Result: Fixed {$fixed} records\n\n";
$totalFixed += $fixed;

// Fix 3: Students - address fields
echo "[3/7] Checking students.current_address and permanent_address...\n";
$students = DB::table('students')->get();
$fixed = 0;

foreach ($students as $student) {
    $needsUpdate = false;
    $updates = [];
    
    // Check current_address
    if ($student->current_address !== null) {
        $decoded = json_decode($student->current_address, true);
        if (!is_array($decoded)) {
            $updates['current_address'] = json_encode([
                'address' => $student->current_address,
                'city' => '',
                'state' => '',
                'pincode' => '',
                'country' => 'India'
            ]);
            $needsUpdate = true;
        }
    }
    
    // Check permanent_address
    if ($student->permanent_address !== null) {
        $decoded = json_decode($student->permanent_address, true);
        if (!is_array($decoded)) {
            $updates['permanent_address'] = json_encode([
                'address' => $student->permanent_address,
                'city' => '',
                'state' => '',
                'pincode' => '',
                'country' => 'India'
            ]);
            $needsUpdate = true;
        }
    }
    
    if ($needsUpdate) {
        DB::table('students')->where('id', $student->id)->update($updates);
        echo "  ✓ Fixed student {$student->id} - {$student->full_name}\n";
        $fixed++;
    }
}
echo "  Result: Fixed {$fixed} records\n\n";
$totalFixed += $fixed;

// Fix 4: Teachers - address fields
echo "[4/7] Checking teachers.current_address and permanent_address...\n";
$teachers = DB::table('teachers')->get();
$fixed = 0;

foreach ($teachers as $teacher) {
    $needsUpdate = false;
    $updates = [];
    
    // Check current_address
    if ($teacher->current_address !== null) {
        $decoded = json_decode($teacher->current_address, true);
        if (!is_array($decoded)) {
            $updates['current_address'] = json_encode([
                'address' => $teacher->current_address,
                'city' => '',
                'state' => '',
                'pincode' => '',
                'country' => 'India'
            ]);
            $needsUpdate = true;
        }
    }
    
    // Check permanent_address
    if ($teacher->permanent_address !== null) {
        $decoded = json_decode($teacher->permanent_address, true);
        if (!is_array($decoded)) {
            $updates['permanent_address'] = json_encode([
                'address' => $teacher->permanent_address,
                'city' => '',
                'state' => '',
                'pincode' => '',
                'country' => 'India'
            ]);
            $needsUpdate = true;
        }
    }
    
    if ($needsUpdate) {
        DB::table('teachers')->where('id', $teacher->id)->update($updates);
        echo "  ✓ Fixed teacher {$teacher->id} - {$teacher->full_name}\n";
        $fixed++;
    }
}
echo "  Result: Fixed {$fixed} records\n\n";
$totalFixed += $fixed;

// Fix 5: Payments - gateway_response
echo "[5/7] Checking payments.gateway_response...\n";
$payments = DB::table('payments')
    ->whereNotNull('gateway_response')
    ->get();
$fixed = 0;

foreach ($payments as $payment) {
    if ($payment->gateway_response !== null) {
        $decoded = json_decode($payment->gateway_response, true);
        if (!is_array($decoded)) {
            $updates['gateway_response'] = json_encode(['raw' => $payment->gateway_response]);
            DB::table('payments')->where('id', $payment->id)->update($updates);
            echo "  ✓ Fixed payment {$payment->id}\n";
            $fixed++;
        }
    }
}
echo "  Result: Fixed {$fixed} records\n\n";
$totalFixed += $fixed;

// Fix 6: Tenants - data field
echo "[6/7] Checking tenants.data...\n";
$tenants = DB::table('tenants')->get();
$fixed = 0;

foreach ($tenants as $tenant) {
    if ($tenant->data !== null) {
        $decoded = json_decode($tenant->data, true);
        if (!is_array($decoded)) {
            $updates['data'] = json_encode([]);
            DB::table('tenants')->where('id', $tenant->id)->update($updates);
            echo "  ✓ Fixed tenant {$tenant->id} - {$tenant->domain}\n";
            $fixed++;
        }
    }
}
echo "  Result: Fixed {$fixed} records\n\n";
$totalFixed += $fixed;

// Fix 7: Verify all models with array casts
echo "[7/7] Verifying data integrity...\n";
$issues = 0;

// Check for any NULL arrays that should be empty arrays
$tables = [
    'students' => ['medical_info'],
    'teachers' => [],
    'payments' => [],
];

foreach ($tables as $table => $fields) {
    foreach ($fields as $field) {
        $count = DB::table($table)
            ->whereNull($field)
            ->update([$field => json_encode([])]);
        if ($count > 0) {
            echo "  ✓ Set empty array for {$count} NULL {$table}.{$field}\n";
            $issues += $count;
        }
    }
}

echo "  Result: Fixed {$issues} NULL values\n\n";
$totalFixed += $issues;

echo "========================================\n";
echo "✅ All checks completed!\n";
echo "Total records fixed: {$totalFixed}\n";
echo "========================================\n\n";

if ($totalFixed > 0) {
    echo "Your database has been cleaned and optimized.\n";
} else {
    echo "No issues found. Your database is in good shape!\n";
}

echo "\nYou can now safely delete this file.\n";

