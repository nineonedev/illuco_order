<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_receivables', function (Blueprint $table) {
            $table->id(); 
            
            $table->foreignId('dealer_id')
                ->nullable()
                ->constrained('dealers')
                ->onDelete('cascade');

            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('customers')
                ->onDelete('cascade');

            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->onDelete('cascade');
            
            $table->decimal('balance', 15, 2)
                ->default(0)
                ->comment('미수금 잔액');

            $table->text('memo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('order_receivables'); 
    }
};
