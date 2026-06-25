<?php

use App\Http\Controllers\Api\Obe\PenilaianObeController;
use App\Http\Controllers\Obe\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix("api/v1/obe")
    ->middleware(["log.api", "log.json:obe"])
    ->group(function () {
        // Login can be stateful (Sanctum SPA cookie/session) or stateless (Bearer token).
        Route::post("login", [AuthController::class, "login"]);
        // Protected endpoints: accept either Sanctum session (SPA cookie) or Bearer token.
        Route::middleware(["auth:sanctum", "auth:dosen_web"])->group(
            function () {
                Route::post("logout", [AuthController::class, "logout"]);
                Route::get("me", [AuthController::class, "me"]);
                Route::get("kelas", [PenilaianObeController::class, "kelas"]);
                Route::get("penilaian", [
                    PenilaianObeController::class,
                    "penilaian",
                ]);
                Route::put("penilaian", [
                    PenilaianObeController::class,
                    "updatePenilaian",
                ]);
                Route::put("penilaian/batch", [
                    PenilaianObeController::class,
                    "updatePenilaianAll",
                ]);
                // sycrn-obe
                Route::get("obe-penilaian", [
                    PenilaianObeController::class,
                    "obe_penilaian",
                ]);
            },
        );
    });
