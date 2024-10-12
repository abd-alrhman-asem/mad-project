<?php

namespace App\Http\Controllers\Api\V1\Auth;
use App\Http\Controllers\Controller;
use App\Http\Services\CodeGenerateServices;
use App\Http\Requests\ResendNotificationRequest;
use App\Models\User;
use App\Notifications\VerificationCodeNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ResendVerificationCodeController extends Controller
{
    public function __construct(
        protected CodeGenerateServices $codeGenerateServices
    )
    {

    }
    public function resendNotification(ResendNotificationRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $code = $this->codeGenerateServices->generateCode();
        $this->codeGenerateServices->storeCodeInCache($user->email, $code);
        $user->notify(new VerificationCodeNotification($code));
        return response()->json(['message' => 'Verification code sent successfully']);
    }



}
