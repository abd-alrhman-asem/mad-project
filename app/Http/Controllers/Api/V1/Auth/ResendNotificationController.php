<?php

namespace App\Http\Controllers\Api\V1\Auth;
use App\Http\Services\UserServices;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResendNotificationRequest;
use App\Models\User;
use App\Notifications\VerificationCodeNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ResendNotificationController extends Controller
{
    private UserServices $userServices;

    public function __construct()
    {
        $this->userServices = new UserServices();
    }


//
    public function resendNotification(ResendNotificationRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $key = "resend_attempts_{$user->email}";

        // Check the rate limit
        if (RateLimiter::tooManyAttempts($key, 2)) {
            return response()->json(['error' => 'You can only resend the code twice within 5 minutes.'], 429);
        }

        // Increase the attempts
        RateLimiter::hit($key, 300); // 5 minutes = 300 seconds

        // If the user has not exceeded the limit, proceed to send the code
        $code = $this->userServices->generateCode();
        $user->notify(new VerificationCodeNotification($code));

        return response()->json(['message' => 'Verification code sent successfully']);
    }


}
