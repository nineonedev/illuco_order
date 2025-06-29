<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id(); 
            
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDeleteCascade();

            $table->string('type', 100); // 알림 종류
            $table->text('data'); // JSON 형식 내용
            // $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('notifications'); 
    }
};
