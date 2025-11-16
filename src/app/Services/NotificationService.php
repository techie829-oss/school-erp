<?php

namespace App\Services;

use App\Models\TenantSetting;
use App\Models\Student;
use App\Models\Payment;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    protected $tenantId;
    protected $settings;
    
    public function __construct($tenantId)
    {
        $this->tenantId = $tenantId;
        $this->loadSettings();
    }

    /**
     * Store notification attempt in ActivityLog table (non-blocking)
     */
    protected function logNotification($channel, $type, $recipient, $status, array $extra = [])
    {
        try {
            ActivityLog::log(
                'notification_' . $status,
                null,
                array_merge([
                    'channel' => $channel,          // sms / email
                    'type' => $type,                // payment_confirmation / payment_reminder / attendance / generic
                    'recipient' => $recipient,
                ], $extra),
                $this->tenantId,
                'system',
                null
            );
        } catch (\Throwable $e) {
            // Never block main flow due to logging failure
            Log::debug('Failed to store notification log: ' . $e->getMessage());
        }
    }
    
    /**
     * Load notification settings from database
     */
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
    
    /**
     * Check if SMS is enabled
     */
    public function isSmsEnabled()
    {
        return ($this->settings['sms_enabled'] ?? false) &&
               !empty($this->settings['msg91_auth_key']);
    }
    
    /**
     * Check if email is enabled
     */
    public function isEmailEnabled()
    {
        return ($this->settings['email_enabled'] ?? false) &&
               !empty($this->settings['mail_host']) &&
               !empty($this->settings['mail_username']);
    }
    
    /**
     * Send SMS using MSG91
     *
     * @param string $mobile
     * @param string $message
     * @param string|null $templateKey  // e.g. 'payment_confirmation', 'payment_reminder', 'fee_due', 'attendance'
     */
    public function sendSms($mobile, $message, $templateKey = null)
    {
        if (!$this->isSmsEnabled()) {
            Log::info('SMS not enabled, skipping');
            $this->logNotification('sms', $templateKey ?? 'generic', $mobile, 'skipped', [
                'reason' => 'sms_not_enabled_or_missing_config',
            ]);
            return ['success' => false, 'error' => 'SMS not enabled'];
        }
        
        try {
            $apiKey = $this->settings['msg91_auth_key'];
            $senderId = $this->settings['msg91_sender_id'] ?? 'SCHOOL';
            $route = $this->settings['msg91_route'] ?? '4';

            // Pick template id based on use-case key (if configured)
            $templateId = null;
            if ($templateKey) {
                $key = match ($templateKey) {
                    'payment_confirmation' => 'msg91_dlt_template_payment_confirmation',
                    'payment_reminder' => 'msg91_dlt_template_payment_reminder',
                    'fee_due' => 'msg91_dlt_template_fee_due',
                    'attendance' => 'msg91_dlt_template_attendance',
                    default => null,
                };
                if ($key && !empty($this->settings[$key])) {
                    $templateId = $this->settings[$key];
                }
            }
            
            $url = "https://api.msg91.com/api/v5/flow/";
            
            $payload = [
                'sender' => $senderId,
                'route' => $route,
                'country' => '91',
                'sms' => [
                    [
                        'message' => $message,
                        'to' => [$mobile]
                    ]
                ]
            ];
            
            if ($templateId) {
                $payload['template_id'] = $templateId;
            }
            
            $response = Http::withHeaders([
                'authkey' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post($url, $payload);
            
            if ($response->successful()) {
                Log::info("SMS sent successfully to {$mobile}");
                $this->logNotification('sms', $templateKey ?? 'generic', $mobile, 'success', [
                    'payload' => $payload,
                ]);
                return ['success' => true];
            }
            
            Log::error("SMS sending failed: " . $response->body());
            $this->logNotification('sms', $templateKey ?? 'generic', $mobile, 'failed', [
                'payload' => $payload,
                'error' => $response->body(),
            ]);
            return ['success' => false, 'error' => $response->body()];
            
        } catch (\Exception $e) {
            Log::error('SMS sending exception: ' . $e->getMessage());
            $this->logNotification('sms', $templateKey ?? 'generic', $mobile, 'failed', [
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send email
     */
    public function sendEmail($to, $subject, $body, $isHtml = true)
    {
        if (!$this->isEmailEnabled()) {
            Log::info('Email not enabled, skipping');
            $this->logNotification('email', 'generic', $to, 'skipped', [
                'reason' => 'email_not_enabled_or_missing_config',
                'subject' => $subject,
            ]);
            return ['success' => false, 'error' => 'Email not enabled'];
        }
        
        try {
            $mailer = $this->settings['mail_mailer'] ?? 'smtp';
            
            // Configure mail settings dynamically
            config([
                'mail.default' => $mailer,
                'mail.mailers.smtp.transport' => 'smtp',
                'mail.mailers.smtp.host' => $this->settings['mail_host'] ?? '',
                'mail.mailers.smtp.port' => $this->settings['mail_port'] ?? 587,
                'mail.mailers.smtp.username' => $this->settings['mail_username'] ?? '',
                'mail.mailers.smtp.password' => $this->settings['mail_password'] ?? '',
                'mail.mailers.smtp.encryption' => $this->settings['mail_encryption'] ?? 'tls',
                'mail.from.address' => $this->settings['mail_from_address'] ?? '',
                'mail.from.name' => $this->settings['mail_from_name'] ?? 'School',
            ]);
            
            Mail::send([], [], function ($message) use ($to, $subject, $body, $isHtml) {
                $message->to($to)
                    ->subject($subject);
                    
                if ($isHtml) {
                    $message->html($body);
                } else {
                    $message->text($body);
                }
            });
            
            Log::info("Email sent successfully to {$to}");
            $this->logNotification('email', 'generic', $to, 'success', [
                'subject' => $subject,
            ]);
            return ['success' => true];
            
        } catch (\Exception $e) {
            Log::error('Email sending exception: ' . $e->getMessage());
            $this->logNotification('email', 'generic', $to, 'failed', [
                'subject' => $subject,
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send payment confirmation notification
     */
    public function sendPaymentConfirmation(Payment $payment)
    {
        $student = $payment->student;
        $amount = number_format($payment->amount, 2);
        $paymentNumber = $payment->payment_number;
        $date = $payment->payment_date->format('d M Y');
        
        // SMS
        if ($this->isSmsEnabled() && $student->guardian_phone && ($this->settings['notify_payment_confirmation'] ?? true)) {
            $message = "Payment of Rs.{$amount} received successfully for {$student->full_name}. Receipt: {$paymentNumber}. Date: {$date}. Thank you!";
            // Use payment confirmation DLT template (if configured)
            $this->sendSms($student->guardian_phone, $message, 'payment_confirmation');
        }
        
        // Email
        if ($this->isEmailEnabled() && $student->email) {
            $subject = "Payment Confirmation - {$paymentNumber}";
            $body = "
                <h2>Payment Confirmation</h2>
                <p>Dear Parent/Guardian,</p>
                <p>We have received the payment for <strong>{$student->full_name}</strong>.</p>
                <table style='border-collapse: collapse; width: 100%; margin: 20px 0;'>
                    <tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Receipt Number:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>{$paymentNumber}</td></tr>
                    <tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Amount Paid:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>₹{$amount}</td></tr>
                    <tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Payment Date:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>{$date}</td></tr>
                    <tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Payment Method:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>" . ucfirst($payment->payment_method) . "</td></tr>
                </table>
                <p>Thank you for your payment!</p>
                <p>Best regards,<br>{$this->settings['mail_from_name']}</p>
            ";
            $this->sendEmail($student->email, $subject, $body);
        }
    }
    
    /**
     * Send payment reminder
     */
    public function sendPaymentReminder(Student $student, $dueAmount)
    {
        // Check if notification preference is enabled
        if (!($this->settings['notify_payment_reminder'] ?? true)) {
            Log::info('Payment reminder notification preference disabled');
            return;
        }
        
        $studentName = $student->full_name;
        $amount = number_format($dueAmount, 2);
        
        // SMS
        if ($this->isSmsEnabled() && $student->guardian_phone) {
            $message = "Dear Parent, Fee payment of Rs.{$amount} is pending for {$studentName}. Please clear the dues at the earliest. Thank you.";
            // Use payment reminder DLT template (if configured)
            $this->sendSms($student->guardian_phone, $message, 'payment_reminder');
        }
        
        // Email
        if ($this->isEmailEnabled() && $student->email) {
            $subject = "Fee Payment Reminder - {$studentName}";
            $body = "
                <h2>Fee Payment Reminder</h2>
                <p>Dear Parent/Guardian,</p>
                <p>This is a friendly reminder that a fee payment is pending for <strong>{$studentName}</strong>.</p>
                <p><strong>Outstanding Amount: ₹{$amount}</strong></p>
                <p>Please clear the pending dues at your earliest convenience.</p>
                <p>For any queries, please contact the school office.</p>
                <p>Best regards,<br>{$this->settings['mail_from_name']}</p>
            ";
            $this->sendEmail($student->email, $subject, $body);
        }
    }
    
    /**
     * Send attendance absence notification
     */
    public function sendAbsenceNotification(Student $student, $date)
    {
        // Check if notification preference is enabled
        if (!($this->settings['notify_attendance'] ?? false)) {
            Log::info('Attendance notification preference disabled');
            return;
        }
        
        $studentName = $student->full_name;
        $formattedDate = $date->format('d M Y');
        
        // SMS
        if ($this->isSmsEnabled() && $student->guardian_phone) {
            $message = "Your child {$studentName} was marked absent on {$formattedDate}. Please contact school if this is incorrect.";
            // Use attendance DLT template (if configured)
            $this->sendSms($student->guardian_phone, $message, 'attendance');
        }
        
        // Email
        if ($this->isEmailEnabled() && $student->email) {
            $subject = "Attendance Alert - {$studentName}";
            $body = "
                <h2>Attendance Notification</h2>
                <p>Dear Parent/Guardian,</p>
                <p>Your child <strong>{$studentName}</strong> was marked <strong>absent</strong> on <strong>{$formattedDate}</strong>.</p>
                <p>If this is incorrect, please contact the school office immediately.</p>
                <p>Best regards,<br>{$this->settings['mail_from_name']}</p>
            ";
            $this->sendEmail($student->email, $subject, $body);
        }
    }
}

