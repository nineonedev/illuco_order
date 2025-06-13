<?php 

namespace App\Domains\Common\Entities;

use App\Domains\Common\Repositories\FileAttachmentRepository;
use Framework\Database\ORM\Entities\MorphEntity;


class FileAttachment extends MorphEntity
{
    protected array $fillable = [
        'original_name', 
        'name',
        'extension',
        'mime_type',
        'size', 
        'extension',
        'path'
    ];

    protected array $casts = [
        'file_attachable_id' => 'int',
        'size' => 'int',
    ];


    public static function morphType(): string
    {
        return 'file_attachable';
    }

    public function repositoryClass(): string
    {
        return FileAttachmentRepository::class;
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