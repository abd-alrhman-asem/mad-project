<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Exceptions\InvalidCredentialsException;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!Hash::check($request->password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        return response()->json([
            'access_token' => $user->createToken($request->email)->plainTextToken,
        ]);
    }
}
