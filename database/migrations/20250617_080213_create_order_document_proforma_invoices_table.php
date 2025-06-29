<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_document_proforma_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // PK & FK
            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('buyer_name')->nullable();
            $table->string('buyer_address')->nullable();
            $table->string('item_name')->nullable();
            $table->string('item_model')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->text('remarks')->nullable();

            $table->foreign('id')
                ->references('id')
                ->on('order_documents')
                ->onDeleteCascade();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('order_document_proforma_invoices');
    }
};
