<?php

namespace app\routes\orders;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

Route::middleware('auth:sanctum')->group(function () {
});
Route::post('/update_orders', [OrderController::class, 'update']);
Route::post('/delete_order/{order}', [OrderController::class, 'delete']);
Route::post('/clear_orders', [OrderController::class, 'clear']);
