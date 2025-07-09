<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('order_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDeleteCascade();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDeleteSetNull();
            $table->string('document_no', 50)->unique();
            $table->string('type', 50);
            $table->string('status', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('order_documents'); 
    }
};
