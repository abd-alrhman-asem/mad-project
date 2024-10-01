<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Support\Facades\Notification;

class ForgotPasswordController extends Controller
{

    // public function __invoke(Request $request)
    // {

    //     // validate the email before send the reset link
    //     $request->validate([
    //         'email' => 'required|email|exists:users,email',
    //     ]);

    //     // here i used class Password from Facades
    //     $status = Password::sendResetLink( // the link send to the url : http://APP_URL.com/reset-password?token=random token &email=the email provided by the user
    //         $request->only('email') // we can include addtional data if we implement custom email using notification may here i just want the email
    //     );

    //     if ($status == Password::RESET_LINK_SENT) { // link sent successfully
    //         return response()->json(['message' => __($status)], 200);
    //     }

    //     return response()->json(['message' => __($status)], 400); // bad request | some error occurred
    // }

    // used customized email

    public function __invoke(Request $request)
    {
        // Validate the email before sending the reset link
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // generate a random token
        $token = Password::getRepository()->create($user);

        // Send the custom reset password notification
        Notification::send($user, new CustomResetPasswordNotification($token));

        return response()->json(['message' => 'Password reset link sent!'], 200);
    }
}
