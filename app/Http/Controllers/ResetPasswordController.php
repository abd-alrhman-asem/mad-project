<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{

    public function showResetForm($token)
    {
        $email = request()->query('email'); // get the email of the user from the url

        return redirect()->to('http://localhost:4200/reset-password/' . $token . '?email=' . urlencode($email)); // redirect the user to the frontend reset password page with the token and the email
    }

    public function reset(Request $request)
    {
        //validate the request
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);


        //i use the reset method from the class Password which takes : (array of credentials from the request as provided , callback function used if the password successfully reset)
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([ // update the password without mass assignment restriction
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60), // change the remember token used for remember me to invalidate previous remember me session
                ])->save();

                event(new PasswordReset($user)); // logging notification
            }
        );

        if ($status == Password::PASSWORD_RESET) { //reset successfull
            return response()->json(['message' => 'Password has been successfully reset.'], 200);
        }

        return response()->json(['message' => __($status)], 400);
    }
}
