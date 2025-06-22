<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('product_values', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('product_id')->constrained('products')->onDeleteCascade();
            $table->foreignId('attribute_id')->constrained('product_attributes')->onDeleteCascade();
            $table->string('attribute_type');
            $table->text('value')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'attribute_id', 'attribute_type']);
        });
    }

    public function down(): void
    {
        Schema::drop('product_values'); 
    }
};
