<?php 

namespace App\Domains\Order\Entities;

use App\Domains\Order\Repositories\CustomerRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Traits\SoftDeletes;

class Customer extends Entity
{
    use SoftDeletes;
    protected array $fillable = [
        'country',
        'user_id',
        'dealer_id',
        'name',
        'phone',
        'email',
        'description',
        'age',
        'address',
        'created_at'
    ];

    protected array $casts = [
        'user_id' => 'int',
        'dealer_id' => '?int',
        'age' => '?int'
    ];

    public static function repositoryClass(): string
    {
        return CustomerRepository::class;
    }
}