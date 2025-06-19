<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('product_attribute_template', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('template_id')->constrained('product_templates')->onDeleteCascade();
            $table->foreignId('attribute_id')->constrained('product_attributes')->onDeleteCascade();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('product_attribute_template'); 
    }
};
