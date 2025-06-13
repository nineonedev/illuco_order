<?php 

namespace App\Domains\User\Entities;

use App\Domains\User\Repositories\DealerRepository;
use Framework\Database\ORM\Entities\Entity;

class Dealer extends Entity
{
    protected array $fillable = [
        'country',
        'code',
        'phone_number',
        'address',
        'description'
    ];

    public static function repositoryClass(): string
    {
        return DealerRepository::class;
    }
}