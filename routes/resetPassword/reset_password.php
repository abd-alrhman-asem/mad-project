<?php

namespace app\routes\resetPassword;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResetPasswordController;


Route::controller(ResetPasswordController::class)->prefix('user/password')->group(function () {
    Route::post('/email', 'forgotPassword');
    Route::post('/reset', 'resetPassword')
        ->middleware(['throttle:5,1']);
});
