<?php


use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\v1\HomePageController;
use App\Http\Controllers\Api\V1\VerifyEmailController;
use App\Http\Controllers\Api\V1\OnlinePayment\WebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\OnlinePayment\SubscriptionController;
use App\Http\Controllers\Api\V1\OnlinePayment\UnSubscribeController;
use App\Http\Middleware\CorsMiddleware;


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

include __DIR__ . "/resetPassword/reset_password.php";
include __DIR__ . "/orders/orders.php";

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::post('/unsubscribe', UnSubscribeController::class)->name('unsubscribe');
});


Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook'])->name('stripe.webhook');


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


Route::get('home', [HomePageController::class, 'index']); // no need to define it under a middleware as guests can view homepage (unless guests middleware is added in future)


Route::post('/verify-email',[VerifyEmailController::class,'verify']);

