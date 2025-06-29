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

            $table->foreignId('order_id')
                    ->constrained('orders')
                    ->onDelete('cascade');

            $table->foreignId('product_id')
                    ->constrained('products')
                    ->onDelete('cascade');

            $table->foreignId('parent_id')
                    ->nullable()
                    ->constrained('order_items')
                    ->onDelete('cascade');

            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_price', 12, 2)->default(0);

            $table->text('memo')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('order_items');
    }
};
