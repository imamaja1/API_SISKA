<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1/simortua')->group(function () {
    Route::get('login-nim', [TahunAkademikController::class, 'GetTahunAkademik']);
});