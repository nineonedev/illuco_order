<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->morphs('writer');
            $table->string('title');
            $table->text('content');

            $table->dateTime('visible_from')->nullable();
            $table->dateTime('visible_to')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->string('status')->default('draft'); // published, archived 등

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('notices'); 
    }
};
