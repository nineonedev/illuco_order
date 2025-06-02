<?php

use Framework\Filesystem\DiskManager;
use Framework\Filesystem\File;
use Framework\Filesystem\Disk;

// 디스크 매니저 반환
if (!function_exists('disk_manager')) {
    function disk_manager(): DiskManager
    {
        return app(DiskManager::class);
    }
}

// 디스크 인스턴스 반환
if (!function_exists('disk')) {
    function disk(?string $name = null): Disk
    {
        return disk_manager()->disk($name);
    }
}

// 파일 시스템 헬퍼 직접 접근
if (!function_exists('file_helper')) {
    function file_helper(): File
    {
        return app(File::class);
    }
}

// 특정 디스크에 파일 저장
if (!function_exists('file_put')) {
    function file_put(string $path, string $content, bool $lock = false, ?string $disk = null): int
    {
        return disk($disk)->put($path, $content, $lock);
    }
}

// 특정 디스크에서 파일 내용 읽기
if (!function_exists('file_get')) {
    function file_get(string $path, ?string $disk = null): string
    {
        return disk($disk)->get($path)->contents();
    }
}

// 특정 디스크에서 파일 존재 여부 확인
if (!function_exists('file_exists_on_disk')) {
    function file_exists_on_disk(string $path, ?string $disk = null): bool
    {
        return disk($disk)->has($path);
    }
}

// 특정 디스크에서 파일 삭제
if (!function_exists('file_delete')) {
    function file_delete(string $path, ?string $disk = null): bool
    {
        return disk($disk)->delete($path);
    }
}

// 업로드된 $_FILES에서 UploadedFile 인스턴스 생성
if (!function_exists('uploaded_file')) {
    function uploaded_file(array $file): \Framework\Filesystem\UploadedFile
    {
        return \Framework\Filesystem\UploadedFile::createFromGlobal($file);
    }
}
