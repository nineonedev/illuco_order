<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
                
            $table->string('previous_status')->nullable()
                ->comment('변경 전 상태');

            $table->string('status')
                ->comment('변경 후 상태');

            $table->text('message')
                ->nullable()
                ->comment('변경 사유 등 추가 메모');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('order_logs');
    }
};
