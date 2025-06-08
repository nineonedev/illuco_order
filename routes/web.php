<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Framework\Security\Auth\Middleware\AuthMiddleware;
use Framework\Support\Facades\Route;


Route::get('/', [AuthController::class, 'signin'])->name('home');

Route::prefix('auth')
->name('auth')
->group(function () {

    Route::get('signin', [AuthController::class, 'signin'])->name('signin');
    Route::post('signin', [AuthController::class, 'login'])->name('login');
    
    Route::get('signup', [AuthController::class, 'signup'])->name('signup');
    Route::post('signup', [AuthController::class, 'register'])->name('register');
    
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::prefix('admin')
->name('admin')
->middleware(AuthMiddleware::class)
->group(function(){

    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/guide', [AdminController::class, 'guide'])->name('guide');
    Route::get('test', [AdminController::class, 'test'])->name('test');
});
