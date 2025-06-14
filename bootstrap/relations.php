<?php

use App\Domains\User\Entities\Admin;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Entities\Employee;
use App\Domains\User\Entities\User;
use Framework\Database\ORM\Rel;

Rel::setConfig([
    Admin::class => [
        Rel::morphOne(User::class),
    ],
    Employee::class => [
        Rel::morphOne(User::class),
    ],
    Dealer::class => [
        Rel::morphOne(User::class),
    ],
    User::class => [
        Rel::morphTo(User::class, [
            Admin::class, 
            Employee::class, 
            Dealer::class
        ]),
    ], 
]);
