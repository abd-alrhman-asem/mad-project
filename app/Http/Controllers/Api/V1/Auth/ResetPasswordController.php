<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\VerifyCodeRequest;
use App\Services\ResetPasswordService;

class ResetPasswordController extends Controller
{
    public function __construct(protected ResetPasswordService $resetPasswordService) {}

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->resetPasswordService->sendCodeToEmail($request->validated());

        return response()->json([
            'message' => 'Code sent to your email'
        ]);
    }

    public function verifyCode(VerifyCodeRequest $request)
    {
        $token = $this->resetPasswordService->verifyCode($request->validated());

        return response()->json([
            'token' => $token
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->resetPasswordService->resetPassword($request->validated());

        return response()->json([
            'message' => 'Password reset successfully.'
        ]);
    }
}
