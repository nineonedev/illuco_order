<?php 

namespace App\Domains\User\Entities;

use App\Domains\User\Repositories\AdminRepository;
use Framework\Database\ORM\Entities\Entity;

class Admin extends Entity
{
    protected array $fillable = [
        'admin_key',
    ];

    public static function repositoryClass(): string
    {
        return AdminRepository::class;
    }

}
