<?php

use App\Domains\Order\Entities\Order;
use App\Domains\Order\Enums\OrderStatus;
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
            $table->foreignId('dealer_id')->nullable()->constrained('dealers')->onDeleteSetNull();

            $table->string('orderer_name');
            $table->string('orderer_email')->nullable();
            $table->string('orderer_phone')->nullable();
            
            $table->string('order_no', 50)->unique();
            $table->string('order_status')->default(OrderStatus::NEW);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('memo')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('orders'); 
    }
};
