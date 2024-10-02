<?php
namespace app\routes\orders;

use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('/create_orders', [OrderController::class, 'store']);
