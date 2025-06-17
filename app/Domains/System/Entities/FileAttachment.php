<?php 

namespace App\Domains\System\Entities;

use App\Domains\System\Repositories\FileAttachmentRepository;
use Framework\Database\ORM\Entities\Entity;
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
        'path',
        'upload_path',
        'sort_order',
        'file_key',
    ];

    protected array $casts = [
        'size' => 'int',
        'sort_order' => 'int',
    ];


    public static function morphType(): string
    {
        return 'file_attachable';
    }

    public static function repositoryClass(): string
    {
        return FileAttachmentRepository::class;
    }

    public function belongsToEntity(Entity $entity): bool
    {
        return $this->{static::getMorphType()} === get_class($entity)::alias()
            && $this->{static::getMorphId()} === $entity->getPrimaryKey();
    }
}