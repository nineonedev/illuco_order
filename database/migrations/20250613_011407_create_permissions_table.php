<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id(); 
            $table->string('resource'); // 예: 'users', 'products', 'orders'
            $table->string('action');   // 예: 'create', 'read', 'update', 'delete'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('permissions'); 
    }
};
