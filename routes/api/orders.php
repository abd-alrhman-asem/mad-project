<?php


namespace app\routes\orders;

use App\Http\Controllers\Api\V1\OrderController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->controller(OrderController::class)->prefix('orders')->group(function() {
    Route::post('/update_order', 'update');
    Route::get('/delete_order/{order}', 'delete');
    Route::get('/clear_orders', 'clear');
    Route::post('/create_order', 'store')
    ->name('create_orders');
});




