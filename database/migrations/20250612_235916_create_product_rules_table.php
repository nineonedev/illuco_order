<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('product_rules', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('validation_id')->constrained('product_validations')->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained('product_attributes')->onDelete('cascade');
            $table->string('operator', 20); // 예: '=', '>=', 'in'
            $table->text('value')->nullable(); // 비교값
            $table->text('message')->nullable(); // 실패 시 메세지
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('product_rules'); 
    }
};
