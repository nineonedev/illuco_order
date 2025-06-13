<?php

namespace App\Domains\User\Repositories;

use App\Domains\User\Entities\Employee;
use Framework\Database\ORM\Repositories\Repository;

class EmployeeRepository extends Repository
{
    public static function table(): string
    {
        return 'employees';
    }

    public static function entityClass(): string
    {
        return Employee::class;
    }

}
