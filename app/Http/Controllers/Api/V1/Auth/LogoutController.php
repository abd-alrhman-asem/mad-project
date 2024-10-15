<?php

namespace App\Http\Controllers\Api\V1\Auth;;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
