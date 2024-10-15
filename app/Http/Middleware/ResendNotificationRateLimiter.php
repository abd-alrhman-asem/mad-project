<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class ResendNotificationRateLimiter
{
    public function handle(Request $request, Closure $next)
    {
        $email = $request->email;
        $key = "resend_attempts_{$email}";

        if (RateLimiter::tooManyAttempts($key, 2)) {
            return response()->json(['error' => 'You can only resend the code twice within 5 minutes.'], 429);
        }
        RateLimiter::hit($key, 300);
        return $next($request);
    }
}
