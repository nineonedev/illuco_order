<?php 

namespace App\Domains\User\Entities;

use App\Domains\User\Repositories\EmployeeRepository;
use Framework\Database\ORM\Entities\Entity;

class Employee extends Entity
{
    protected array $fillable = [
        'phone_number',
    ];
    
    public static function repositoryClass(): string
    {
        return EmployeeRepository::class;
    }
}