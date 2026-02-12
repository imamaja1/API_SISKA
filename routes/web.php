<?php

use App\Http\Controllers\ApiPanel\AuthController;
use App\Http\Controllers\ApiPanel\PanelController;
use App\Http\Middleware\EnsureApiUserAuthenticated;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('api-panel/login', [AuthController::class, 'showLoginForm'])->name('api_user.login');
Route::post('api-panel/login', [AuthController::class, 'login'])->name('api_user.login.submit');
Route::post('api-panel/logout', [AuthController::class, 'logout'])->name('api_user.logout')->middleware(EnsureApiUserAuthenticated::class);

Route::middleware([EnsureApiUserAuthenticated::class])->prefix('api-panel')->group(function () {
    Route::get('/', [PanelController::class, 'index'])->name('api_panel.home');
    // OBE auth tester page (browser-based Sanctum cookie auth)
    Route::view('obe-test', 'obe_test')->name('obe_test');
});

Route::post('api/v1/obe/login', [AuthController::class, 'login']);
