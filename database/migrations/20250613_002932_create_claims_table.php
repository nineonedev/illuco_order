<?php

use App\Domains\Communication\Enums\ClaimStatus;
use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id(); 
            
            // 스냅샷: 제품 정보
            $table->string('product_name');
            $table->string('product_code');
            $table->string('product_model');
            $table->string('product_serial_number');
            $table->text('product_description')->nullable();

            // 관계
            $table->foreignId('user_id')->nullable()->constrained('users')->onDeleteCascade();
            $table->foreignId('delaer_id')->nullable()->constrained('dealers')->onDeleteSetNull();

            // 고객 입력 정보 (스냅샷)
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');

            // 상태
            $table->string('status')->default(ClaimStatus::RECEIVED);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('claims'); 
    }
};
