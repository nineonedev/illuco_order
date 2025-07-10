<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('product_headlights', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('type');
            $table->string('wireless_color')->nullable();
            $table->string('engraving_text')->nullable();

            $table->foreign('id')
                ->references('id')
                ->on('products')
                ->onDeleteCascade();
                
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('product_headlights'); 
    }
};
