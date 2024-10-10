<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;


class VerifyCodeAction
{
    public function execute($request)
    {
        $resetData = Cache::many(['email', 'code']);

        $email = $resetData['email'];
        $code = $resetData['code'];


        if (!$code || $code !== $request['code']) {
            return response()->json(['message' => 'Invalid code'], 400);
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        } else {
            $token = $user->createToken('reset-token', ['reset-password'])->plainTextToken;
        }

        Cache::forget('email');
        Cache::forget('code');

        return $token;
    }
}
