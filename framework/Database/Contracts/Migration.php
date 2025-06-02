<?php

namespace Framework\Database\Contracts;

interface Migration
{
    /**
     * 마이그레이션 적용 (테이블 생성 등)
     */
    public function up(): void;

    /**
     * 마이그레이션 롤백 (테이블 삭제 등)
     */
    public function down(): void;
}
