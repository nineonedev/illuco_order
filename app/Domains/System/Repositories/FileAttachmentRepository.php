<?php

namespace App\Domains\System\Repositories;

use App\Domains\System\Entities\FileAttachment;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Entities\MorphEntity;
use Framework\Database\ORM\Repositories\Repository;
use Framework\Filesystem\UploadedFile;
use Framework\Validation\Validator;

class FileAttachmentRepository extends Repository
{
    public static function table(): string
    {
        return 'file_attachments';
    }

    public static function entityClass(): string
    {
        return FileAttachment::class;
    }

    public function uploadOne(Entity $entity, string $filename, ?string $ruleNames = null): ?FileAttachment
    {
        if (!request()->hasFile($filename)) return null;

        if (!$ruleNames) {
            $ruleNames = 'uploaded|uploadedOk';
        }

        request()->validateOrFail([$filename => $ruleNames]);
        $file = request()->file($filename);

        $uploadedFile = $this->storeAndCreateUploadedFile($file, $entity);
        $attachment = $this->makeAttachment($entity, $uploadedFile, $fileKey = $filename, $sortOrder = 0);

        return static::make()->save($attachment);
    }

    public function uploadMany(Entity $entity, $files = [], ?string $ruleNames = null): array
    {
        /** @var class-string<MorphEntity> $entityClass */
        $entityClass = static::entityClass();

        if (empty($files)) {
            $files = request()->file($entityClass::morphType()); // 전체 파일 입력
        }

        if (!is_array($files)) return [];

        $uploaded = [];
        $normalizedFiles = $this->normalizeFiles($files);

        return $uploaded;
    }

    protected function storeAndCreateUploadedFile($file, Entity $entity): UploadedFile
    {
        $uploadedFile = UploadedFile::createFromGlobal($file);

        $directory = get_class($entity)::alias();
        $disk = disk()->toWork($directory);

        $uploadedFile->storeAs($disk);

        return $uploadedFile;
    }

    protected function makeAttachment(Entity $entity, UploadedFile $uploadedFile, string $fileKey = null, int $sortOrder = 0): FileAttachment
    {
        /** @var class-string<MorphEntity> $entityClass */
        $entityClass = static::entityClass();

        $attributes = array_merge(
            $uploadedFile->toArray(),
            [
                $entityClass::getMorphType() => $entityClass::morphType(),
                $entityClass::getMorphId() => $entity->getPrimaryKey(),
                'file_key' => $fileKey,
                'sort_order' => $sortOrder,
            ]
        );

        return FileAttachment::make($attributes);
    }

    protected function normalizeFiles(array $group, ?string $ruleNames = null): array
    {
        if (isset($group['tmp_name']) && !is_array($group['tmp_name'])) {
            return [$group];
        }

        if (!$ruleNames) {
            $ruleNames = 'uploaded|uploadedOk';
        }

        $normalized = [];
        $count = count($group['name']);

        for ($i = 0; $i < $count; $i++) {
            if (empty($group['name'][$i])) continue;

            $normalized[] = [
                'name'     => $group['name'][$i],
                'type'     => $group['type'][$i],
                'tmp_name' => $group['tmp_name'][$i],
                'error'    => $group['error'][$i],
                'size'     => $group['size'][$i],
            ];
        }

        return $normalized;
    }
}
