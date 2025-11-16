# Data Format Fixes Applied

## Overview
Fixed all data format inconsistencies across the entire codebase to ensure proper handling of JSON/array data types.

---

## Issues Fixed

### 1. **Blade Template Fixes**

#### File: `attendance.blade.php`
**Issue:** `in_array()` error when `weekend_days` is stored as JSON string  
**Fix Applied:**
```php
$weekendDays = old('weekend_days', $attendanceSettings->weekend_days ?? ['sunday']);
// Handle if weekend_days is stored as JSON string
if (is_string($weekendDays)) {
    $weekendDays = json_decode($weekendDays, true) ?? ['sunday'];
}
```

#### File: `payment.blade.php`
**Issue:** `in_array()` error when `payment_methods` is stored as JSON string  
**Fix Applied:**
```php
$paymentMethods = old('payment_methods', $paymentSettings['payment_methods'] ?? ['cash', 'cheque', 'card', 'upi']);
// Handle if payment_methods is stored as JSON string
if (is_string($paymentMethods)) {
    $paymentMethods = json_decode($paymentMethods, true) ?? ['cash', 'cheque', 'card', 'upi'];
}
```

---

### 2. **Service Layer Fixes**

#### File: `PaymentGatewayService.php`
**Already Fixed:** Properly handles JSON string for `payment_methods`
```php
public function getOfflinePaymentMethods()
{
    $paymentMethods = $this->settings['payment_methods'] ?? [];
    
    // If stored as JSON string, decode it
    if (is_string($paymentMethods)) {
        $paymentMethods = json_decode($paymentMethods, true) ?? [];
    }
    
    return array_filter($paymentMethods, function($method) {
        return in_array($method, ['cash', 'cheque', 'card', 'upi', 'net_banking', 'demand_draft']);
    });
}
```

#### File: `NotificationService.php`
**Already Fixed:** Properly loads and decrypts notification settings
```php
protected function loadSettings()
{
    $this->settings = TenantSetting::getAllForTenant($this->tenantId, 'notifications');
    
    // Decrypt encrypted fields
    if (!empty($this->settings['mail_password'])) {
        try {
            $this->settings['mail_password'] = decrypt($this->settings['mail_password']);
        } catch (\Exception $e) {
            $this->settings['mail_password'] = null;
        }
    }
    
    if (!empty($this->settings['msg91_auth_key'])) {
        try {
            $this->settings['msg91_auth_key'] = decrypt($this->settings['msg91_auth_key']);
        } catch (\Exception $e) {
            $this->settings['msg91_auth_key'] = null;
        }
    }
}
```

---

### 3. **Model Verification**

All models with array/JSON casts verified and working correctly:

✅ **Student Model**
- `current_address` → array
- `permanent_address` → array  
- `medical_info` → array

✅ **Teacher Model**
- `current_address` → array
- `permanent_address` → array

✅ **Payment Model**
- `gateway_response` → array

✅ **Tenant Model**
- `data` → array

✅ **AttendanceSettings Model**
- `weekend_days` → array

✅ **TenantSetting Model**
- Properly handles JSON type with `getValue()` method

---

## Comprehensive Database Fixer Script

Created: `fix-settings-data.php`

### What It Checks & Fixes:

1. **attendance_settings.weekend_days**
   - Ensures proper JSON array format
   - Sets default ['sunday'] if invalid

2. **tenant_settings (JSON type)**
   - All settings with type='json'
   - Special handling for payment_methods
   - Fixes malformed JSON

3. **students table**
   - current_address field
   - permanent_address field
   - Converts old format to proper JSON

4. **teachers table**
   - current_address field
   - permanent_address field
   - Converts old format to proper JSON

5. **payments table**
   - gateway_response field
   - Ensures proper JSON format

6. **tenants table**
   - data field
   - Ensures proper JSON object format

7. **NULL value cleanup**
   - Sets empty arrays for NULL JSON fields
   - Ensures consistency

### How to Run:

```bash
cd /Users/rohitk/react/lara/school-erp
php fix-settings-data.php
```

### Output Example:
```
========================================
  Comprehensive Database Data Fixer
========================================

[1/7] Checking attendance_settings.weekend_days...
  ✓ Fixed tenant 1
  Result: Fixed 1 records

[2/7] Checking tenant_settings with JSON type...
  ✓ Fixed tenant 1 - payment_methods
  Result: Fixed 1 records

[3/7] Checking students.current_address and permanent_address...
  Result: Fixed 0 records

[4/7] Checking teachers.current_address and permanent_address...
  Result: Fixed 0 records

[5/7] Checking payments.gateway_response...
  Result: Fixed 0 records

[6/7] Checking tenants.data...
  Result: Fixed 0 records

[7/7] Verifying data integrity...
  Result: Fixed 0 NULL values

========================================
✅ All checks completed!
Total records fixed: 2
========================================

Your database has been cleaned and optimized.

You can now safely delete this file.
```

---

## Prevention Measures

### 1. **Blade Template Pattern**
When using `in_array()` with database data that could be JSON:

```php
@php
    $arrayData = old('field', $data ?? []);
    // Always check if it's a string and decode
    if (is_string($arrayData)) {
        $arrayData = json_decode($arrayData, true) ?? [];
    }
@endphp
```

### 2. **Service Layer Pattern**
Always check data format when retrieving from settings:

```php
$data = $this->settings['field'] ?? [];

if (is_string($data)) {
    $data = json_decode($data, true) ?? [];
}
```

### 3. **Model Casts**
Always use appropriate casts in models:

```php
protected $casts = [
    'json_field' => 'array',
    'boolean_field' => 'boolean',
    'decimal_field' => 'decimal:2',
];
```

---

## Files Modified

### Views (2 files)
1. ✅ `src/resources/views/tenant/admin/settings/attendance.blade.php`
2. ✅ `src/resources/views/tenant/admin/settings/payment.blade.php`

### Services (Already Safe)
1. ✅ `src/app/Services/PaymentGatewayService.php`
2. ✅ `src/app/Services/NotificationService.php`

### Models (Already Safe)
1. ✅ `src/app/Models/TenantSetting.php` - Has proper getValue() method
2. ✅ `src/app/Models/AttendanceSettings.php` - Has array cast
3. ✅ All other models with array casts - Working correctly

### Utility Scripts (New)
1. ✅ `fix-settings-data.php` - Comprehensive database fixer

---

## Testing Checklist

### Before Running Fix Script:
- [ ] Backup your database
- [ ] Note current tenant count
- [ ] Check error logs for specific issues

### After Running Fix Script:
- [x] All settings pages load without errors
- [x] SVPS tenant settings work
- [x] LPU tenant settings work
- [x] Payment methods display correctly
- [x] Weekend days display correctly
- [x] Student addresses work
- [x] Teacher addresses work
- [x] No `in_array()` errors in logs

### Manual Verification:
1. Open each tenant's Settings page
2. Check all tabs (General, Features, Academic, Attendance, Payment, Notifications)
3. Verify weekend days checkboxes work
4. Verify payment methods checkboxes work
5. Test saving settings
6. Check student/teacher edit forms

---

## Summary

**Problem:** Some tenants had data stored as plain strings or malformed JSON, causing `in_array()` errors when the code expected arrays.

**Solution:** 
1. ✅ Added runtime checks in Blade templates to handle both formats
2. ✅ Created comprehensive database fixer script
3. ✅ Verified all models have proper casts
4. ✅ Ensured services handle data correctly

**Result:** 
- ✅ All tenants work regardless of data format
- ✅ Database can be cleaned with provided script
- ✅ Future-proof against similar issues
- ✅ No breaking changes to existing functionality

---

## Support

If you encounter any issues after applying these fixes:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Run the fix script: `php fix-settings-data.php`
3. Verify model casts are in place
4. Check blade template has the string-to-array conversion

**All fixes are backward compatible!** Old data format still works, new data format works better.

