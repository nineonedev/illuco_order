<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_document_packing_lists', function (Blueprint $table) {
            $table->id(); // PK & FK → order_documents.id

            // Header 정보
            $table->string('bill_to_name')->nullable();
            $table->string('bill_to_address')->nullable();
            $table->string('bill_to_tel')->nullable();
            $table->string('bill_to_attn')->nullable();
            $table->string('bill_to_email')->nullable();

            $table->string('ship_to_name')->nullable();
            $table->string('ship_to_address')->nullable();
            $table->string('ship_to_tel')->nullable();
            $table->string('ship_to_attn')->nullable();
            $table->string('ship_to_email')->nullable();

            $table->string('ref_no')->nullable();
            $table->date('packing_date')->nullable();
            $table->string('pi_no')->nullable();
            $table->string('po_no')->nullable();

            $table->string('carrier')->nullable();
            $table->string('estimated_delivery_date')->nullable();
            $table->string('payment_terms')->nullable();
            $table->string('price_terms')->nullable();
            $table->string('country_of_origin')->nullable();

            // Footer 정보
            $table->text('packing_details')->nullable();
            $table->string('hs_code')->nullable();

            // Totals
            $table->integer('total_cartons')->nullable();
            $table->integer('total_quantity')->nullable();
            $table->decimal('total_weight', 10, 2)->nullable();
            $table->decimal('total_volume_cbm', 10, 5)->nullable();

            $table->foreign('id')
                ->references('id')
                ->on('order_documents')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('order_document_packing_lists');
    }
};
