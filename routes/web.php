<?php

use App\Http\Controllers\ClaimController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductAttributeController;
use App\Http\Controllers\ProductOptionController;
use App\Http\Controllers\ProductTemplateController;
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
        Route::get('signup', [AuthController::class, 'signup'])->name('signup');
        
        Route::post('signin', [AuthController::class, 'login'])->name('login');
        Route::post('signup', [AuthController::class, 'register'])->name('register');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');

        Route::put('{id}', [AuthController::class, 'update'])->name('update');
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
            
            Route::post('upload', [AdminController::class, 'upload'])->name('upload'); 

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
                    Route::get('{id}/edit', [RoleController::class, 'edit'])->name('edit');
                    
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
                    Route::get('{id}/edit', [ClaimController::class, 'edit'])->name('edit');
                    
                    Route::post('/', [ClaimController::class, 'store'])->name('store');
                    Route::put('{id}', [ClaimController::class, 'update'])->name('update');
                    Route::delete('{id}', [ClaimController::class, 'destroy'])->name('destroy');
                });

            Route::prefix('customers')
                ->name('customers')
                ->group(function(){
                    Route::get('/', [CustomerController::class, 'index'])->name('index');
                    Route::get('/create', [CustomerController::class, 'create'])->name('create');
                    Route::get('{id}/edit', [CustomerController::class, 'edit'])->name('edit');
                    
                    Route::post('/', [CustomerController::class, 'store'])->name('store');
                    Route::put('{id}', [CustomerController::class, 'update'])->name('update');
                    Route::delete('{id}', [CustomerController::class, 'destroy'])->name('destroy');
                });

            Route::prefix('dealers')
                ->name('dealers')
                ->group(function(){
                    Route::get('/', [DealerController::class, 'index'])->name('index');
                    Route::get('/create', [DealerController::class, 'create'])->name('create');
                    Route::get('/edit/{id}', [DealerController::class, 'edit'])->name('edit');
                    
                    Route::post('/', [DealerController::class, 'store'])->name('store');
                    Route::put('{id}', [DealerController::class, 'update'])->name('update');
                    Route::delete('{id}', [DealerController::class, 'destroy'])->name('destroy');
                });

            Route::prefix('employees')
                ->name('employees')
                ->group(function(){
                    Route::get('/', [EmployeeController::class, 'index'])->name('index');
                    Route::get('/create', [EmployeeController::class, 'create'])->name('create');
                    Route::get('/edit/{id}', [EmployeeController::class, 'edit'])->name('edit');
                    
                    Route::post('/', [EmployeeController::class, 'store'])->name('store');
                    Route::put('{id}', [EmployeeController::class, 'update'])->name('update');
                    Route::delete('{id}', [EmployeeController::class, 'destroy'])->name('destroy');
                });

            Route::prefix('product-templates')
                ->name('product_templates')
                ->group(function(){
                    Route::get('/', [ProductTemplateController::class, 'index'])->name('index');
                    Route::get('/create', [ProductTemplateController::class, 'create'])->name('create');
                    Route::get('{id}/edit', [ProductTemplateController::class, 'edit'])->name('edit');
                    Route::get('{id}', [ProductTemplateController::class, 'show'])->name('show');
                    
                    Route::post('/', [ProductTemplateController::class, 'store'])->name('store');
                    Route::put('{id}', [ProductTemplateController::class, 'update'])->name('update');
                    Route::delete('{id}', [ProductTemplateController::class, 'destroy'])->name('destroy');
                });

            Route::prefix('product-attributes')
                ->name('product_attributes')
                ->group(function(){
                    Route::get('/{id}', [ProductAttributeController::class, 'show'])->name('show');

                    Route::post('/', [ProductAttributeController::class, 'store'])->name('store');
                    Route::put('{id}', [ProductAttributeController::class, 'update'])->name('update');
                    Route::delete('{id}', [ProductAttributeController::class, 'destroy'])->name('destroy');
                });

            Route::prefix('product-options')
                ->name('product_options')
                ->group(function(){
                    Route::post('/', [ProductOptionController::class, 'store'])->name('store');
                    Route::put('{id}', [ProductOptionController::class, 'update'])->name('update');
                    Route::delete('{id}', [ProductOptionController::class, 'destroy'])->name('destroy');
                });

        });

});
