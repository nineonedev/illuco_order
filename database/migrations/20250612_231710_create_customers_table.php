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
            $table->foreignId('user_id')->nullable()->constrained('users')->onDeleteSetNull();
            $table->foreignId('dealer_id')->nullable()->constrained('users')->onDeleteSetNull();
            $table->string('name');
            $table->string('country')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('customers'); 
    }
};
