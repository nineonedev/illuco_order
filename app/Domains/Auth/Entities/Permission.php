<?php 

namespace App\Domains\Auth\Entities;

use App\Domains\Auth\Repositories\PermissionRepository;
use Framework\Database\ORM\Entities\Entity;

class Permission extends Entity
{
    protected array $fillable = ['resource', 'action'];

    public static function repositoryClass(): string
    {
        return PermissionRepository::class;
    }

    public function matches(string $resource, string $action): bool
    {
        return $this->get('resource') === $resource && $this->get('action') === $action;
    }

}