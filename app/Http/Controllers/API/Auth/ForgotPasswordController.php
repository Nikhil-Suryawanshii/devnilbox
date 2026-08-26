<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\OTPRequest;
use App\Http\Requests\OTPVerifyRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Mail\OtpMail;
use App\Repositories\UserRepository;
use App\Repositories\VerificationCodeRepository;
use App\Services\TwilioOTPService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function __construct(protected TwilioOTPService $otpService) {}

    /**
     * Send OTP to user's phone via Twilio SMS
     * and to user's email via Hostinger SMTP — both with the same OTP.
     */
    public function resendOTP(OTPRequest $request)
    {
        $user = UserRepository::findByPhone($request->phone);

        if (! $user) {
            return $this->json(__('Sorry! No user found with this email/phone.'), [], 422);
        }

        if (! $user->is_active) {
            return $this->json('Sorry, your account is not active', [], 422);
        }

        $verificationCode = VerificationCodeRepository::findOrCreateByContact($user->phone);
        $otp      = $verificationCode->otp;
        $phoneCode = $request->phone_code ?? $user->phone_code;
        $phone    = $this->otpService->formatPhone($user->phone);

        // Send OTP via SMS (Twilio)
        try {
            $this->otpService->sendForgotPasswordOTP($phone, $otp);
        } catch (\Throwable) {
            return $this->json('Failed to send OTP via SMS. Please try again.', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Send same OTP via Email (Hostinger SMTP)
        if ($user->email) {
            try {
                Mail::to($user->email)->send(new OtpMail($otp, 'NilBox Code'));
            } catch (\Throwable) {
                // SMS already sent, so we continue even if email fails
            }
        }

        return $this->json('Your NilBox code is sent to your phone', [
            'email_or_phone' => $phoneCode . $user->phone,
            'phone_code'     => $phoneCode,
            'otp'            => app()->environment('local') ? $otp : null,
        ]);
    }

    /**
     * Verify the OTP submitted by the user.
     */
    public function verifyOtp(OTPVerifyRequest $request)
    {
        $user = UserRepository::findByPhone($request->phone);

        if (! $user) {
            return $this->json('Sorry! No user found', [], 422);
        }

        $verificationCode = VerificationCodeRepository::checkOTP($user->phone, $request->otp);

        if (! $verificationCode) {
            return $this->json('Invalid otp', [], Response::HTTP_BAD_REQUEST);
        }

        if (! $user->phone_verified_at && $user->phone) {
            $user->update(['phone_verified_at' => now()]);
        }

        return $this->json('Otp verified successfully', [
            'token' => $verificationCode->token,
        ]);
    }

    /**
     * Reset the user's password using the verified token.
     */
    public function resetPassword(PasswordResetRequest $request)
    {
        $verifyOTP = VerificationCodeRepository::checkByToken($request->token);

        $user = UserRepository::findByPhone($verifyOTP->phone);

        if (! $user) {
            return $this->json('Sorry! No user found with this phone.', [], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        $verifyOTP->delete();

        return $this->json('Password reset successfully');
    }
}
