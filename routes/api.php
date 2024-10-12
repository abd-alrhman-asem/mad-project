<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', LoginController::class);

//helz
Route::post('resendNotification', [\App\Http\Controllers\Api\V1\Auth\ResendVerificationCodeController::class, 'resendNotification'])
    ->middleware(['throttle:5,1','auth:sanctum','ability:resendNotification']);
    Route::post('register', [\App\Http\Controllers\Api\V1\Auth\RegisterController::class, 'registerFunction']);
    Route::post('/logout', [\App\Http\Controllers\Api\V1\Auth\LogoutController::class, 'logout'])->middleware('auth:sanctum');
  //  Route::post('resendNotification', [\App\Http\Controllers\Api\V1\Auth\ResendNotificationController::class, 'resendNotification']);
//    Route::post('resendNotification', [\App\Http\Controllers\Api\V1\Auth\ResendVerificationCodeController::class, 'resendNotification'])
//   ->middleware('resend.rate.limit');

Route::post('resendNotification', [\App\Http\Controllers\Api\V1\Auth\ResendVerificationCodeController::class, 'resendNotification'])
    ->middleware('throttle:2,5');










