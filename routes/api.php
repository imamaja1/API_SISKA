<?php

use App\Http\Controllers\Api\ApiAuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Route::post('register', [ApiAuthController::class, 'register']);
    Route::post('login', [ApiAuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [ApiAuthController::class, 'me']);
        Route::post('logout', [ApiAuthController::class, 'logout']);
        Route::post('logout-all', [ApiAuthController::class, 'logoutAll']);
    });
});
