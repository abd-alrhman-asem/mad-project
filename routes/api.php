<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\v1\HomePageController;
use App\Http\Controllers\Api\V1\VerifyEmailController;
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


Route::get('home', [HomePageController::class, 'index']); // no need to define it under a middleware as guests can view homepage (unless guests middleware is added in future)


Route::post('/verify-email',[VerifyEmailController::class,'verify']);
