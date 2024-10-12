<?php

namespace App\Services;

use App\Models\User;
use App\Mail\ResetCodePassword;
use App\Services\RandomCodeService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class ResetPasswordService
{
    public function __construct(protected RandomCodeService $randomCodeService) {}

    //forgot password
    public function generateRandomCode($request)
    {
        $randomCode = $this->randomCodeService->generate();

        Cache::put('reset_email-' . $request['email'], $request['email'], now()->addMinutes(10));
        Cache::put('reset_code-' . $request['email'], $randomCode, now()->addMinutes(10));

        Mail::to($request['email'])->send(new ResetCodePassword($randomCode));
    }

    //verify code
    public function verifyCode($request)
    {
        $resetCode = Cache::get('reset_email-' . $request['email']);
        $resetCode = Cache::get('reset_code-' . $request['email']);

        if (!$resetCode || $resetCode !== $request['code']) {
            return response()->json(['message' => 'Invalid code'], 400);
        }

        $user = User::where('email', $request['email'])->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        } else {
            $token = $user->createToken('reset-token', ['reset-password'])->plainTextToken;
        }

        Cache::forget($request['email']);

        return $token;
    }

    //reset password
    public function resetPassword($request)
    {
        $user = auth()->user();

        $user->password = Hash::make($request['password']);
        $user->save();
    }
}
