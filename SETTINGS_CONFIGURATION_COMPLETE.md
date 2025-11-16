# Settings Configuration Complete ✅

## Overview
All configuration options for Payment Gateway, SMS, and Email are now fully implemented and available in the Settings page.

---

## Configuration Available

### 1. Payment Settings Tab ✅

**Location:** Settings → Payment Settings

**Options Available:**
- ✅ **Enable Online Payments** (Checkbox to enable/disable online payment gateway)
- ✅ **Payment Gateway Selection** (Razorpay, Stripe, PayU, PhonePe)
- ✅ **Razorpay Configuration:**
  - Razorpay Key ID
  - Razorpay Key Secret (encrypted)
  - Webhook Secret (encrypted)
  - Test Mode Toggle
- ✅ **Offline Payment Methods** (Cash, Cheque, Card, UPI, Net Banking, Demand Draft)
- ✅ **Auto-generate Receipts** (Enable/Disable)
- ✅ **Payment Reminder Days**
- ✅ **Late Fee Percentage**
- ✅ **Receipt Settings:**
  - Receipt Number Prefix
  - Invoice Number Prefix
  - Currency Code (INR, USD, EUR, GBP)
  - Tax/GST Percentage
- ✅ **Email Receipts** (Enable/Disable)
- ✅ **SMS Payment Confirmation** (Enable/Disable)

---

### 2. Notification Settings Tab ✅ (NEW)

**Location:** Settings → Notifications (SMS/Email)

#### Email Configuration
- ✅ **Enable Email Notifications** (Master toggle)
- ✅ **SMTP Configuration:**
  - Mail Driver (SMTP, Sendmail, Mailgun, Amazon SES)
  - SMTP Host
  - SMTP Port
  - Encryption (TLS, SSL, None)
  - SMTP Username
  - SMTP Password (encrypted)
  - From Email Address
  - From Name
- ✅ **Test Email Button** (Send test email to verify configuration)

#### SMS Configuration
- ✅ **Enable SMS Notifications** (Master toggle)
- ✅ **SMS Provider Selection** (MSG91, Twilio, Textlocal)
- ✅ **MSG91 Configuration:**
  - MSG91 Auth Key (encrypted)
  - MSG91 Sender ID (6 chars max)
  - MSG91 Route (Transactional/Promotional)
  - DLT Template ID (for Indian regulations)
- ✅ **Test SMS Button** (Send test SMS to verify configuration)

#### Notification Preferences
- ✅ **Payment Confirmation** (Enable/Disable)
- ✅ **Payment Reminder** (Enable/Disable)
- ✅ **Fee Due Alert** (Enable/Disable)
- ✅ **Attendance Alert** (Enable/Disable)

---

## Technical Implementation

### Services Created

#### 1. NotificationService.php
**Location:** `src/app/Services/NotificationService.php`

**Features:**
- Loads notification settings from database (notifications category)
- Automatically decrypts encrypted credentials (mail_password, msg91_auth_key)
- Checks if SMS/Email is enabled before sending
- Respects notification preferences (payment confirmation, reminders, attendance)
- Sends payment confirmations
- Sends payment reminders
- Sends attendance absence notifications
- Dynamic SMTP configuration per tenant
- MSG91 SMS integration with DLT template support

**Methods:**
- `sendPaymentConfirmation(Payment $payment)`
- `sendPaymentReminder(Student $student, $dueAmount)`
- `sendAbsenceNotification(Student $student, $date)`
- `sendEmail($to, $subject, $body, $isHtml = true)`
- `sendSms($mobile, $message)`

#### 2. PaymentGatewayService.php
**Location:** `src/app/Services/PaymentGatewayService.php`

**Features:**
- Loads payment settings from database (payment category)
- Automatically decrypts encrypted credentials (razorpay_key_secret, razorpay_webhook_secret)
- Checks if online payments are enabled
- Checks if Razorpay is enabled and configured
- Creates Razorpay orders
- Verifies Razorpay payment signatures
- Processes refunds
- Gets payment methods (online + offline)

**Methods:**
- `isOnlinePaymentEnabled()`
- `isRazorpayEnabled()`
- `getRazorpayApi()`
- `createRazorpayOrder($amount, $receiptId, $notes = [])`
- `verifyRazorpaySignature($orderId, $paymentId, $signature)`
- `fetchPaymentDetails($paymentId)`
- `processRefund($paymentId, $amount, $notes = [])`
- `getRazorpayConfig()` (for frontend)
- `getAllPaymentMethods()`

---

## Controller Updates

### SettingsController.php
**New Method:** `updateNotifications(Request $request)`

**Handles:**
- Email configuration (SMTP settings)
- SMS configuration (MSG91 settings)
- Notification preferences
- Encrypts sensitive fields (passwords, API keys) before storage
- Validates all input fields

---

## View Files

### 1. notifications.blade.php (NEW)
**Location:** `src/resources/views/tenant/admin/settings/notifications.blade.php`

**Features:**
- Clean, modern UI with toggle sections
- Email configuration form with SMTP settings
- SMS configuration form with MSG91 settings
- Test email/SMS buttons
- Notification preferences checkboxes
- Help text and examples for each field
- Security notes about encrypted storage

### 2. index.blade.php (UPDATED)
**Location:** `src/resources/views/tenant/admin/settings/index.blade.php`

**Changes:**
- Added new "Notifications (SMS/Email)" tab
- Includes notification settings view

---

## Routes

### web.php (UPDATED)
**New Route:**
```php
Route::post('/admin/settings/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.update.notifications');
```

---

## How to Configure

### Step 1: Configure Payment Gateway (Razorpay)

1. Go to **Settings → Payment Settings**
2. Check **"Enable Online Payments"**
3. Select **"Razorpay"** from Payment Gateway dropdown
4. Get your API keys from [Razorpay Dashboard](https://dashboard.razorpay.com/)
5. Enter:
   - **Razorpay Key ID**: `rzp_test_xxxxxxxxxx` (for test) or `rzp_live_xxxxxxxxxx` (for production)
   - **Razorpay Key Secret**: Your secret key
   - Check **"Test Mode"** if using test keys
6. Click **"Save Payment Settings"**

### Step 2: Configure Email (SMTP)

1. Go to **Settings → Notifications (SMS/Email)**
2. Check **"Enable Email Notifications"**
3. Select **"SMTP"** as Mail Driver
4. Enter your SMTP settings:

**For Gmail:**
- Host: `smtp.gmail.com`
- Port: `587`
- Encryption: `TLS`
- Username: `your-email@gmail.com`
- Password: Use [App Password](https://myaccount.google.com/apppasswords), NOT your regular password
- From Address: `your-email@gmail.com`
- From Name: Your school name

**For Other Providers:**
- Use their respective SMTP settings

5. Click **"Send Test Email"** to verify configuration
6. Click **"Save Notification Settings"**

### Step 3: Configure SMS (MSG91)

1. Go to **Settings → Notifications (SMS/Email)**
2. Check **"Enable SMS Notifications"**
3. Select **"MSG91"** as SMS Provider
4. Sign up at [msg91.com](https://msg91.com/) to get your Auth Key
5. Enter:
   - **MSG91 Auth Key**: Your API key
   - **MSG91 Sender ID**: 6-character uppercase ID (e.g., SCHOOL)
   - **MSG91 Route**: Choose Transactional (Route 4) or Promotional (Route 1)
   - **DLT Template ID**: Required for Indian regulations
6. Enter a phone number and click **"Send Test SMS"**
7. Click **"Save Notification Settings"**

### Step 4: Configure Notification Preferences

1. In the same **Notifications** tab, scroll to **"Notification Preferences"**
2. Check/uncheck options based on your needs:
   - ✅ Payment Confirmation (Recommended)
   - ✅ Payment Reminder (Recommended)
   - ✅ Fee Due Alert
   - ⬜ Attendance Alert (Optional)
3. Click **"Save Notification Settings"**

---

## Security Features

### Encryption
All sensitive data is encrypted before storage:
- ✅ SMTP Password
- ✅ MSG91 Auth Key
- ✅ Razorpay Key Secret
- ✅ Razorpay Webhook Secret

### Tenant Isolation
- ✅ Each tenant has their own settings
- ✅ Settings are loaded per tenant
- ✅ No cross-tenant data leakage

### Validation
- ✅ All inputs are validated before storage
- ✅ Email addresses validated
- ✅ Port numbers validated (1-65535)
- ✅ Sender ID limited to 6 characters

---

## Usage in Code

### Sending Payment Confirmation
```php
use App\Services\NotificationService;

$notificationService = new NotificationService($tenantId);
$notificationService->sendPaymentConfirmation($payment);
```

### Sending Payment Reminder
```php
use App\Services\NotificationService;

$notificationService = new NotificationService($tenantId);
$notificationService->sendPaymentReminder($student, $balanceAmount);
```

### Creating Razorpay Order
```php
use App\Services\PaymentGatewayService;

$paymentGateway = new PaymentGatewayService($tenantId);

if ($paymentGateway->isRazorpayEnabled()) {
    $result = $paymentGateway->createRazorpayOrder($amount, $receiptId, $notes);
    if ($result['success']) {
        $orderId = $result['order_id'];
        // Process order
    }
}
```

### Checking Payment Methods
```php
use App\Services\PaymentGatewayService;

$paymentGateway = new PaymentGatewayService($tenantId);
$methods = $paymentGateway->getAllPaymentMethods();
// Returns: ['cash', 'cheque', 'upi', 'razorpay'] etc.
```

---

## Controllers Using These Services

### 1. FeeCollectionController.php
**Uses:** NotificationService
- Sends payment confirmation after successful payment
- Located in `processPayment()` method

### 2. StudentFeeCardController.php
**Uses:** NotificationService
- Sends payment reminders
- Located in `sendReminder()` method

---

## Database Storage

### TenantSetting Model
All settings are stored in `tenant_settings` table with:
- `tenant_id`: Links to tenant
- `group`: Category (payment, notifications)
- `key`: Setting name
- `value`: Setting value (encrypted for sensitive data)
- `type`: Data type (string, boolean, json, integer)

### Settings Groups
1. **payment**: Payment gateway and fee collection settings
2. **notifications**: Email and SMS notification settings
3. **general**: General tenant information
4. **features**: Module enable/disable flags
5. **academic**: Academic year and session settings
6. **attendance**: Attendance system settings

---

## Testing

### Test Email
1. Configure SMTP settings
2. Click "Send Test Email" button
3. Check your inbox for test email

### Test SMS
1. Configure MSG91 settings
2. Enter a 10-digit phone number
3. Click "Send Test SMS" button
4. Check your phone for test message

### Test Payment Gateway
1. Configure Razorpay with test keys
2. Enable test mode
3. Try making a payment from fee collection
4. Use [Razorpay test cards](https://razorpay.com/docs/payments/payments/test-card-details/)

---

## Troubleshooting

### Email Not Sending
- ✅ Check SMTP host and port
- ✅ Verify username and password
- ✅ Use App Password for Gmail (not regular password)
- ✅ Check encryption (TLS for port 587, SSL for port 465)
- ✅ Check Laravel logs in `storage/logs/laravel.log`

### SMS Not Sending
- ✅ Verify MSG91 Auth Key
- ✅ Check sender ID (max 6 chars, uppercase)
- ✅ Ensure DLT template is approved (for India)
- ✅ Verify phone number format (10 digits without country code)
- ✅ Check MSG91 account balance
- ✅ Check Laravel logs in `storage/logs/laravel.log`

### Payment Gateway Not Working
- ✅ Verify Razorpay keys (test or live)
- ✅ Check test mode setting matches keys used
- ✅ Ensure "Enable Online Payments" is checked
- ✅ Verify webhook URL is configured in Razorpay dashboard
- ✅ Check Laravel logs for errors

---

## Important Notes

### Gmail SMTP
If using Gmail, you MUST:
1. Enable 2-Factor Authentication on your Google account
2. Generate an [App Password](https://myaccount.google.com/apppasswords)
3. Use the App Password, NOT your regular Gmail password

### MSG91 DLT Registration (India)
For Indian users, DLT (Distributed Ledger Technology) registration is mandatory:
1. Register your sender ID with MSG91
2. Create and get approval for SMS templates
3. Use approved template IDs in the settings

### Razorpay Test vs Live
- **Test Mode**: Use `rzp_test_` keys, safe to test without real money
- **Live Mode**: Use `rzp_live_` keys, processes real transactions

---

## Summary

✅ **All configuration options are available**
✅ **Settings are stored in database per tenant**
✅ **Sensitive data is encrypted**
✅ **Services are ready to use**
✅ **Controllers are integrated**
✅ **UI is user-friendly with help text**
✅ **Test buttons available for verification**

**Next Steps:**
1. Configure Payment Gateway settings
2. Configure Email/SMTP settings
3. Configure SMS/MSG91 settings
4. Test using the test buttons
5. Enable notification preferences
6. Start using payment collection and notifications!

---

## Files Modified/Created

### New Files
1. `src/resources/views/tenant/admin/settings/notifications.blade.php`

### Modified Files
1. `src/resources/views/tenant/admin/settings/index.blade.php`
2. `src/app/Http/Controllers/Tenant/Admin/SettingsController.php`
3. `src/routes/web.php`
4. `src/app/Services/NotificationService.php`
5. `src/app/Services/PaymentGatewayService.php`
6. `src/app/Http/Controllers/Tenant/Admin/FeeCollectionController.php`
7. `src/app/Http/Controllers/Tenant/Admin/StudentFeeCardController.php`

---

**Configuration is COMPLETE and READY TO USE! 🎉**

