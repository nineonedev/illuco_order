<?php 

namespace Framework\Filesystem;

use RuntimeException;

class File 
{
    public function get(string $path): string
    {
        if (!file_exists($path)) {
            throw new RuntimeException("File does not exist at path [$path]"); 
        }

        return file_get_contents($path); 
    }

    public function put(string $path, string $contents, bool $lock = false): int
    {
        // lock은 동시성 이슈 방지, true면 순서대로 실행됨
        return file_put_contents($path, $contents, $lock ? LOCK_EX: 0);
    }
    
    function exists(string $path): bool
    {
        return file_exists($path); 
    }

    public function delete(string $path): bool
    {
        return file_exists($path) ? unlink($path) : false; 
    }

    public function size(string $path): int
    {
        if (!file_exists($path)) {
            throw new RuntimeException("Cannot determine size. File does not exist at path [$path]"); 
        }

        return filesize($path); 
    }

    public function mimeType(string $path): string
    {
        if (!file_exists($path)) {
            throw new RuntimeException("Cannot determine MIME type. File doest not exist at path [$path]"); 
        }

        return mime_content_type($path); 
    }
}