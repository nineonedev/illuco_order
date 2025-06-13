<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('serial_numbers', function (Blueprint $table) {
            $table->id(); 
            $table->morphs('serialable');

            $table->string('serial_number')->unique();
            $table->string('prefix_code');
            $table->string('option_code')->nullable();
            $table->string('date_code');
            $table->unsignedInteger('sequence');
            $table->string('revision_code')->nullable();

            $table->timestamp('created_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('serial_numbers'); 
    }
};
