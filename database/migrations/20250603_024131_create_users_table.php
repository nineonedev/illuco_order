<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); 
            $table->string('username')->unique();
            $table->string('name');
            $table->string('password');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('users'); 
    }
};
