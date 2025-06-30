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
            $table->string('type', 50)->comment('admin, employee, dealer');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->char('gender', 1)->default('U'); // M, F, U
            $table->timestamp('email_verified_at')->nullable();
            $table->date('birth')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('users');
    }
};
