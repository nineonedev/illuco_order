<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id(); 
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDeleteCascade();
                
            $table->string('title');
            $table->text('content')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('posts'); 
    }
};
