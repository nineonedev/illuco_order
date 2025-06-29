<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('dealers', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('country');
            $table->string('code');
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->text('memo')->nullable();
            $table->boolean('use_default_memo')->default(false);
            
            $table->foreign('id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('dealers'); 
    }
};
