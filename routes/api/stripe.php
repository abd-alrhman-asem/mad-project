<?php

use App\Http\Controllers\Api\V1\OnlinePayment\SubscriptionController;
use App\Http\Controllers\Api\V1\OnlinePayment\UnSubscribeController;
use App\Http\Controllers\Api\V1\OnlinePayment\WebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');
    Route::get('/unsubscribe', UnSubscribeController::class)->name('unsubscribe');
});

Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook'])->name('stripe.webhook');
