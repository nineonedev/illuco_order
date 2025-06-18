<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('product_templates')->onDeleteCascade();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('model')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->json('option_json')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('products'); 
    }
};
