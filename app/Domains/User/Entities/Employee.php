<?php 

namespace App\Domains\User\Entities;

use App\Domains\User\Repositories\EmployeeRepository;
use Framework\Database\ORM\Entities\Entity;
use Framework\Database\ORM\Traits\SoftDeletes;

class Employee extends Entity
{
    use SoftDeletes;
    
    protected array $fillable = [
        'phone_number',
        'department',
        'position',
        'deleted_at',
    ];
    
    public static function repositoryClass(): string
    {
        return EmployeeRepository::class;
    }
}