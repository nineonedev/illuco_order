<?php

use Framework\Database\ORM\RelationMap;

use App\Domains\Common\Entities\FileAttachment;

use App\Domains\User\Entities\Admin;
use App\Domains\User\Entities\Employee;
use App\Domains\User\Entities\User;


RelationMap::setRelationMap([
    User::class => [
        RelationMap::morphTo('userable')
    ], 
    Admin::class => [
        RelationMap::morphOne('user', User::class),
    ],
    Employee::class => [
        RelationMap::morphOne('user', User::class),
    ],
    Employee::class => [
        RelationMap::morphOne('user', User::class),
    ],
    FileAttachment::class => [
        RelationMap::morphTo('file_attachable')
    ]
]);