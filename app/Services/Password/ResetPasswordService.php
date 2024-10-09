<?php

namespace App\Services\Password;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ResetPasswordService
{
    /**
     * Reset the user's password.
     *
     * @param string $email
     * @param string $verificationCode
     * @param string $newPassword
     * @return string
     * @throws \Exception
     */
    public function resetPassword(string $email, string $verificationCode, string $newPassword): string
    {
        // Retrieve the code from cache
        $cachedCode = Cache::get('password_reset_' . $email);

        // Check if the code matches and is still valid
        if (!$cachedCode || $cachedCode !== $verificationCode) {
            Log::info("Invalid or expired verification code for email: $email");
            throw new \Exception('Invalid or expired verification code.');
        }

        // Proceed with password reset
        $user = User::where('email', $email)->first();

        if (!$user) {
            Log::info("Invalid email for password reset: $email");
            throw new \Exception('Invalid email.');
        }

        // Update the user's password and clear the cache
        $user->update(['password' => Hash::make($newPassword)]);
        Cache::forget('password_reset_' . $email); // Clear the cached code
        Log::info("Password reset successfully for email: $email");

        return 'Password has been successfully reset.';
    }
}
