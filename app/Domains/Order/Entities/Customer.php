<?php 

namespace App\Domains\Order\Entities;

use App\Domains\Order\Repositories\CustomerRepository;
use Framework\Database\ORM\Entities\Entity;

class Customer extends Entity
{
    protected array $fillable = [
        'user_id',
        'country',
        'name',
        'phone_number',
        'email',
        'description',
    ];

    public static function repositoryClass(): string
    {
        return CustomerRepository::class;
    }
}