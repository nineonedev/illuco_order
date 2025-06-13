<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id(); 
            $table->timestamp('used_at')->nullable(); // 사용 여부 판단
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('password_reset_tokens'); 
    }
};
