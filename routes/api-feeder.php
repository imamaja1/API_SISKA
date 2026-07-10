<?php

use App\Http\Controllers\Api\Feeder\SyncController;
use App\Http\Controllers\Api\Feeder\ValidasiController;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/feeder')->middleware(['log.api', 'log.json:feeder'])->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('prodi', [ValidasiController::class, 'getProdi']);
        Route::get('ta', [ValidasiController::class, 'getTahunAkademik']);
        Route::get('matakuliah', [ValidasiController::class, 'getMatakuliah']);
        Route::get('kelas', [ValidasiController::class, 'getKelas']);

        Route::get('validasi/mahasiswa', [ValidasiController::class, 'validasiMahasiswa']);
        Route::get('validasi/kelas', [ValidasiController::class, 'validasiKelas']);

        Route::middleware(['role:akademik', ThrottleRequests::class.':30,1'])->group(function () {
            Route::post('sync', [SyncController::class, 'sync']);
            Route::get('history', [SyncController::class, 'history']);
        });
    });
});
