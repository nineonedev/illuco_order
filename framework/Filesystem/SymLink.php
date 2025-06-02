<?php 

namespace Framework\Filesystem;

use RuntimeException;

class SymLink
{
    protected string $link; // 사용자 접근 경로
    protected string $target; // 원본

    public function __construct(string $target, string $link)
    {
        $this->target = $target; 
        $this->link = $link; 
    }

    public function create(bool $force = false): void
    {
        if ($this->exists()) {
            if (!$force) {
                throw new RuntimeException("Symbolic link already exists: {$this->link}"); 
            }

            $this->delete(); 
        }

        if (!symlink($this->target, $this->link)) {
            throw new RuntimeException("Failed to create symbolic link: {$this->link} => {$this->target}");
        }
    }

    public function exists(): bool
    {
        return is_link($this->link);
    }

    public function delete(): bool
    {
        if (!$this->exists()) {
            return false;
        } 

        return unlink($this->link); 
    }

    public function link(): string
    {
        return $this->link; 
    }
    
    public function target(): string
    {
        if (!$this->exists()) {
            throw new RuntimeException("Link does not exist: {$this->link}"); 
        }

        // readlink는 읽은 링크의 원본 경로를 반환함
        return readlink($this->link); 
    }

    public function isBroken(): bool
    {
        return $this->exists() && !file_exists($this->target()); 
    }
}