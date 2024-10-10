<?php

namespace App\Http\Controllers;

use App\Actions\ResetPasswordAction;
use App\Actions\ForgotPasswordAction;
use App\Actions\VerifyCodeAction;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\VerifyCodeRequest;
use Exception;

class ResetPasswordController extends Controller
{
    public function forgotPassword(ForgotPasswordRequest $request, ForgotPasswordAction $forgotPasswordAction)
    {
        $forgotPasswordAction->execute($request->validated());

        return response()->json([
            'message' => 'Code sent to your email'
        ]);
    }

    public function verifyCode(VerifyCodeRequest $request, VerifyCodeAction $verifyCodeAction)
    {
        $token = $verifyCodeAction->execute($request->validated());

        if (!$token) {
            throw new Exception('Inavalid code');
        }

        return response()->json([
            'message' => 'Correct Code.',
            'token' => $token
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request, ResetPasswordAction $resetPasswordAction)
    {
        $resetPasswordAction->execute($request->validated());

        return response()->json([
            'message' => 'Password reset successfully.'
        ]);
    }
}
