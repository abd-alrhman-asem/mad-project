<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class ResetPasswordAction
{
    public function execute($request)
    {
        $resetEmail = Cache::get('reset_email.' . $request['email']);
        $resetCode = Cache::get('reset_code.' . $request['email']);

        if (!$resetCode || $resetCode !== $request['code']) {
            return response()->json(['message' => 'Invalid code'], 400);
        }

        $user = User::where('email', $resetEmail)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->password = Hash::make($request['password']);
        $user->save();

        Cache::forget('reset_email.' . $request['email']);
        Cache::forget('reset_code.' . $request['email']);
    }
}
