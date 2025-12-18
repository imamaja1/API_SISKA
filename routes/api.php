<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\TahunAkademikController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Route::post('register', [ApiAuthController::class, 'register']);
    Route::post('login', [ApiAuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [ApiAuthController::class, 'me']);
        Route::post('logout', [ApiAuthController::class, 'logout']);
        Route::post('logout-all', [ApiAuthController::class, 'logoutAll']);

        # Tahun Akademik
        Route::get('tahun-akademik', [TahunAkademikController::class, 'GetTahunAkademik']);
        Route::post('tahun-akademik', [TahunAkademikController::class, 'CreateTahunAkademik']);
        Route::get('tahun-akademik/{id}/update', [TahunAkademikController::class, 'ShowTahunAkademik']);
        Route::put('tahun-akademik/{id}/update', [TahunAkademikController::class, 'UpdateTahunAkademik']);
        Route::delete('tahun-akademik/{id}/delete', [TahunAkademikController::class, 'DeleteTahunAkademik']);
        Route::patch('tahun-akademik/{id}/status', [TahunAkademikController::class, 'UpdateStatusTahunAkademik']);

        
    });
});
