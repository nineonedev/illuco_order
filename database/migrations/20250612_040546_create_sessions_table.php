<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64)->unique(); // 쿠키에 저장됨
            $table->foreignId('user_id')->nullable()->constrained('users')->onDeleteCascade();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->longText('payload');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('sessions');
    }
};
