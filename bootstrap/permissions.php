<?php

use App\Domains\Common\Entities\FileAttachment;
use App\Domains\Communication\Entities\Claim;
use App\Domains\Communication\Entities\Notice;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Entities\Employee;

return [
    Employee::class =>  ['create', 'read', 'update', 'delete'],
    Dealer::class =>  ['create', 'read', 'update', 'delete'],
    FileAttachment::class => ['create', 'read', 'update', 'delete'],
    Claim::class =>  ['create', 'read', 'update', 'delete'],
    Notice::class =>  ['create', 'read', 'update', 'delete'],
];