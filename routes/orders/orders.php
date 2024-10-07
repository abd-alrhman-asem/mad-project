<?php
namespace app\routes\orders;

use App\Http\Controllers\OrderController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::post('/create_orders', [OrderController::class, 'store'])
    ->name('create_orders')
    ->middleware('auth:sanctum');

Route::get('/create-token',function (){
    $user = User::find(1);
    return $user->createToken('token')->plainTextToken;
});
