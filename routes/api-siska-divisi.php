<?php

use App\Http\Controllers\Api\Divisi\AkademikController;
use App\Http\Controllers\Api\Divisi\UniversalController;
use App\Http\Controllers\Devisi\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/divisi')->group(function () {
    // Login can be stateful (Sanctum SPA cookie/session) or stateless (Bearer token).
    Route::post('login', [AuthController::class, 'login']);
    // Protected endpoints: accept either Sanctum session (SPA cookie) or Bearer token.
    Route::middleware(['auth:sanctum', 'auth:auth_divisi_siska'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
        // universal endpoint
        Route::get('tahun-akademik', [UniversalController::class, 'tahunAkademik']);
        Route::get('tahun-akademik/aktif', [UniversalController::class, 'tahunAkademikAktif']);
        Route::get('tahun-akademik/find', [UniversalController::class, 'tahunAkademikByKode']);
        Route::get('program-studi', [UniversalController::class, 'program_studi']);
        Route::middleware(['role:akademik'])->group(function () {
            Route::get('status-perkuliahan', [AkademikController::class, 'getStatusPerkuliahan']);
            Route::get('status-perkuliahan-by-prodi', [AkademikController::class, 'getStatusPerkuliahanByProdi']);
            Route::put('update-pengumpulan-krs', [AkademikController::class, 'updatePengumpulanKRS']);
        });
    });
});
