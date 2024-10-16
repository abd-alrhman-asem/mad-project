<?php

namespace app\routes\resetPassword;

use App\Http\Controllers\Api\V1\Auth\ResetPasswordController;
use Illuminate\Support\Facades\Route;


Route::controller(ResetPasswordController::class)->prefix('user/password')->group(function () {
    Route::post('/email', 'forgotPassword');
    Route::post('/verify-code', 'verifyCode');
    Route::post('/reset', 'resetPassword')->middleware(['throttle:5,1', 'auth:sanctum', 'ability:reset-password']);
});
