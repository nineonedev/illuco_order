<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id(); 
            
            // 스냅샷: 제품 정보
            $table->string('product_name');
            $table->string('product_code');
            $table->string('product_model');

            // 관계
            $table->unsignedBigInteger('template_id')->nullable(); // 견본 기준
            $table->unsignedBigInteger('user_id')->nullable();     // 작성자

            $table->string('claimer_type'); // 고객/대리점/직원 등
            $table->unsignedBigInteger('claimer_id');

            // 고객 입력 정보 (스냅샷)
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');

            // 제품 정보
            $table->string('serial_number');
            $table->text('description')->nullable();

            // 상태
            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('claims'); 
    }
};
