<?php

use App\Http\Controllers\Api\Divisi\AkademikController;
use App\Http\Controllers\Api\Divisi\KedokteranController;
use App\Http\Controllers\Api\Divisi\UniversalController;
use App\Http\Controllers\Devisi\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/divisi')->middleware(['log.divisi', 'log.json:divisi'])->group(function () {
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
        // kedokteran endpoint
        Route::get('get-mhs-kedokteran', [KedokteranController::class, 'get_mhs_kedokteran']);
        Route::get('get-dosen-kedokteran', [KedokteranController::class, 'get_dosen_kedokteran']);
        Route::get('get-tahun-akademik', [KedokteranController::class, 'get_tahun_akademik']);
        Route::get('get-matakuliah', [KedokteranController::class, 'get_matakuliah']);
        Route::get('get-krs-khs', [KedokteranController::class, 'get_krs_khs']);
        Route::middleware(['role:akademik'])->group(function () {
            Route::get('status-perkuliahan', [AkademikController::class, 'getStatusPerkuliahan']);
            Route::get('status-perkuliahan-not-kumpul', [AkademikController::class, 'getStatusPerkuliahanNotKumpul']);
            Route::get('status-perkuliahan-kumpul', [AkademikController::class, 'getStatusPerkuliahanKumpul']);
            Route::get('status-perkuliahan-by-prodi', [AkademikController::class, 'getStatusPerkuliahanByProdi']);
            Route::get('status-perkuliahan-by-prodi-kumpul', [AkademikController::class, 'getStatusPerkuliahanByProdiKumpul']);
            Route::get('status-perkuliahan-by-prodi-not-kumpul', [AkademikController::class, 'getStatusPerkuliahanByProdiNotKumpul']);
            Route::put('update-pengumpulan-krs', [AkademikController::class, 'updatePengumpulanKRS']);
            Route::get('chart-pengumpulan-krs', [AkademikController::class, 'chart_pengumpulan_krs']);
            Route::get('chart-pengumpulan-krs-by-prodi', [AkademikController::class, 'chart_pengumpulan_krs_by_prodi']);
            Route::get('chart-pengumpulan-krs-by-tahun-angkatan', [AkademikController::class, 'chart_kumpulan_krs_by_tahun_angkatan']);
            Route::get('chart-pengumpulan-krs-by-prodi-and-tahun-angkatan', [AkademikController::class, 'chart_kumpulan_krs_by_prodi_and_tahun_angkatan']);
        });
    });
});
