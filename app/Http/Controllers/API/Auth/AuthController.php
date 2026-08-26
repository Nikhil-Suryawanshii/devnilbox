<?php

namespace App\Http\Controllers\API\Auth;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\VerifyLoginOtpRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Repositories\CustomerRepository;
use App\Repositories\DeviceKeyRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Services\TwilioOTPService;
use App\Support\UserLogout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function __construct(protected TwilioOTPService $otpService) {}
    /**
     * Register a new user and return the registration result.
     */
    public function register(RegistrationRequest $request): JsonResponse
    {
        // Create a new user
        $user = UserRepository::registerNewUser($request);

        $this->storeDeviceKeyWhenPresent($user, $request);

        // Create a new customer
        CustomerRepository::storeByRequest($user);

        // create wallet
        WalletRepository::storeByRequest($user);

        $user->assignRole(Roles::CUSTOMER->value);

        return $this->json('Registration successfully complete', [
            'user' => new UserResource($user),
            'access' => UserRepository::getAccessToken($user),
        ]);
    }

    /**
     * Login a user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->authenticate($request);
        if ($user?->customer) {

            $this->storeDeviceKeyWhenPresent($user, $request);

            return $this->json('Login successfully', [
                'user' => new UserResource($user),
                'access' => UserRepository::getAccessToken($user),
            ]);
        }

        return $this->json('Credential is invalid!', [], Response::HTTP_BAD_REQUEST);
    }

    // public function login(LoginRequest $request)
    // {

    //     $user = $this->authenticate($request);


    //     if (! $user?->customer) {
    //         return $this->json('Credential is invalid!', [], Response::HTTP_BAD_REQUEST);
    //     }

    //     // Format phone for Twilio — must be E.164 e.g. +919697752975
    //     $phone = $this->formatPhone($user->phone);

    //     try {
    //         $this->otpService->sendOTP($phone);
    //     } catch (\Exception $e) {
    //         return $this->json($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
    //     }
    //     $otp = config('app.debug') ? Cache::get("login_otp_{$phone}") : null;
    //     return $this->json('OTP sent to your registered phone number.', [
    //         'otp_required' => true,
    //         'phone'        => $phone,
    //         'debug_otp'    => $otp,
    //     ]);
    // }

    /**
     * Step 2: Verify OTP → return access token.
     */
    public function verifyLoginOtp(VerifyLoginOtpRequest $request)
    {
        $phone = $this->formatPhone($request->phone);

        if (! $this->otpService->verifyOTP($phone, $request->otp)) {
            return $this->json('Invalid or expired OTP.', [], Response::HTTP_BAD_REQUEST);
        }

        $user = UserRepository::findByPhone($request->phone);

        if (! $user?->customer) {
            return $this->json('User not found.', [], Response::HTTP_NOT_FOUND);
        }

        if ($request->device_key) {
            DeviceKeyRepository::storeByRequest($user, $request);
        }

        return $this->json('Login successfully', [
            'user'   => new UserResource($user),
            'access' => UserRepository::getAccessToken($user),
        ]);
    }

    /**
     * Authenticate the user and return the user.
     */
    private function authenticate(LoginRequest $request): ?User
    {
        $user = UserRepository::findByPhone($request->phone);
        if (! is_null($user) && Hash::check($request->password, $user->password)) {
            return $user;
        }

        return null;
    }


    private function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone); // Strip non-digits

        // Already has country code (10+ digits with leading 91 for India)
        if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
            return '+' . $phone;
        }

        // 10-digit Indian number — prepend +91
        if (strlen($phone) === 10) {
            return '+91' . $phone;
        }

        return '+' . $phone;
    }

    private function storeDeviceKeyWhenPresent(User $user, Request $request): void
    {
        if ($request->device_key) {
            DeviceKeyRepository::storeByRequest($user, $request);
        }
    }

    /**
     * Logout the user and revoke the token.
     */
    public function logout(Request $request): JsonResponse
    {
        /** @var \User $user */
        $user = auth()->user();

        if (! $user) {
            return $this->json('User not found!', [], Response::HTTP_NOT_FOUND);
        }

        UserLogout::revokeApiAccess($user);
        UserLogout::revokeWebSession($request);

        return $this->json('Logged out successfully!');
    }

    public function callback(Request $request) {}
}
