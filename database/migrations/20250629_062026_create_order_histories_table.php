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
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            $table->decimal('balance', 15, 2)
                ->default(0)
                ->comment('미수금 변동액');

            $table->boolean('settled')
                ->default(false)
                ->comment('이 히스토리가 처리 완료되었는지 여부');

            $table->text('memo')
                ->nullable()
                ->comment('고정 노트 또는 기타 메모');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('order_histories');
    }
};
