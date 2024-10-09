<?php

namespace App\Http\Controllers\Api\V1\Password;

use App\Http\Controllers\Controller;
use App\Http\Requests\Password\ForgotPasswordRequest;
use App\Services\Password\ForgotPasswordService;

class ForgotPasswordController extends Controller
{
    protected $forgotPasswordService;

    public function __construct(ForgotPasswordService $forgotPasswordService)
    {
        $this->forgotPasswordService = $forgotPasswordService;
    }

    public function __invoke(ForgotPasswordRequest $request)
    {
        try {
            $message = $this->forgotPasswordService->sendVerificationCode($request->input('email'));
            return response()->json(['message' => $message], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }
}
