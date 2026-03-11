<?php

use App\Http\Controllers\Siska\AuthSiskaController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/siska')->middleware(['log.mahasiswa', 'log.json:mahasiswa'])->group(function () {
    // Login can be stateful (Sanctum SPA cookie/session) or stateless (Bearer token).
    Route::post('login-mhs', [AuthSiskaController::class, 'login']);
    // Protected endpoints: accept either Sanctum session (SPA cookie) or Bearer token.
    Route::middleware(['auth:sanctum', 'auth:auth_mhs_siska'])->group(function () {
        Route::get('me', [AuthSiskaController::class, 'me']);
        Route::post('logout', [AuthSiskaController::class, 'logout']);
    });
});
