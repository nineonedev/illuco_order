<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id(); 
            $table->string('admin_key')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('admins'); 
    }
};
