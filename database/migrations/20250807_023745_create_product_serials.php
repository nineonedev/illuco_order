<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('product_serials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->comment('제품 ID');
            $table->string('serial_number')->unique()->comment('시리얼 번호');
            $table->enum('status', ['in_stock', 'reserved', 'sold', 'returned'])
                  ->default('in_stock')
                  ->comment('상태');
            $table->unsignedBigInteger('order_item_id')->nullable()->comment('주문 항목 ID');

            $table->timestamps();

            // Foreign keys
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');

            $table->foreign('order_item_id')
                  ->references('id')
                  ->on('order_items')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_serials');
    }
};
