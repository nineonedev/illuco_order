<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Communication\SalesInfoController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\Order\OrderDocumentController;
use App\Http\Controllers\Product\CategoryController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Communication\ClaimController;
use App\Http\Controllers\Communication\NoticeController;
use App\Http\Controllers\DealerMemoController;
use App\Http\Controllers\DealerPriceController;
use App\Http\Controllers\Order\CartController;
use App\Http\Controllers\Order\CartItemController;
use App\Http\Controllers\Order\CustomerController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Order\OrderHistoryController;
use App\Http\Controllers\Order\OrderItemController;
use App\Http\Controllers\Product\ProductTemplateController;
use Framework\Support\Facades\Route;

Route::middleware(['web'])->group(function(){

    // ============================================================================================================
    // home 
    // ============================================================================================================
    Route::get('/', [AdminController::class, 'home'])->name('home');

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
            
            Route::prefix('dashboard')
                ->name('dashboard')
                ->group(function() {
                    Route::get('aggregation', [AdminController::class, 'aggregateApi'])->name('aggregation');
            });

            Route::prefix('salesinfo')
                ->name('salesinfo')
                ->group(function(){
                    Route::get('/', [SalesInfoController::class, 'index'])->name('index');
                    Route::post('/save', [SalesInfoController::class, 'save'])->name('save');
                });
                

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

                    Route::delete('bulk-delete', [NoticeController::class, 'destroyMany'])->name('destroyMany');
                    Route::delete('{id}', [NoticeController::class, 'destroy'])->name('destroy');
                });

            Route::prefix('claims')
                ->name('claims')
                ->group(function(){
                    Route::get('/', [ClaimController::class, 'index'])->name('index');
                    Route::get('/create', [ClaimController::class, 'create'])->name('create');
                    Route::get('/edit/{id}', [ClaimController::class, 'edit'])->name('edit');
                    Route::get('{id}', [ClaimController::class, 'show'])->name('show');
                    
                    Route::post('/', [ClaimController::class, 'store'])->name('store');
                    Route::put('{id}', [ClaimController::class, 'update'])->name('update');
                    
                    Route::delete('bulk-delete', [ClaimController::class, 'destroyMany'])->name('destroyMany');
                    Route::delete('{id}', [ClaimController::class, 'destroy'])->name('destroy');
                });

            Route::prefix('customers')
                ->name('customers')
                ->group(function(){
                    Route::get('/', [CustomerController::class, 'index'])->name('index');
                    Route::get('/create', [CustomerController::class, 'create'])->name('create');
                    Route::get('{id}/edit', [CustomerController::class, 'edit'])->name('edit');
                    Route::get('{id}', [CustomerController::class, 'show'])->name('show');
                    
                    Route::post('/', [CustomerController::class, 'store'])->name('store');
                    Route::put('{id}', [CustomerController::class, 'update'])->name('update');

                    Route::delete('bulk-delete', [CustomerController::class, 'destroyMany'])->name('destroyMany');
                    Route::delete('{id}', [CustomerController::class, 'destroy'])->name('destroy');
                });

            Route::prefix('dealer-memo')
                ->name('dealer-memo.')
                ->group(function(){
                    Route::get('{id}/edit', [DealerMemoController::class, 'edit'])->name('edit');
                    Route::post('{id}', [DealerMemoController::class, 'save'])->name('save'); 
                });

            Route::prefix('dealer-price')
                ->name('dealer-price.')
                ->group(function(){
                    Route::get('{id}/edit', [DealerPriceController::class, 'edit'])->name('edit');
                    Route::post('{id}', [DealerPriceController::class, 'save'])->name('save'); 
                    Route::delete('{id}', [DealerPriceController::class, 'destroy'])->name('destroy'); 
                });

            Route::prefix('dealers')
                ->name('dealers.')
                ->group(function () {
                    // 목록/생성/수정 화면
                    Route::get('/',            [DealerController::class, 'index'])->name('index');
                    Route::get('/create',      [DealerController::class, 'create'])->name('create');
                    Route::get('/edit/{id}',   [DealerController::class, 'edit'])->name('edit');

                    // 저장/수정
                    Route::post('/',           [DealerController::class, 'store'])->name('store');
                    Route::put('{id}',         [DealerController::class, 'update'])->name('update');

                    // 비밀번호 변경
                    Route::put('{id}/password', [DealerController::class, 'updatePassword'])->name('password.update');

                    // 임시 비밀번호 발급 / 회수
                    Route::post('{id}/temp-password',   [DealerController::class, 'issueTempPassword'])
                        ->name('temp-password.issue');
                    Route::delete('{id}/temp-password', [DealerController::class, 'revokeTempPassword'])
                        ->name('temp-password.revoke');

                    // 삭제
                    Route::delete('bulk-delete', [DealerController::class, 'destroyMany'])->name('destroyMany');
                    Route::delete('{id}',        [DealerController::class, 'destroy'])->name('destroy');
                });



            Route::prefix('employees')
                ->name('employees')
                ->group(function(){
                    Route::get('/', [EmployeeController::class, 'index'])->name('index');
                    Route::get('/create', [EmployeeController::class, 'create'])->name('create');
                    Route::get('/edit/{id}', [EmployeeController::class, 'edit'])->name('edit');
                    
                    Route::post('/', [EmployeeController::class, 'store'])->name('store');
                    Route::put('{id}', [EmployeeController::class, 'update'])->name('update');

                    Route::delete('bulk-delete', [EmployeeController::class, 'destroyMany'])->name('destroyMany');
                    Route::delete('{id}', [EmployeeController::class, 'destroy'])->name('destroy');
                });
    
            Route::prefix('product-templates')
                ->name('product_templates')
                ->group(function(){
                    Route::get('/', [ProductTemplateController::class, 'index'])->name('index');
                    Route::get('/create', [ProductTemplateController::class, 'create'])->name('create');
                    Route::get('/options', [ProductTemplateController::class, 'options'])->name('options');
                    Route::get('/attributes', [ProductTemplateController::class, 'attributes'])->name('attributes');
                    Route::get('/labels', [ProductTemplateController::class, 'labels'])->name('labels');
                    Route::get('/set-group-items', [ProductTemplateController::class, 'setGroupItems'])->name('setGroupItems');

                    Route::get('{id}/edit', [ProductTemplateController::class, 'edit'])->name('edit');
                    Route::get('{id}', [ProductTemplateController::class, 'show'])->name('show');
                    
                    Route::post('/', [ProductTemplateController::class, 'store'])->name('store');
                    Route::put('{id}', [ProductTemplateController::class, 'update'])->name('update');

                    Route::delete('bulk-delete', [ProductTemplateController::class, 'destroyMany'])->name('destroyMany');
                    Route::delete('{id}', [ProductTemplateController::class, 'destroy'])->name('destroy');
                });
            
            Route::prefix('product-categories')
                ->name('product_categories')
                ->group(function(){
                    Route::get('/', [CategoryController::class, 'index'])->name('index');
                    Route::post('/', [CategoryController::class, 'store'])->name('store');
                    Route::put('{id}', [CategoryController::class, 'update'])->name('update');
                    Route::delete('{id}', [CategoryController::class, 'destroy'])->name('destroy');
                });

            // Route::prefix('product-options')
            //     ->name('product_options')
            //     ->group(function(){
            //         Route::post('/', [ProductOptionController::class, 'store'])->name('store');
            //         Route::put('{id}', [ProductOptionController::class, 'update'])->name('update');
            //         Route::delete('{id}', [ProductOptionController::class, 'destroy'])->name('destroy');
            //     });

            // carts
            Route::prefix('cart')
                ->name('cart')
                ->group(function(){
                    Route::get('/', [CartController::class, 'index'])->name('index');
                    Route::post('/', [CartController::class, 'store'])->name('store');
                });

            Route::prefix('cartitems')
            ->name('cartitems')
            ->group(function(){
                Route::get('{id}', [CartItemController::class, 'show'])->name('show');
                Route::put('/', [CartItemController::class, 'updateMany'])->name('updateMany');
                Route::put('{id}', [CartItemController::class, 'update'])->name('update');
                
                Route::delete('/', [CartItemController::class, 'destroyMany'])->name('destroyMany');
                Route::delete('{id}', [CartItemController::class, 'destroy'])->name('destroy');
            });

            // orders
            Route::prefix('orders')
                ->name('orders')
                ->group(function(){
                    Route::get('/', [OrderController::class, 'index'])->name('index');
                    Route::get('edit/{orderNo}', [OrderController::class, 'edit'])->name('edit');
                    Route::get('export', [OrderController::class, 'export'])->name('export');
                    Route::get('serial', [OrderController::class, 'serial'])->name('serial');
                    Route::get('{orderNo}', [OrderController::class, 'show'])->name('show');


                    Route::post('/', [OrderController::class, 'store'])->name('store');
                    Route::post('/restore-all/{orderId}', [OrderController::class, 'restoreAll'])->name('restore_all');
                    Route::post('/restore/{orderItemId}', [OrderController::class, 'restoreItem'])->name('restore_item');
                    
                    Route::put('/cancel/{orderNo}', [OrderController::class, 'cancel'])->name('cancel');
                    Route::put('{id}', [OrderController::class, 'update'])->name('update');
                    
                    Route::delete('bulk-delete', [OrderController::class, 'destroyMany'])->name('destroyMany');
                    Route::delete('{id}', [OrderController::class, 'destroy'])->name('destroy');

                });

            Route::prefix('orderitems')
                ->name('orderitems')
                ->group(function(){
                    Route::delete('{id}', [OrderItemController::class, 'destroy'])->name('destroy');
                });

            Route::prefix('order-documents')
                ->name('order_documents')
                ->group(function(){
                    Route::get('print/{documentNo}', [OrderDocumentController::class, 'print'])->name('print');
                    Route::get('edit/{documentNo}', [OrderDocumentController::class, 'edit'])->name('edit');
                    Route::get('download/{documentNo}', [OrderDocumentController::class, 'download'])->name('download');
                    Route::get('{documentNo}', [OrderDocumentController::class, 'show'])->name('show');
                    Route::put('{id}', [OrderDocumentController::class, 'update'])->name('update'); 
                });

            // order-document-histories
            Route::prefix('order-histories')
                ->name('order_histories')
                ->group(function(){
                    Route::get('{orderId}', [OrderHistoryController::class, 'index'])->name('index');
                    Route::post('/', [OrderHistoryController::class, 'store'])->name('store');

                    Route::put('{id}', [OrderHistoryController::class, 'update'])->name('update');
                    Route::delete('{id}', [OrderHistoryController::class, 'destroy'])->name('destroy');
                });

            // product-values
            // Route::prefix('product-values')
            //     ->name('product_values')
            //     ->group(function(){
            //         Route::post('/', [ProductValueController::class, 'store'])->name('store');
            //         Route::put('{id}', [ProductValueController::class, 'update'])->name('update');
            //         Route::delete('{id}', [ProductValueController::class, 'destroy'])->name('destroy');
            //     });


        });

});
