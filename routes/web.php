<?php

use App\Admin\Controllers\AdminController;
use Framework\Security\Auth\Middleware\AuthMiddleware;
use Framework\Support\Facades\Route;


Route::get('/', [AdminController::class, 'signin'])->name('home');

Route::prefix('auth')
    ->name('auth')
    ->group(function () {
    Route::post('login', [AdminController::class, 'login'])->name('login');
    Route::post('logout', [AdminController::class, 'logout'])->name('logout');
});

Route::prefix('admin')
    ->name('admin')
    ->middleware(AuthMiddleware::class)
    ->group(function(){

    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/guide', [AdminController::class, 'guide'])->name('guide');
});
