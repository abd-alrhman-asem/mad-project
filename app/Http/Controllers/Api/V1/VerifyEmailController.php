<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyEmailRequest;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class VerifyEmailController extends Controller
{
    public function verify(VerifyEmailRequest $request)
    {
        $user = User::whereEmail($request->email)->first();


        $cachedCode=Cache::get("verification_code_{$user->id}");

        if(!$cachedCode || $cachedCode !==$request->code)
        {
            throw new InvalidCredentialsException("Invalid Verification code");
        }

        $user->email_verified_at=now();
        $user->save();
        Cache::forget("verification_code_{$user->id}");

         return response()->json([
            'message'=>'Email verified successfully'
        ],200);
    }
}
