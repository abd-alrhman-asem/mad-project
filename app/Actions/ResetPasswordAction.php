<?php

namespace App\Actions;

use App\Models\User;
use App\Models\ResetCodePassword;
use Illuminate\Support\Facades\Hash;

class ResetPasswordAction
{
    public function execute($request)
    {
        $resetCode = ResetCodePassword::firstWhere('code', $request['code']);

        if (!$resetCode) {
            return response()->json(['message' => 'Invalid code'], 400);
        }

        if ($resetCode->created_at > now()->addMinutes(10)) {
            $resetCode->delete();
            return response()->json(['message' => 'Expired code'], 422);
        }

        $user = User::where('email', $resetCode->email)->first();
        $user->password = Hash::make($request['password']);
        $user->save();

        $resetCode->delete();
    }
}
