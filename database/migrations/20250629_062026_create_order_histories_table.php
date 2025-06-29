<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_histories', function (Blueprint $table) {
            $table->id(); 
            
            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            $table->foreignId('dealer_id')
                ->nullable()
                ->constrained('dealers')
                ->onDelete('set null');

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->onDelete('set null');

            $table->foreignId('created_by')
                ->constrained('users')
                ->onDelete('set null');

            $table->text('memo');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('order_histories'); 
    }
};
