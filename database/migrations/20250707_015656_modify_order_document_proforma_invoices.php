<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::table('order_document_proforma_invoices', function (Blueprint $table) {
            // ✅ 기존 컬럼 제거
            $table->dropColumn(
                'item_name',
                'item_model',
                'unit_price',
                'quantity',
                'amount',
                'remarks',
            );

            // ✅ 새 컬럼 추가
            $table->string('document_no')->nullable()->after('id');
            $table->string('purchase_order_no')->nullable()->after('document_no');

            $table->string('buyer_tel')->nullable()->after('buyer_address');
            $table->string('buyer_attn')->nullable()->after('buyer_tel');
            $table->string('buyer_email')->nullable()->after('buyer_attn');

            $table->string('country_of_origin')->nullable()->after('buyer_email');
            $table->string('currency', 20)->nullable()->after('country_of_origin');

            $table->string('salesperson_name')->nullable()->after('currency');
            $table->string('salesperson_tel')->nullable()->after('salesperson_name');
            $table->string('salesperson_email')->nullable()->after('salesperson_tel');

            $table->string('estimated_date_of_delivery')->nullable()->after('salesperson_email');
            $table->string('price_terms')->nullable()->after('estimated_date_of_delivery');
            $table->string('payment_terms')->nullable()->after('price_terms');
            $table->string('shipment_by')->nullable()->after('payment_terms');

            $table->string('hs_code')->nullable()->after('shipment_by');
            $table->decimal('freight_charge', 12, 2)->nullable()->after('hs_code');

            $table->string('bank_beneficiary')->nullable()->after('freight_charge');
            $table->string('bank_name')->nullable()->after('bank_beneficiary');
            $table->string('bank_address')->nullable()->after('bank_name');
            $table->string('bank_swift_code')->nullable()->after('bank_address');
            $table->string('bank_account_no')->nullable()->after('bank_swift_code');

            $table->text('note')->nullable()->after('bank_account_no');
        });
    }

    public function down(): void
    {
        Schema::table('order_document_proforma_invoices', function (Blueprint $table) {
            // ✅ 새로 추가한 컬럼 제거
            $table->dropColumn(
                'document_no',
                'purchase_order_no',
                'buyer_tel',
                'buyer_attn',
                'buyer_email',
                'country_of_origin',
                'currency',
                'salesperson_name',
                'salesperson_tel',
                'salesperson_email',
                'estimated_date_of_delivery',
                'price_terms',
                'payment_terms',
                'shipment_by',
                'hs_code',
                'freight_charge',
                'bank_beneficiary',
                'bank_name',
                'bank_address',
                'bank_swift_code',
                'bank_account_no',
                'note'
            );

            // ✅ 기존 컬럼 복구
            $table->string('item_name')->nullable();
            $table->string('item_model')->nullable();
            $table->decimal('unit_price', 10, 2)->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->text('remarks')->nullable();
        });
    }
};
