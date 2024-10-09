<?php

namespace App\Http\Controllers\Api\V1\Password;

use App\Http\Controllers\Controller;
use App\Http\Requests\Password\ResetPasswordRequest;
use App\Services\Password\ResetPasswordService;

class ResetPasswordController extends Controller
{
    protected $resetPasswordService;

    public function __construct(ResetPasswordService $resetPasswordService)
    {
        $this->resetPasswordService = $resetPasswordService;
    }

    public function __invoke(ResetPasswordRequest $request)
    {
        try {
            $message = $this->resetPasswordService->resetPassword(
                $request->input('email'),
                $request->input('verification_code'),
                $request->input('password')
            );
            return response()->json(['message' => $message], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
