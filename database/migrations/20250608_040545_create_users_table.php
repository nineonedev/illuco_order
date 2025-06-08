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
            $table->string('name');
            $table->string('username')->nullable();
            $table->string('email')->unique();
            $table->string('password');

            $table->date('birth')->nullable();
            $table->enum('gender', ['M', 'F', 'U'])->nullable();
            $table->string('remember_token')->nullable();
            
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->unsignedInteger('login_count')->default(0); 
            $table->string('login_ip')->nullable();  
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('users');
    }
};
