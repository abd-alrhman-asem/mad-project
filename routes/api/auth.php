<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\ResendVerificationCodeController;
use App\Http\Controllers\Api\V1\VerifyEmailController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [RegisterController::class, 'registerFunction']);
Route::post('/login', LoginController::class);
Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/verify-email',[VerifyEmailController::class,'verify']);
Route::post('/resendNotification', [ResendVerificationCodeController::class, 'resendNotification'])
    ->middleware('throttle:2,5');
