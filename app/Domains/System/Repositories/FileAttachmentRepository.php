<?php

namespace App\Domains\System\Repositories;

use App\Domains\System\Entities\FileAttachment;
use Exception;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Entities\MorphEntity;
use Framework\Database\ORM\Repositories\Repository;
use Framework\Filesystem\UploadedFile;
use Framework\Validation\Validator;

class FileAttachmentRepository extends Repository
{
    const UPDATE_INPUT_KEY = '_file_attachment_updates';
    const DELETE_INPUT_KEY = '_file_attachment_deleted';

    public static function table(): string
    {
        return 'file_attachments';
    }

    public static function entityClass(): string
    {
        return FileAttachment::class;
    }

    public function handleDelete(Entity $entity): void
    {
        $deletedIds = (array) request()->input(static::DELETE_INPUT_KEY, []);


        foreach ($deletedIds as $id) {
            $attachment = $this->find($id);
            if ($attachment && $attachment->belongsToEntity($entity)) {
                $this->delete($attachment);
            }
        }
    }

    public function handleUpdate(Entity $entity): void
    {
        $updates = request()->input(static::UPDATE_INPUT_KEY, []);
        foreach ($updates as $id => $props) {
            $attachment = $this->find($id);
            if ($attachment && $attachment->belongsToEntity($entity)) {
                $attachment->fill([
                    'sort_order' => $props['sort_order'] ?? 0,
                    'file_key'   => $props['file_key'] ?? null,
                ]);
                $this->save($attachment);
            }
        }
    }

    protected function deleteByFileKey(Entity $entity, string $fileKey): void
    {
        $attachments = static::query()
            ->where(FileAttachment::getMorphType(), get_class($entity)::alias())
            ->where(FileAttachment::getMorphId(), $entity->getPrimaryKey())
            ->where('file_key', $fileKey)
            ->get();

        foreach ($attachments as $attachment) {
            $this->delete($attachment);
        }
    }


    public function handleUpload(Entity $entity, array $config = []): array
    {
        $uploaded = [];

        foreach ($config as $fileKey => $ruleNames) {
            $files = request()->file($fileKey);
            if (!$files) continue;
            if (!request()->hasFile($fileKey)) continue;

            $this->deleteByFileKey($entity, $fileKey);

            // 업로드 수행
            if ($this->isSingleUpload($files)) {
                $uploaded[] = $this->processUploadFile($entity, $files, $ruleNames, $fileKey, 0);
            } else {
                $normalized = $this->normalizeFiles($files);
                foreach ($normalized as $i => $file) {
                    $uploaded[] = $this->processUploadFile($entity, $file, $ruleNames, "{$fileKey}[{$i}]", $i);
                }
            }
        }

        return array_filter($uploaded);
    }



    public function uploadBulk(Entity $entity, array $fields = [], ?string $ruleNames = null): array
    {
        $uploaded = [];

        foreach ($fields as $key) {
            $files = request()->file($key);
            
            if (!$files) continue;

            if ($this->isSingleUpload($files)) {
                $attachment = $this->processUploadFile($entity, $files, $ruleNames, $key, 0);
                if ($attachment) {
                    $uploaded[] = $attachment;
                }
            } else {
                $normalizedFiles = $this->normalizeFiles($files);
                foreach ($normalizedFiles as $index => $file) {
                    $fileKey = "{$key}[{$index}]";
                    $attachment = $this->processUploadFile($entity, $file, $ruleNames, $fileKey, $index);
                    if ($attachment) {
                        $uploaded[] = $attachment;
                    }
                }
            }
        }

        return $uploaded;
    }

    public function uploadBulkWithConfig(Entity $entity, array $config = []): array
    {
        $uploaded = [];

        foreach ($config as $key => $ruleNames) {
            $files = request()->file($key);

            if (!$files) continue;

            if ($this->isSingleUpload($files)) {
                $attachment = $this->processUploadFile($entity, $files, $ruleNames, $key, 0);
                if ($attachment) {
                    $uploaded[] = $attachment;
                }
            } else {
                $normalizedFiles = $this->normalizeFiles($files);
                foreach ($normalizedFiles as $index => $file) {
                    $fileKey = "{$key}[{$index}]";
                    $attachment = $this->processUploadFile($entity, $file, $ruleNames, $fileKey, $index);
                    if ($attachment) {
                        $uploaded[] = $attachment;
                    }
                }
            }
        }

        return $uploaded;
    }

    public function deleteAllFor(Entity $entity): void
    {
        /** @var class-string<MorphEntity> $morph */
        $morph = static::entityClass();

        $attachments = static::query()
            ->where($morph::getMorphType(), get_class($entity)::alias())
            ->where($morph::getMorphId(), $entity->getPrimaryKey())
            ->get();

        foreach ($attachments as $attachment) {
            $this->delete($attachment);
        }
    }

    /** 단일 파일 업로드 */
    public function uploadOne(Entity $entity, string $key, ?string $ruleNames = null): ?FileAttachment
    {
        $file = request()->file($key) ?? null;
        if (!$file) return null;

        return $this->processUploadFile($entity, $file, $ruleNames, $key, 0);
    }

    /** 다건 파일 업로드 */
    public function uploadMany(Entity $entity, $files = [], ?string $ruleNames = null): array
    {
        /** @var class-string<MorphEntity> $entityClass */
        $entityClass = static::entityClass();
        $alias = get_class($entity)::alias(); 

        // 입력 없으면 요청에서 morphType 기준으로 가져옴
        if (empty($files)) {
            $files = request()->file($entityClass::morphType());
        }

        if (!$files) return [];

        $uploaded = [];

        // 단일 파일일 경우
        if ($this->isSingleUpload($files)) {
            $attachment = $this->processUploadFile($entity, $files, $ruleNames, $entityClass::morphType(), 0);
            return $attachment ? [$attachment] : [];
        }

        // 멀티 파일일 경우
        $normalizedFiles = $this->normalizeFiles($files);

        foreach ($normalizedFiles as $index => $file) {
            $fileKey = "{$entityClass::morphType()}[{$index}]";
            $attachment = $this->processUploadFile($entity, $file, 'uploaded|uploadedOk', $fileKey, $index);
            if ($attachment) {
                $uploaded[] = $attachment;
            }
        }

        return $uploaded;
    }

    /** 내부: 단일 파일 업로드 처리 + 벨리데이션 + 저장 */
    protected function processUploadFile(Entity $entity, array $file, ?string $rules, string $fileKey = null, int $sortOrder = 0): ?FileAttachment
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        $rules ??= 'uploaded|uploadedOk';

        $validator = Validator::make([$file], [0 => $rules]);
        if ($validator->fails()) return null;

        $uploadedFile = $this->storeAndCreateUploadedFile($file, $entity);
        $attachment = $this->makeAttachment($entity, $uploadedFile, $fileKey, $sortOrder);

        return static::make()->save($attachment);
    }

    /** 단일 업로드 여부 */
    public function isSingleUpload(array $files): bool
    {
        return isset($files['tmp_name']) && !is_array($files['tmp_name']);
    }

    /** 파일 배열 정규화 */
    protected function normalizeFiles(array $group): array
    {
        if (isset($group['tmp_name']) && !is_array($group['tmp_name'])) {
            return [$group];
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

    /** 특정 엔티티에 속한 첨부 파일 조회 */
    public function hasMany(Entity $entity): array
    {
        /** @var class-string<MorphEntity> $morph */
        $morph = static::entityClass();

        return static::query()
            ->where($morph::getMorphType(), get_class($entity)::alias())
            ->where($morph::getMorphId(), $entity->getPrimaryKey())
            ->get();
    }

    /** 내부: 업로드 파일 저장 및 디스크 이동 */
    protected function storeAndCreateUploadedFile($file, Entity $entity): UploadedFile
    {
        $uploadedFile = UploadedFile::createFromGlobal($file);
        $directory = get_class($entity)::alias();
        $disk = disk()->toWork($directory);

        $uploadedFile->storeAs($disk);
        return $uploadedFile;
    }

    /** 내부: FileAttachment 엔티티 생성 */
    protected function makeAttachment(Entity $entity, UploadedFile $uploadedFile, string $fileKey = null, int $sortOrder = 0): FileAttachment
    {
        /** @var class-string<MorphEntity> $entityClass */
        $entityClass = static::entityClass();

        return FileAttachment::make(array_merge(
            $uploadedFile->toArray(),
            [
                $entityClass::getMorphType() => get_class($entity)::alias(),
                $entityClass::getMorphId() => $entity->getPrimaryKey(),
                'upload_path' => $uploadedFile->getUploadPath(),
                'file_key' => $fileKey,
                'sort_order' => $sortOrder,
            ]
        ));
    }

    public function delete(Entity $entity): bool
{
        $disk = disk()->setRoot($entity->path);
        $disk->load();

        if (!$disk->has($entity->name)) {
            return false;
        }

        $disk->delete($entity->name);

        return parent::delete($entity);
    }
}
