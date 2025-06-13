<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('file_attachments', function (Blueprint $table) {
            $table->id();
            $table->morphs('file_attachable');
            $table->string('original_name')->nullable();
            $table->string('name')->nullable();
            $table->string('mime_type')->nullable();
            $table->integer('size')->nullable();
            $table->string('path')->nullable();
            $table->string('extension', 20);
            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::drop('file_attachments');
    }
};
