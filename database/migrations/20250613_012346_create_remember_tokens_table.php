<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('remember_tokens', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('session_id')->constrained('sessions')->onDeleteCascade();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('remember_tokens'); 
    }
};
