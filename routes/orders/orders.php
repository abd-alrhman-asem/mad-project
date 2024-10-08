<?php

namespace app\routes\orders;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::middleware('auth:sanctum')->controller(OrderController::class)->prefix('orders')->group(function() {
    Route::post('/update_orders', 'update');
    Route::post('/delete_order/{order}', 'delete');
    Route::post('/clear_orders', 'clear');
});

