<?php

namespace app\routes\resetPassword;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ResetPasswordController;


Route::post('/user/password/email', [ResetPasswordController::class, 'forgotPassword']);
Route::post('/user/password/reset', [ResetPasswordController::class, 'resetPassword'])
    ->middleware(['throttle:5,1']);
