<?php 

namespace App\Domains\Order\Entities;

use App\Domains\Order\Repositories\CustomerRepository;
use Framework\Database\ORM\Entities\Entity;

class Customer extends Entity
{
    protected array $fillable = [
        'country',
        'user_id',
        'dealer_id',
        'name',
        'phone',
        'email',
        'description',
    ];

    protected array $casts = [
        'user_id' => 'int',
        'dealer_id' => 'int',
    ];

    public static function repositoryClass(): string
    {
        return CustomerRepository::class;
    }
}