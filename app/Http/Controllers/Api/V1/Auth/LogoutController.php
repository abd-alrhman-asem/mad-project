<?php

namespace App\Http\Controllers\Api\V1\Auth;;

use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function logout()
    {
        $user = auth()->user();
        $user->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out successfully.'
        ], 200);
    }
}
