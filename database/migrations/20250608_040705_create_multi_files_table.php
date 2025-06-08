<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('multi_files', function (Blueprint $table) {
            $table->id();
            $table->morphs();
            $table->foreignId('file_id')->constrained('files')->onDeleteCascade();
            $table->integer('order')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['morph_type', 'morph_id', 'file_id']);
        });
    }

    public function down(): void
    {
        Schema::drop('multi_files');
    }
};
