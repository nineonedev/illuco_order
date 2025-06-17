<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_document_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('order_document_templates')->onDeleteCascade();
            $table->string('name');         // 필드명
            $table->string('label')->nullable();  // 표시용
            $table->string('type')->default('text'); // 예: text, number, date, select
            $table->boolean('required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('order_document_values'); 
    }
};
