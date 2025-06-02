<?php 

namespace Framework\Filesystem;

class DiskFile
{
    protected string $path; 
    protected File $file; 

    public function __construct(string $path, ?File $file)
    {
        $this->path = $path;
        $this->file = $file; 
    }

    public function name(): string
    {
        return basename($this->path); 
    }

    public function path(): string
    {
        return $this->path;
    }

    public function size(): int
    {
        return $this->file->size($this->path); 
    }

    public function mimeType(): string
    {
        return $this->file->mimeType($this->path); 
    }

    public function contents(): string
    {
        return $this->file->get($this->path); 
    }

    public function delete(): bool
    {
        return $this->file->delete($this->path); 
    }

    public function toArray(): array
    {
        return [
            'name'      => $this->name(),
            'path'      => $this->path,
            'size'      => $this->size(),
            'mime_type' => $this->mimeType(),
        ];
    }
}