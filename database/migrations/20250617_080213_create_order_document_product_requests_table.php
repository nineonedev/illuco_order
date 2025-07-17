<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_document_product_requests', function (Blueprint $table) {
            $table->id(); // PK & FK → order_documents.id

            // Header
            $table->string('country')->nullable();
            $table->string('customer_name')->nullable();
            $table->date('created_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->string('manager_name')->nullable();
            $table->string('document_no')->nullable();

            for ($i = 1; $i <= 16; $i++) {
                $table->string("item{$i}_no")->nullable();
            }

            // 박스 정보 (최대 5개)
            for ($i = 1; $i <= 5; $i++) {
                $table->string("box{$i}_no")->nullable();
                $table->string("box{$i}_weight")->nullable();
                $table->string("box{$i}_size")->nullable();
            }

            // 메모
            $table->text('note')->nullable();

            $table->foreign('id')
                ->references('id')
                ->on('order_documents')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('order_document_product_requests');
    }
};
