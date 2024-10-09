<?php

namespace App\Services\Password;

use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordService
{
    /**
     * Handle sending the verification code for password reset.
     *
     * @param string $email
     * @return string
     */
    public function sendVerificationCode(string $email): string
    {
        // Find the user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            throw new \Exception('Email not found.');
        }

        // Generate a random verification code
        $verificationCode = Str::random(6); // Example: 6 character random code

        // Store the code in cache for 10 minutes
        Cache::put('password_reset_' . $user->email, $verificationCode, 600); // 600 seconds = 10 minutes

        // Send the code via email
        Mail::to($user->email)->send(new VerificationCodeMail($verificationCode));

        return 'Verification code sent to your email!';
    }
}
