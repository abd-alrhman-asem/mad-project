<?php

namespace App\Actions;

use Illuminate\Support\Str;
use App\Models\ResetCodePassword;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetCodePassword as ResetPasswordEmail;

class ForgotPasswordAction
{
    public function execute($request)
    {
        $randomCode = Str::random(6);

        ResetCodePassword::create([
            'email' => $request['email'],
            'code' => $randomCode
        ]);

        Mail::to($request['email'])->send(new ResetPasswordEmail($randomCode));
    }
}
