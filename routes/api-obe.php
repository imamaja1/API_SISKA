<?php

use App\Http\Controllers\Simortua\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/obe')->group(function () {
    Route::post('login-dosen', [AuthController::class, 'LoginNim']);
});
