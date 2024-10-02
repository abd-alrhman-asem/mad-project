<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResendNotificationRequest;
use App\Models\User;
use App\Notifications\VerificationCodeNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ResendNotificationController extends Controller
{

    public function resendNotification(ResendNotificationRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $attemptsCacheKey = "resend_attempts_{$user->email}";
        $lastSentCacheKey = "last_sent_time_{$user->email}";

        $resendAttempts = Cache::get($attemptsCacheKey, 0);
        $lastSentTime = Cache::get($lastSentCacheKey, null);

        if ($lastSentTime && Carbon::parse($lastSentTime)->diffInMinutes(now()) < 5 && $resendAttempts >= 2) {
            return response()->json(['error' => 'You can only resend the code twice within 5 minutes.'], 429);
        }

        if ($lastSentTime && Carbon::parse($lastSentTime)->diffInMinutes(now()) >= 5) {
            $resendAttempts = 0;
        }

        $lowercaseLetters = 'abcdefghijklmnopqrstuvwxyz';
        $uppercaseLetters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        $characters = $lowercaseLetters . $uppercaseLetters . $numbers;

        $verificationCode = substr(str_shuffle($characters), 0, 6);

        Cache::put($lastSentCacheKey, now(), 5 * 60);
        Cache::put($attemptsCacheKey, $resendAttempts + 1, 5 * 60);

        $user->notify(new VerificationCodeNotification($verificationCode));

        return response()->json(['message' => 'Verification code sent successfully']);
    }

}
