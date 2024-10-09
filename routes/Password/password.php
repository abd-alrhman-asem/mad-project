<?php

namespace app\routes\orders;

use App\Http\Controllers\Api\V1\Password\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Password\ResetPasswordController;
use App\Http\Controllers\OrderController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::post('/forgot-password', ForgotPasswordController::class);
Route::post('/reset-password', ResetPasswordController::class);
