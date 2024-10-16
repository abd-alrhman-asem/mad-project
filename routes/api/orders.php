<?php


namespace app\routes\orders;

use App\Http\Controllers\Api\V1\OrderController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->controller(OrderController::class)->prefix('orders')->group(function() {
    Route::post('/update_order', 'update');
    Route::post('/delete_order/{order}', 'delete');
    Route::post('/clear_orders', 'clear');
    Route::get('/create_order', 'store')
    ->name('create_orders');
});


Route::get('/create-token',function (){
    $user = User::find(1);
    return $user->createToken('token')->plainTextToken;

});

