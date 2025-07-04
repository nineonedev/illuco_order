<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_document_packing_lists', function (Blueprint $table) {
            $table->id(); // PK & FK
            $table->string('packing_list_no')->nullable();
            $table->date('packing_date')->nullable();
            $table->string('buyer_name')->nullable();
            $table->string('box_no')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_model')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('net_weight', 8, 2)->nullable();
            $table->decimal('gross_weight', 8, 2)->nullable();
            $table->string('volume')->nullable();
            $table->text('remarks')->nullable();

            $table->foreign('id')->references('id')->on('order_documents')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('order_document_packing_lists');
    }
};
