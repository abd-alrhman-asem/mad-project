<?php

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\VerifyEmailRequest;
use App\Models\User;
use App\Services\CodeGenerateServices;
use Illuminate\Support\Facades\Cache;

class VerifyEmailController extends Controller
{
    public function __construct(
        protected CodeGenerateServices $codeGenerateServices,
    ) {}

    public function verify(VerifyEmailRequest $request)
    {
        $user = User::whereEmail($request->email)->first();

        if(!$this->codeGenerateServices->verifyCode($request->email,$request->code))
        {
            throw new InvalidCredentialsException("Invalid Verification code");
        }

        $user->email_verified_at=now();
        $user->save();
        Cache::forget("verification_code_{$request->email}");

         return response()->json([
            'message'=>'Email verified successfully',
             'token'=>$user->createToken('token')->plainTextToken
        ],200);
    }
}
