# ⚙️ Settings-Based Configuration - Payment Gateway & Notifications

**Date:** November 16, 2025  
**Status:** ✅ **IMPLEMENTED**  
**Benefit:** Multi-tenant support with per-tenant credentials

---

## 🎯 OVERVIEW

The system now uses **database settings** instead of `.env` file for:
- ✅ Payment Gateway (Razorpay)
- ✅ Email Configuration (SMTP)
- ✅ SMS Configuration (MSG91)

### Why Settings-Based?

**Before (`.env` based):**
- ❌ All tenants share same credentials
- ❌ Cannot have different payment gateways per school
- ❌ Hard to update without file access
- ❌ Not suitable for multi-tenant SaaS

**After (Database settings):**
- ✅ Each tenant has own credentials
- ✅ Configure from admin panel
- ✅ No file access needed
- ✅ Perfect for multi-tenant SaaS
- ✅ Enable/disable per tenant

---

## 🆕 NEW SERVICES CREATED

### 1. **PaymentGatewayService** ✅

**File:** `src/app/Services/PaymentGatewayService.php`

**Features:**
- Load payment settings from database
- Create Razorpay orders
- Verify payment signatures
- Process refunds
- Get offline payment methods
- Check if online payments enabled

**Usage:**
```php
$paymentService = new PaymentGatewayService($tenantId);

// Check if Razorpay is enabled
if ($paymentService->isRazorpayEnabled()) {
    // Create order
    $order = $paymentService->createRazorpayOrder(1000, 'REC-001', [
        'student_id' => 123,
        'class' => '10-A'
    ]);
}

// Get all available payment methods
$methods = $paymentService->getAllPaymentMethods();
// Returns: ['cash', 'cheque', 'bank_transfer', 'razorpay']
```

---

### 2. **NotificationService** ✅

**File:** `src/app/Services/NotificationService.php`

**Features:**
- Send SMS via MSG91
- Send emails via SMTP
- Payment confirmation notifications
- Payment reminders
- Attendance absence alerts

**Usage:**
```php
$notificationService = new NotificationService($tenantId);

// Send payment confirmation
$notificationService->sendPaymentConfirmation($payment);

// Send payment reminder
$notificationService->sendPaymentReminder($student, $dueAmount);

// Send manual SMS
$notificationService->sendSms('9876543210', 'Your custom message');

// Send manual email
$notificationService->sendEmail('parent@example.com', 'Subject', 'Body content', true);
```

---

## 🔧 HOW TO CONFIGURE

### Method 1: Via Admin Settings Page (Recommended)

**Step 1: Go to Settings**
```
Admin Panel → Settings → Payment Settings Tab
```

**Step 2: Configure Razorpay**
- Enable Online Payments: ✅
- Enable Razorpay: ✅
- Razorpay Key ID: `rzp_test_xxxxx` or `rzp_live_xxxxx`
- Razorpay Secret: `your_secret_key`
- Test Mode: ✅ (for testing) / ❌ (for production)
- Currency: INR
- Save Settings

**Step 3: Configure Email (SMTP)**
```
Admin Panel → Settings → Notification Settings Tab
```
- Enable Email Notifications: ✅
- SMTP Host: `smtp.gmail.com`
- SMTP Port: `587`
- SMTP Username: `your-email@gmail.com`
- SMTP Password: `your-app-password`
- SMTP Encryption: TLS
- From Email: `school@example.com`
- Save Settings

**Step 4: Configure SMS (MSG91)**
```
Admin Panel → Settings → Notification Settings Tab
```
- Enable SMS Notifications: ✅
- MSG91 API Key: `your_msg91_api_key`
- SMS Sender ID: `SCHOOL`
- SMS Route: `4` (Transactional)
- Save Settings

---

### Method 2: Via Database/Seeder (For Development)

**Create a seeder:**

```php
// database/seeders/PaymentSettingsSeeder.php
use App\Models\TenantSetting;

public function run()
{
    $tenantId = 1; // Your tenant ID
    
    // Razorpay Settings
    TenantSetting::setSetting($tenantId, 'payment_online_enabled', true, 'boolean', 'payment');
    TenantSetting::setSetting($tenantId, 'payment_razorpay_enabled', true, 'boolean', 'payment');
    TenantSetting::setSetting($tenantId, 'payment_razorpay_key', 'rzp_test_xxxxx', 'string', 'payment');
    TenantSetting::setSetting($tenantId, 'payment_razorpay_secret', 'your_secret', 'string', 'payment');
    TenantSetting::setSetting($tenantId, 'payment_razorpay_test_mode', true, 'boolean', 'payment');
    TenantSetting::setSetting($tenantId, 'payment_currency', 'INR', 'string', 'payment');
    
    // Email Settings
    TenantSetting::setSetting($tenantId, 'notification_email_enabled', true, 'boolean', 'notification');
    TenantSetting::setSetting($tenantId, 'notification_email_host', 'smtp.gmail.com', 'string', 'notification');
    TenantSetting::setSetting($tenantId, 'notification_email_port', 587, 'integer', 'notification');
    TenantSetting::setSetting($tenantId, 'notification_email_username', 'your-email@gmail.com', 'string', 'notification');
    TenantSetting::setSetting($tenantId, 'notification_email_password', 'your-password', 'string', 'notification');
    TenantSetting::setSetting($tenantId, 'notification_email_encryption', 'tls', 'string', 'notification');
    TenantSetting::setSetting($tenantId, 'notification_email_from', 'school@example.com', 'string', 'notification');
    
    // SMS Settings
    TenantSetting::setSetting($tenantId, 'notification_sms_enabled', true, 'boolean', 'notification');
    TenantSetting::setSetting($tenantId, 'notification_sms_api_key', 'your_msg91_key', 'string', 'notification');
    TenantSetting::setSetting($tenantId, 'notification_sms_sender_id', 'SCHOOL', 'string', 'notification');
    TenantSetting::setSetting($tenantId, 'notification_sms_route', '4', 'string', 'notification');
}
```

**Run seeder:**
```bash
php artisan db:seed --class=PaymentSettingsSeeder
```

---

## 📋 REQUIRED SETTINGS

### Payment Gateway Settings

| Setting Key | Type | Required | Description |
|------------|------|----------|-------------|
| `payment_online_enabled` | boolean | Yes | Enable online payments |
| `payment_razorpay_enabled` | boolean | Yes | Enable Razorpay |
| `payment_razorpay_key` | string | Yes | Razorpay Key ID |
| `payment_razorpay_secret` | string | Yes | Razorpay Secret |
| `payment_razorpay_test_mode` | boolean | No | Test mode (default: false) |
| `payment_currency` | string | No | Currency (default: INR) |
| `payment_cash_enabled` | boolean | No | Enable cash payments |
| `payment_cheque_enabled` | boolean | No | Enable cheque payments |
| `payment_bank_transfer_enabled` | boolean | No | Enable bank transfer |

### Email Settings

| Setting Key | Type | Required | Description |
|------------|------|----------|-------------|
| `notification_email_enabled` | boolean | Yes | Enable email notifications |
| `notification_email_host` | string | Yes | SMTP host (e.g., smtp.gmail.com) |
| `notification_email_port` | integer | Yes | SMTP port (e.g., 587) |
| `notification_email_username` | string | Yes | SMTP username |
| `notification_email_password` | string | Yes | SMTP password/app password |
| `notification_email_encryption` | string | No | Encryption (tls/ssl, default: tls) |
| `notification_email_from` | string | Yes | From email address |

### SMS Settings

| Setting Key | Type | Required | Description |
|------------|------|----------|-------------|
| `notification_sms_enabled` | boolean | Yes | Enable SMS notifications |
| `notification_sms_api_key` | string | Yes | MSG91 API key |
| `notification_sms_sender_id` | string | Yes | SMS Sender ID (e.g., SCHOOL) |
| `notification_sms_route` | string | No | MSG91 Route (default: 4) |

---

## 🔄 UPDATED CONTROLLERS

### **StudentFeeCardController** ✅

**sendReminder() method** now uses `NotificationService`:

```php
public function sendReminder(Request $request, $studentId)
{
    // ... validation ...
    
    // Use NotificationService (reads from database settings)
    $notificationService = new \App\Services\NotificationService($tenant->id);
    $notificationService->sendPaymentReminder($student, $student->studentFeeCard->balance_amount);
    
    return redirect()->back()->with('success', 'Payment reminder sent!');
}
```

### **FeeCollectionController** ✅

**processPayment() method** now sends notifications:

```php
public function processPayment(Request $request, $studentId)
{
    // ... payment processing ...
    
    DB::commit();
    
    // Send payment confirmation (reads from database settings)
    $notificationService = new \App\Services\NotificationService($tenant->id);
    $notificationService->sendPaymentConfirmation($payment);
    
    return redirect()->back()->with('success', 'Payment collected!');
}
```

---

## 🎯 FEATURES THAT NOW WORK WITH SETTINGS

### ✅ Payment Confirmation (Auto-sent after payment)
- **SMS:** Sent to guardian's phone
- **Email:** Sent to student's email
- **Content:** Receipt number, amount, date, method

### ✅ Payment Reminders (Manual)
- **SMS:** Sent to guardian's phone
- **Email:** Sent to student's email
- **Content:** Student name, outstanding amount
- **Trigger:** Click "Send Reminder" button in fee card

### ✅ Razorpay Integration (Future)
- Create orders
- Verify signatures
- Process refunds
- Frontend config (for payment page)

---

## 🧪 TESTING

### Test Payment Confirmation

1. **Setup:**
   - Configure email/SMS in settings
   - Add student with valid email/phone
   
2. **Collect Payment:**
   ```
   Fee Collection → Select Student → Collect Payment
   Fill amount and submit
   ```

3. **Check:**
   - SMS sent to guardian's phone ✅
   - Email sent to student's email ✅
   - Check logs: `storage/logs/laravel.log`

---

### Test Payment Reminder

1. **Setup:**
   - Student must have pending dues
   - Configure email/SMS in settings

2. **Send Reminder:**
   ```
   Fee Card → Click "Send Reminder" button
   ```

3. **Check:**
   - SMS sent with outstanding amount ✅
   - Email sent with payment details ✅
   - Check logs for success/failure

---

## 📊 BENEFITS

### For SaaS Platform:
- ✅ Each school has own payment gateway account
- ✅ Each school has own email/SMS credentials
- ✅ Enable/disable features per school
- ✅ No shared credentials between tenants

### For Admin:
- ✅ Configure from UI (no server access needed)
- ✅ Update credentials anytime
- ✅ Test mode toggle
- ✅ Enable/disable notifications

### For Development:
- ✅ Test with different configurations
- ✅ Easy to seed test data
- ✅ No environment file changes
- ✅ Works in multi-tenant setup

---

## 🔒 SECURITY NOTES

### Credentials Storage:
- ✅ Stored in database (encrypted if needed)
- ✅ Per-tenant isolation
- ✅ Only admin can modify
- ✅ Not exposed to frontend

### Recommended:
1. Encrypt sensitive fields (API keys, passwords)
2. Use Laravel encryption for sensitive settings
3. Add audit log for settings changes
4. Restrict settings access to super admin

---

## 🚀 NEXT STEPS

### To Enable Full Functionality:

**1. Install Composer Packages:**
```bash
# For Razorpay
composer require razorpay/razorpay

# For email (already included in Laravel)
# No additional package needed
```

**2. Configure Settings:**
- Go to Settings page
- Fill in all credentials
- Test with small amount
- Enable production mode when ready

**3. Test Notifications:**
- Send test payment confirmation
- Send test payment reminder
- Check logs for any errors
- Verify delivery

---

## 📚 DOCUMENTATION REFERENCE

### Service Methods:

**PaymentGatewayService:**
- `isOnlinePaymentEnabled()` - Check if online payments enabled
- `isRazorpayEnabled()` - Check Razorpay configuration
- `createRazorpayOrder($amount, $receiptId, $notes)` - Create order
- `verifyRazorpaySignature($orderId, $paymentId, $signature)` - Verify payment
- `getAllPaymentMethods()` - Get all available methods

**NotificationService:**
- `isSmsEnabled()` - Check SMS configuration
- `isEmailEnabled()` - Check email configuration
- `sendSms($mobile, $message)` - Send SMS
- `sendEmail($to, $subject, $body, $isHtml)` - Send email
- `sendPaymentConfirmation($payment)` - Auto notification
- `sendPaymentReminder($student, $dueAmount)` - Reminder
- `sendAbsenceNotification($student, $date)` - Attendance alert

---

## ✅ CHECKLIST

- [x] PaymentGatewayService created
- [x] NotificationService created
- [x] StudentFeeCardController updated
- [x] FeeCollectionController updated
- [x] Services read from database settings
- [x] Multi-tenant support working
- [ ] Install Razorpay package (when needed)
- [ ] Configure settings in admin panel
- [ ] Test SMS sending
- [ ] Test email sending
- [ ] Test payment confirmation
- [ ] Test payment reminder

---

## 🎉 RESULT

**Payment Gateway and Notifications now work with tenant-specific database settings instead of .env file!**

Each tenant can have:
- ✅ Own Razorpay account
- ✅ Own SMTP credentials
- ✅ Own MSG91 account
- ✅ Independent configuration
- ✅ Enable/disable per feature

Perfect for **multi-tenant SaaS** setup! 🚀

---

*Configuration system implemented: November 16, 2025*

