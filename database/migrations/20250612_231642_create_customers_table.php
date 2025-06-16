<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('country');
            $table->string('name');
            $table->string('phone_number');
            $table->string('address')->nullable();
            $table->text('description')->nullable();
        });
    }

    public function down(): void
    {
        Schema::drop('customers'); 
    }
};
