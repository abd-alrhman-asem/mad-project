<?php

namespace App\Actions;

use Illuminate\Support\Facades\Hash;

class ResetPasswordAction
{
    public function execute($request)
    {
        $user = auth()->user();

        $user->password = Hash::make($request['password']);
        $user->save();
    }
}
