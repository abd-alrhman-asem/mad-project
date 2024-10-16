<?php

use App\Http\Controllers\Api\v1\HomePageController;
use Illuminate\Support\Facades\Route;

Route::get('home', [HomePageController::class, 'index']);
