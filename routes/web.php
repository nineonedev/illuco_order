<?php

use App\Admin\Controllers\AdminController;
use Framework\Security\Auth\Middleware\AuthMiddleware;
use Framework\Support\Facades\Route;

Route::get('/', [AdminController::class, 'signin'])->name('home');

Route::prefix('admin')
    ->name('admin')
    ->middleware(AuthMiddleware::class)
    ->group(function(){

    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/guide', [AdminController::class, 'guide'])->name('guide');
});