<?php

namespace App\Services;

use App\Models\Otp;
use Carbon\Carbon;

class OTPService
{
    /**
     * Generate and send OTP code
     *
     * @param string $phone Phone number
     * @param string $type OTP type (registration, password_reset, verification)
     * @return string Generated OTP code
     */
    public static function generate(string $phone, string $type = 'registration'): string
    {
        // Generate 6-digit code
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Create OTP record
        Otp::create([
            'phone_number' => $phone,
            'code' => $code,
            'type' => $type,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Prepare message based on type
        $message = match ($type) {
            'registration' => "Welcome to KABZS EVENT! Your verification code is: {$code}. Valid for 10 minutes. 🇬🇭",
            'password_reset' => "Your KABZS EVENT password reset code is: {$code}. Valid for 10 minutes.",
            'verification' => "Your KABZS EVENT verification code is: {$code}. Valid for 10 minutes.",
            default => "Your KABZS EVENT code is: {$code}. Valid for 10 minutes.",
        };

        // Send SMS
        SMSService::send($phone, $message);

        return $code;
    }

    /**
     * Verify OTP code
     *
     * @param string $phone Phone number
     * @param string $code OTP code to verify
     * @return bool Verification success
     */
    public static function verify(string $phone, string $code): bool
    {
        $otp = Otp::where('phone_number', $phone)
            ->where('code', $code)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($otp) {
            $otp->update(['used' => true]);
            return true;
        }

        return false;
    }

    /**
     * Clear expired OTPs (cleanup job)
     */
    public static function clearExpired(): int
    {
        return Otp::where('expires_at', '<', Carbon::now())->delete();
    }

    /**
     * Resend OTP (invalidates previous codes)
     */
    public static function resend(string $phone, string $type = 'registration'): string
    {
        // Invalidate previous OTPs for this phone and type
        Otp::where('phone_number', $phone)
            ->where('type', $type)
            ->where('used', false)
            ->update(['used' => true]);

        // Generate new OTP
        return self::generate($phone, $type);
    }
}

