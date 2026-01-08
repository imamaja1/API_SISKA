<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Simortua\AuthController;

Route::prefix('api/v1/simortua')->group(function () {
    Route::post('login-nim',  [AuthController::class, 'LoginNim']);
    Route::post('login-password',  [AuthController::class, 'LoginPassword']);
});