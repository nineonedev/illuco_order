<?php 

namespace App\Domains\Claim\Entities;

use App\Domains\Claim\Repositories\ClaimRepository;
use Framework\Database\ORM\Entities\Entity;

class Claim extends Entity
{
    protected array $fillable = []; 

    public static function repositoryClass(): string
    {
        return ClaimRepository::class;
    }
}