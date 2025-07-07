<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_document_proforma_invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // PK & FK to order_documents.id

            $table->string('document_no')->nullable(); // Ref No.
            $table->string('purchase_order_no')->nullable(); // P.O. No.

            $table->string('invoice_no')->nullable();
            $table->date('invoice_date')->nullable();

            $table->string('buyer_name')->nullable();
            $table->string('buyer_address')->nullable();
            $table->string('buyer_tel')->nullable();
            $table->string('buyer_attn')->nullable();
            $table->string('buyer_email')->nullable();

            $table->string('country_of_origin')->nullable();
            $table->string('currency', 20)->nullable();

            $table->string('salesperson_name')->nullable();
            $table->string('salesperson_tel')->nullable();
            $table->string('salesperson_email')->nullable();

            $table->string('estimated_date_of_delivery')->nullable();
            $table->string('price_terms')->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('shipment_by')->nullable();

            $table->string('hs_code')->nullable();
            $table->decimal('freight_charge', 12, 2)->nullable();

            // Bank details
            $table->string('bank_beneficiary')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_address')->nullable();
            $table->string('bank_swift_code')->nullable();
            $table->string('bank_account_no')->nullable();

            $table->text('note')->nullable();

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
