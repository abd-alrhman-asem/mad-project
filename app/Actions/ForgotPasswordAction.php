<?php

namespace App\Actions;

use Illuminate\Support\Str;
use App\Models\ResetCodePassword;
use App\Services\RandomCodeService;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetCodePassword as ResetPasswordEmail;

class ForgotPasswordAction
{
    private RandomCodeService $randomCodeService;

    public function execute($request)
    {
        $this->randomCodeService = new RandomCodeService();
        $randomCode = $this->randomCodeService->generate();

        ResetCodePassword::create([
            'email' => $request['email'],
            'code' => $randomCode
        ]);

        Mail::to($request['email'])->send(new ResetPasswordEmail($randomCode));
    }
}
