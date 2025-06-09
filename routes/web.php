<?php

use App\Domains\Notices\Controllers\ClaimController;
use App\Domains\Notice\Controllers\NoticeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Framework\Support\Facades\Route;

Route::middleware(['web'])->group(function(){

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
    ->middleware(['auth'])
    ->group(function(){

        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('guide', [AdminController::class, 'guide'])->name('guide');
        Route::get('test', [AdminController::class, 'test'])->name('test');

        Route::get('seting', [AdminController::class, 'setting'])->name('setting'); 


        Route::prefix('notices')
        ->name('notices')
        ->group(function(){
            Route::get('/', [NoticeController::class, 'index'])->name('index');
            Route::get('create', [NoticeController::class, 'create'])->name('create');
            Route::get('edit/{id}', [NoticeController::class, 'edit'])->name('edit');
            Route::get('{id}', [NoticeController::class, 'show'])->name('show');
            
            Route::post('/', [NoticeController::class, 'store'])->name('store');
            Route::put('{id}', [NoticeController::class, 'update'])->name('update');
            Route::delete('{id}', [NoticeController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('claims')
        ->name('claims')
        ->group(function(){
            Route::get('/', [ClaimController::class, 'index'])->name('index');
        });

    });

});
