<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\MahasiswaController;
use App\Http\Controllers\Api\ProgramStudiController;
use App\Http\Controllers\Api\TahunAkademikController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Route::post('register', [ApiAuthController::class, 'register']);
    Route::post('login', [ApiAuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [ApiAuthController::class, 'me']);
        Route::post('logout', [ApiAuthController::class, 'logout']);
        Route::post('logout-all', [ApiAuthController::class, 'logoutAll']);

        // Mahasiswa
        Route::get('mahasiswa', [MahasiswaController::class, 'index']);
        Route::get('mahasiswa-nim', [MahasiswaController::class, 'show']);
        Route::get('mahasiswa-get', [MahasiswaController::class, 'GetMhs']);
        Route::post('mahasiswa-show', [MahasiswaController::class, 'ShowMhs']);

        // program studi (api)
        Route::get('program-studi', [ProgramStudiController::class, 'GetProgramStudi']);

        // Tahun Akademik
        Route::get('tahun-akademik', [TahunAkademikController::class, 'GetTahunAkademik']);
        Route::post('tahun-akademik', [TahunAkademikController::class, 'CreateTahunAkademik']);
        Route::get('tahun-akademik/{id}/update', [TahunAkademikController::class, 'ShowTahunAkademik']);
        Route::put('tahun-akademik/{id}/update', [TahunAkademikController::class, 'UpdateTahunAkademik']);
        Route::delete('tahun-akademik/{id}/delete', [TahunAkademikController::class, 'DeleteTahunAkademik']);
        Route::patch('tahun-akademik/{id}/status', [TahunAkademikController::class, 'UpdateStatusTahunAkademik']);

    });
});
