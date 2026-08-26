<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Twilio\Http\GuzzleClient;
use Twilio\Rest\Client;
use GuzzleHttp\Client as GuzzleHttp;

class TwilioOTPService
{
    protected Client $twilioClient;
    protected string $fromPhone;

    public function __construct()
{
    $guzzle = new GuzzleHttp();

    $this->twilioClient = new Client(
        config('twilio.sid'),
        config('twilio.token'),
        null,
        null,
        new GuzzleClient($guzzle)
    );

    $this->fromPhone = config('twilio.from');
}

    /**
     * Generate OTP, cache it, and send via SMS.
     * Used for login (AuthController).
     */
    public function sendOTP(string $phone): int
    {
        $otp = random_int(100000, 999999);

        Cache::put("login_otp_{$phone}", $otp, now()->addMinutes(5));

        $this->sendSMS($phone, "Your Nilbox login OTP is: {$otp}. Valid for 5 minutes.");

        return $otp;
    }

    /**
     * Send a pre-generated OTP via SMS.
     * Used for forgot password (ForgotPasswordController).
     * OTP is already stored in DB via VerificationCodeRepository.
     */
    public function sendForgotPasswordOTP(string $phone, int $otp): void
    {
        $this->sendSMS($phone, "Your Nilbox OTP is: {$otp}. Valid for 5 minutes.");
    }

    /**
     * Verify a cached OTP (used for login flow).
     */
    public function verifyOTP(string $phone, string $otp): bool
    {
        $cached = Cache::get("login_otp_{$phone}");

        if ($cached && (string) $cached === (string) $otp) {
            Cache::forget("login_otp_{$phone}");
            return true;
        }

        return false;
    }

    /**
     * Normalize a phone number to E.164 format for Twilio.
     * Assumes Indian numbers if no country code is present.
     */
    public function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
            return '+' . $phone;
        }

        if (strlen($phone) === 10) {
            return '+91' . $phone;
        }

        return '+' . $phone;
    }

    private function sendSMS(string $phone, string $body): void
    {
        $this->twilioClient->messages->create($phone, [
            'from' => $this->fromPhone,
            'body' => $body,
        ]);
    }
}