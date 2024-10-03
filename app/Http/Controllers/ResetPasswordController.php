<?php

namespace App\Http\Controllers;

use App\Actions\ResetPasswordAction;
use App\Actions\ForgotPasswordAction;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ForgotPasswordRequest;

class ResetPasswordController extends Controller
{
    public function forgotPassword(ForgotPasswordRequest $request, ForgotPasswordAction $action)
    {
        $action->execute($request);

        return response()->json([
            'message' => 'Code sent to your email'
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request, ResetPasswordAction $action)
    {
        $action->execute($request);

        return response()->json([
            'message' => 'Password reset successfully.'
        ]);
    }
}
