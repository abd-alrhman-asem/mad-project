<?php

namespace App\Http\Controllers\Api\V1\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\ResendNotificationRequest;
use App\Models\User;
use App\Notifications\VerificationCodeNotification;
use App\Services\CodeGenerateServices;


class ResendVerificationCodeController extends Controller
{
    public function __construct(
        protected CodeGenerateServices $codeGenerateServices
    )
    {

    }
    public function resendVerificationCode(ResendNotificationRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $code = $this->codeGenerateServices->generateCode();
        $this->codeGenerateServices->storeCodeInCache($user->email, $code);
        $user->notify(new VerificationCodeNotification($code));
        return response()->json(['message' => 'Verification code sent successfully']);
    }



}
