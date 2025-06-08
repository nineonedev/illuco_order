<?php 

namespace App\Domains\User\Entities;

use App\Domains\User\Repositories\FileRepository;
use Framework\Database\ORM\Entities\Entity;

class File extends Entity
{
    protected array $fillable = [
        'morph_type',
        'morph_id',
        'original_name', 
        'name',
        'extension',
        'mime_type',
        'size', 
        'extension',
        'path'
    ];

    protected array $casts = [
        'morph_id' => 'int',
        'size' => 'int',
    ];

    public function repositoryClass(): string
    {
        return FileRepository::class;
    }

    public function storagePath(): string
    {
        return rtrim($this->path, DS) . DS . $this->name;
    }

    public function uploadPath(): string
    {
        return upload_path($this->path . DS . $this->name);
    }
}