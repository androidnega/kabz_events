<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\AdminSetting;

class PaystackService
{
    protected $secretKey;
    protected $publicKey;
    protected $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        // Get Paystack keys from admin settings
        $this->secretKey = AdminSetting::where('key', 'paystack_secret_key')->value('value');
        $this->publicKey = AdminSetting::where('key', 'paystack_public_key')->value('value');
        
        // Fallback to env if not set in settings
        if (empty($this->secretKey)) {
            $this->secretKey = config('services.paystack.secret_key');
        }
        if (empty($this->publicKey)) {
            $this->publicKey = config('services.paystack.public_key');
        }
    }

    /**
     * Get public key for frontend
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }

    /**
     * Initialize a payment transaction
     */
    public function initializePayment($email, $amount, $reference, $metadata = [])
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', [
                'email' => $email,
                'amount' => $amount * 100, // Convert to kobo
                'reference' => $reference,
                'metadata' => $metadata,
                'currency' => 'GHS',
                'callback_url' => route('paystack.callback'),
            ]);

            $data = $response->json();

            if ($response->successful() && $data['status']) {
                return [
                    'success' => true,
                    'authorization_url' => $data['data']['authorization_url'],
                    'access_code' => $data['data']['access_code'],
                    'reference' => $data['data']['reference'],
                ];
            }

            Log::error('Paystack initialization failed', ['response' => $data]);
            return [
                'success' => false,
                'message' => $data['message'] ?? 'Payment initialization failed',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'An error occurred. Please try again.',
            ];
        }
    }

    /**
     * Verify a payment transaction
     */
    public function verifyPayment($reference)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            $data = $response->json();

            if ($response->successful() && $data['status']) {
                return [
                    'success' => true,
                    'data' => $data['data'],
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'Verification failed',
            ];
        } catch (\Exception $e) {
            Log::error('Paystack verification exception', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Verification failed',
            ];
        }
    }

    /**
     * Generate a unique payment reference
     */
    public static function generateReference($prefix = 'KABZ')
    {
        return $prefix . '_' . time() . '_' . uniqid();
    }
}

