<?php

namespace App\Http\Controllers\Api\V1\Auth;
use App\Http\Controllers\Controller;
use App\Http\Services\CodeGenerateServices;
use App\Http\Services\UserServices;
use App\Http\Requests\ResendNotificationRequest;
use App\Models\User;
use App\Notifications\VerificationCodeNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ResendNotificationController extends Controller
{

    private CodeGenerateServices $codeGenerateServices;
    public function __construct()
    {
        $this->codeGenerateService = new CodeGenerateServices();
    }
    public function resendNotification(ResendNotificationRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $code = $this->codeGenerateService->generateCode();
        $user->notify(new VerificationCodeNotification($code));

        return response()->json(['message' => 'Verification code sent successfully']);
    }


}
