<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDeleteSetNull();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDeleteSetNull();
            $table->string('orderer_name');
            $table->string('orderer_email')->nullable();
            $table->string('orderer_phone')->nullable();
            
            $table->text('memo')->nullable();
            $table->string('order_status')->default('pending');
            $table->unsignedInteger('total_amount')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('orders'); 
    }
};
