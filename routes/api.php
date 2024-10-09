<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\v1\HomePageController;
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

include __DIR__ . "/Password/password.php";
include __DIR__ . "/orders/orders.php";
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware([CorsMiddleware::class])->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
        Route::post('/unsubscribe', UnSubscribeController::class)->name('unsubscribe');
    });
});

Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook'])->name('stripe.webhook');


Route::post('login', LoginController::class);


Route::get('home', [HomePageController::class, 'index']); // no need to define it under a middleware as guests can view homepage (unless guests middleware is added in future)
