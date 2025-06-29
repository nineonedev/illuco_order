<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('serial_numbers', function (Blueprint $table) {
            $table->id();

            $table->string('serial_no', 50)->unique();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('model_prefix', 20);
            $table->string('special_prefix', 20)->default('NNN');
            $table->char('year', 2);
            $table->unsignedInteger('sequence');
            $table->char('revision', 2)->default('A');
            $table->timestamps();
            $table->index(['model_prefix', 'year']);
        });
    }

    public function down(): void
    {
        Schema::drop('serial_numbers');
    }
};


// Tracking / ERP 관리용 컬럼들
// $table->date('warranty_start_date')
// $table->date('warranty_end_date')->nullable();
// $table->string('status', 50)->default('in_stock');
// $table->string('location', 100)->nullable();
// $table->foreignId('owner_customer_id')->nullable()->constrained('customers')->onDelete('set null');
// $table->string('asset_tag', 50)->nullable();
// $table->text('remark')->nullable();->nullable();
