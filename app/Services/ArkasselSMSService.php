<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArkasselSMSService
{
    /**
     * Send SMS via Arkassel API
     *
     * @param string $to Phone number (format: +233XXXXXXXXX or 0XXXXXXXXX)
     * @param string $message Message content
     * @return bool Success status
     */
    public static function send(string $to, string $message): bool
    {
        // Get configuration from SettingsService
        $apiKey = SettingsService::get('sms_api_key');
        $sender = SettingsService::get('sms_sender_id', 'KABZS');
        $enabled = SettingsService::get('sms_enabled', false);

        // Check if SMS is enabled
        if (!$enabled) {
            Log::info('SMS disabled - message not sent', ['to' => $to]);
            return false;
        }

        // Check if API key exists
        if (empty($apiKey)) {
            Log::error('Arkassel SMS API key not configured');
            return false;
        }

        // Format phone number for Ghana
        $to = self::formatGhanaNumber($to);

        try {
            // Send SMS via Arkassel API
            $response = Http::post('https://sms.arkassel.com/api/v1/send', [
                'key' => $apiKey,
                'to' => $to,
                'sender' => $sender,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully', ['to' => $to]);
                return true;
            }

            Log::error('Arkassel SMS failed', [
                'to' => $to,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Arkassel SMS exception', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Format phone number for Ghana (+233 format)
     */
    private static function formatGhanaNumber(string $number): string
    {
        // Remove any whitespace
        $number = preg_replace('/\s+/', '', $number);

        // If starts with 0, replace with +233
        if (substr($number, 0, 1) === '0') {
            return '+233' . substr($number, 1);
        }

        // If starts with 233, add +
        if (substr($number, 0, 3) === '233') {
            return '+' . $number;
        }

        // If already has +233, return as is
        if (substr($number, 0, 4) === '+233') {
            return $number;
        }

        // Otherwise, assume it's local number, add +233
        return '+233' . $number;
    }

    /**
     * Send bulk SMS to multiple numbers
     */
    public static function sendBulk(array $recipients, string $message): array
    {
        $results = [];

        foreach ($recipients as $to) {
            $results[$to] = self::send($to, $message);
        }

        return $results;
    }
}

