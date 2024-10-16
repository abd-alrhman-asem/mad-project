<?php


namespace app\routes\orders;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Models\User;

Route::middleware('auth:sanctum')->controller(OrderController::class)->prefix('orders')->group(function() {
    Route::post('/update_orders', 'update');
    Route::post('/delete_order/{order}', 'delete');
    Route::post('/clear_orders', 'clear');
  Route::post('/create_orders', 'store'])
    ->name('create_orders');
});


Route::get('/create-token',function (){
    $user = User::find(1);
    return $user->createToken('token')->plainTextToken;

});

