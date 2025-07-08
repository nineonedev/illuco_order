<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->string('set_group_id')->nullable()->comment('동적 세트 그룹 번호');
            $table->unsignedInteger('set_group_sort')->nullable()->comment('세트 그룹 내 정렬 순서');
            $table->boolean('is_main_item')->default(false)->comment('세트 내 메인 제품 여부');

            $table->foreignId('order_id')
                    ->constrained('orders')
                    ->onDeleteCascade();

            $table->foreignId('product_id')
                    ->constrained('products')
                    ->onDeleteCascade();

            $table->unsignedInteger('quantity')->default(1);
            $table->string('box_no')->nullable();
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);
            
            $table->softDeletes();
            $table->timestamps();

            $table->index(['order_id']);
            $table->index(['product_id']);
            $table->index(['order_id', 'product_id']);
            $table->index(['set_group_id']);
            $table->index(['set_group_id', 'set_group_sort']);

        });
    }

    public function down(): void
    {
        Schema::drop('order_items');
    }
};
