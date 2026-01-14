<?php

use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\Pembayaran;
use App\Http\Controllers\Simortua\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/simortua')->group(function () {
    Route::post('login-nim', [AuthController::class, 'LoginNim']);
    Route::post('login-password', [AuthController::class, 'LoginCredentials']);
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('mahasiswa', [MahasiswaController::class, 'ShowMhs']);
        Route::get('pembayaran', [Pembayaran::class, 'CheckPembayaran']);
    });
});
