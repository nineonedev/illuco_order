<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_document_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained('order_documents')->onDeleteCascade();
            $table->foreignId('attribute_id')->constrained('order_document_attributes')->onDeleteCascade();
            $table->text('value')->nullable(); // 자유 입력값
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('order_document_values'); 
    }
};
