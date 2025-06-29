<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_document_product_requests', function (Blueprint $table) {
            $table->id(); // PK & FK
            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_model')->nullable();
            $table->string('box_size')->nullable();
            $table->text('memo')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('total_qty')->nullable();
            $table->text('remarks')->nullable();

            $table->foreign('id')->references('id')->on('order_documents')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::drop('order_document_product_requests');
    }
};
