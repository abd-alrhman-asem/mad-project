<?php

use Illuminate\Support\Facades\Route;


include __DIR__ . "/api/reset_password.php";
include __DIR__ . "/api/orders.php";
include __DIR__ . "/api/auth.php";
include __DIR__ . "/api/homePage.php";


Route::fallback(function () {
    return response()->json(['message' => 'Page not found.'], 404);
});
