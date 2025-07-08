<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_document_commercial_invoices', function (Blueprint $table) {
            $table->id(); // PK & FK → order_documents.id

            // BILL TO
            $table->string('bill_to_name')->nullable();
            $table->string('bill_to_address')->nullable();
            $table->string('bill_to_tel')->nullable();
            $table->string('bill_to_attn')->nullable();
            $table->string('bill_to_email')->nullable();

            // SHIP TO
            $table->string('ship_to_name')->nullable();
            $table->string('ship_to_address')->nullable();
            $table->string('ship_to_tel')->nullable();
            $table->string('ship_to_attn')->nullable();
            $table->string('ship_to_email')->nullable();

            // 문서 정보
            $table->string('ref_no')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('pi_no')->nullable();
            $table->string('po_no')->nullable();

            // 조건 정보
            $table->string('carrier')->nullable();
            $table->string('estimated_delivery_date')->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('price_terms')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->string('currency', 10)->nullable();

            // 하단 정보 (HS Code 등)
            $table->string('hs_code')->nullable();
            $table->string('dev')->nullable();
            $table->string('lst')->nullable();
            $table->string('ein')->nullable();

            $table->foreign('id')
                ->references('id')
                ->on('order_documents')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('order_document_commercial_invoices');
    }
};
