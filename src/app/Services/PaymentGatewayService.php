<?php

namespace App\Services;

use App\Models\TenantSetting;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

class PaymentGatewayService
{
    protected $tenantId;
    protected $settings;

    public function __construct($tenantId)
    {
        $this->tenantId = $tenantId;
        $this->loadSettings();
    }

    /**
     * Load payment settings from database
     */
    protected function loadSettings()
    {
        $this->settings = TenantSetting::getAllForTenant($this->tenantId, 'payment');

        // Decrypt encrypted fields
        if (!empty($this->settings['razorpay_key_secret'])) {
            try {
                $this->settings['razorpay_key_secret'] = decrypt($this->settings['razorpay_key_secret']);
            } catch (\Exception $e) {
                $this->settings['razorpay_key_secret'] = null;
            }
        }

        if (!empty($this->settings['razorpay_webhook_secret'])) {
            try {
                $this->settings['razorpay_webhook_secret'] = decrypt($this->settings['razorpay_webhook_secret']);
            } catch (\Exception $e) {
                $this->settings['razorpay_webhook_secret'] = null;
            }
        }
    }

    /**
     * Check if online payments are enabled
     */
    public function isOnlinePaymentEnabled()
    {
        return $this->settings['enable_online_payments'] ?? false;
    }

    /**
     * Check if Razorpay is enabled
     */
    public function isRazorpayEnabled()
    {
        return $this->isOnlinePaymentEnabled() &&
               ($this->settings['payment_gateway'] ?? '') === 'razorpay' &&
               !empty($this->settings['razorpay_key_id']) &&
               !empty($this->settings['razorpay_key_secret']);
    }

    /**
     * Get Razorpay API instance
     */
    public function getRazorpayApi()
    {
        if (!$this->isRazorpayEnabled()) {
            throw new \Exception('Razorpay is not enabled or configured');
        }

        $key = $this->settings['razorpay_key_id'];
        $secret = $this->settings['razorpay_key_secret'];

        return new Api($key, $secret);
    }

    /**
     * Create Razorpay order
     */
    public function createRazorpayOrder($amount, $receiptId, $notes = [])
    {
        try {
            $api = $this->getRazorpayApi();

            $orderData = [
                'receipt' => $receiptId,
                'amount' => $amount * 100, // Convert to paise
                'currency' => $this->settings['currency_code'] ?? 'INR',
                'notes' => $notes
            ];

            $order = $api->order->create($orderData);

            return [
                'success' => true,
                'order_id' => $order->id,
                'amount' => $order->amount,
                'currency' => $order->currency,
            ];
        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Verify Razorpay payment signature
     */
    public function verifyRazorpaySignature($orderId, $paymentId, $signature)
    {
        try {
            $api = $this->getRazorpayApi();

            $attributes = [
                'razorpay_order_id' => $orderId,
                'razorpay_payment_id' => $paymentId,
                'razorpay_signature' => $signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            return true;
        } catch (\Exception $e) {
            Log::error('Razorpay signature verification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch payment details from Razorpay
     */
    public function fetchPaymentDetails($paymentId)
    {
        try {
            $api = $this->getRazorpayApi();
            $payment = $api->payment->fetch($paymentId);

            return [
                'success' => true,
                'payment' => $payment
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch payment details: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Process refund
     */
    public function processRefund($paymentId, $amount, $notes = [])
    {
        try {
            $api = $this->getRazorpayApi();

            $refund = $api->payment->fetch($paymentId)->refund([
                'amount' => $amount * 100, // Convert to paise
                'notes' => $notes
            ]);

            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $refund->amount / 100
            ];
        } catch (\Exception $e) {
            Log::error('Refund processing failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get Razorpay config for frontend
     */
    public function getRazorpayConfig()
    {
        return [
            'key' => $this->settings['razorpay_key_id'] ?? '',
            'currency' => $this->settings['currency_code'] ?? 'INR',
            'name' => 'School', // You can get this from general settings if needed
            'description' => 'Fee Payment',
            'theme_color' => '#2563eb'
        ];
    }

    /**
     * Check if test mode is enabled
     */
    public function isTestMode()
    {
        return $this->settings['razorpay_test_mode'] ?? false;
    }

    /**
     * Get offline payment methods
     */
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

    /**
     * Get all payment methods (online + offline)
     */
    public function getAllPaymentMethods()
    {
        $methods = $this->getOfflinePaymentMethods();

        if ($this->isRazorpayEnabled() && $this->isOnlinePaymentEnabled()) {
            $methods[] = 'razorpay';
        }

        return $methods;
    }
}

