<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('product_validations', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('template_id')->constrained('product_templates')->onDeleteCascade();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('product_validations'); 
    }
};
