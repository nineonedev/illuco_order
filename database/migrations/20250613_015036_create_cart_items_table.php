<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('cart_id')
                    ->constrained('carts')
                    ->onDeleteCascade();
            
            $table->foreignId('product_id')
                    ->constrained('products')
                    ->onDeleteCascade();
            
            $table->unsignedInteger('quantity')->default(1);
            
            $table->unsignedBigInteger('set_group_no')->nullable()->comment('동적 세트 그룹 번호');
            $table->unsignedInteger('set_group_sort')->nullable()->comment('세트 그룹 내 정렬 순서');
            $table->boolean('is_main_item')->default(false)->comment('세트 내 메인 제품 여부');

            $table->boolean('selected')->default(true)->comment('선택 여부');

            $table->timestamps();

            $table->index(['cart_id']);
            $table->index(['product_id']);
            $table->index(['cart_id', 'product_id']);
            $table->index(['set_group_no']);
            $table->index(['set_group_no', 'set_group_sort']);
        });
    }

    public function down(): void
    {
        Schema::drop('cart_items');
    }
};
