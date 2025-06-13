<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id(); 
            $table->morphs('orderer');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('carts'); 
    }
};
