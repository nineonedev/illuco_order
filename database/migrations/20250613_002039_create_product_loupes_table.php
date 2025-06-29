<?php

use Framework\Database\Contracts\Migration;
use Framework\Database\Schema\Blueprint;
use Framework\Support\Facades\Schema;

return new class implements Migration
{
    public function up(): void
    {
        Schema::create('product_loupes', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('type', 50)->comment('ready-made or custom-made');

            // 기타
            $table->string('flip_up_color')->nullable();
            $table->string('working_distance')->nullable();

            // 시력 정보 (OD / OS)
            $table->decimal('od_sph', 4, 2)->nullable();
            $table->decimal('os_sph', 4, 2)->nullable();

            $table->decimal('od_cyl', 4, 2)->nullable();
            $table->decimal('os_cyl', 4, 2)->nullable();

            $table->integer('od_axis')->nullable();
            $table->integer('os_axis')->nullable();
            
            $table->decimal('od_add', 4, 2)->nullable();
            $table->decimal('os_add', 4, 2)->nullable();

            // PD 정보
            $table->decimal('pd_right', 4, 1)->nullable();
            $table->decimal('pd_left', 4, 1)->nullable();
            $table->decimal('pd_total', 4, 1)->nullable();


            $table->foreign('id')
                ->references('id')
                ->on('products')
                ->onDeleteCascade();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('product_loupes');
    }
};
