<?php

namespace App\Actions;

use App\Services\RandomCodeService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\ResetCodePassword as ResetPasswordEmail;

class ForgotPasswordAction
{
    public function __construct(protected RandomCodeService $randomCodeService) {}

    public function execute($request)
    {
        $randomCode = $this->randomCodeService->generate();

        Cache::putMany([
            'email' => $request['email'],
            'code' => $randomCode
        ], now()->addMinutes(10));

        Mail::to($request['email'])->send(new ResetPasswordEmail($randomCode));
    }
}
