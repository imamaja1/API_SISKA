<?php

use App\Http\Controllers\Obe\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/obe')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::group(['middleware' => ['auth:sanctum', 'dosen']], function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});
