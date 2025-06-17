<?php

use App\Http\Controllers\ClaimController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use Framework\Support\Facades\Route;

Route::middleware(['web'])->group(function(){

    // ============================================================================================================
    // home 
    // ============================================================================================================
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

    // ============================================================================================================
    // admin 
    // ============================================================================================================
    Route::prefix('admin')
        ->name('admin')
        ->middleware(['auth'])
        ->group(function(){
            
            Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('guide', [AdminController::class, 'guide'])->name('guide');
            Route::get('test', [AdminController::class, 'test'])->name('test');
            Route::get('seting', [AdminController::class, 'setting'])->name('setting'); 
            

            Route::prefix('me')
                ->name('me')
                ->group(function(){
                    Route::get('/', [AuthController::class, 'edit'])->name('edit');
                    Route::patch('/', [AuthController::class, 'update'])->name('update');
                });
            
            Route::prefix('roles')
                ->name('roles')
                ->group(function(){
                    Route::get('/', [RoleController::class, 'index'])->name('index');
                    Route::get('/create', [RoleController::class, 'create'])->name('create');
                    Route::get('/edit/{id}', [RoleController::class, 'edit'])->name('edit');
                    
                    Route::post('/', [RoleController::class, 'store'])->name('store');
                    Route::put('{id}', [RoleController::class, 'update'])->name('update');
                    Route::delete('{id}', [RoleController::class, 'destroy'])->name('destroy');
                });


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
                    Route::get('/create', [ClaimController::class, 'create'])->name('create');
                    
                    Route::post('/', [ClaimController::class, 'store'])->name('store');
                    Route::put('{id}', [ClaimController::class, 'update'])->name('update');
                    Route::delete('{id}', [ClaimController::class, 'destroy'])->name('destroy');
                });

            Route::prefix('customers')
                ->name('customers')
                ->group(function(){
                    Route::get('/', [CustomerController::class, 'index'])->name('index');
                    Route::get('/create', [CustomerController::class, 'create'])->name('create');
                    
                    Route::post('/', [CustomerController::class, 'store'])->name('store');
                    Route::put('{id}', [CustomerController::class, 'update'])->name('update');
                    Route::delete('{id}', [CustomerController::class, 'destroy'])->name('destroy');
                });

        });

});
