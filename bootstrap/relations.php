<?php

use App\Domains\Auth\Entities\Permission;
use App\Domains\Auth\Entities\Role;
use App\Domains\Communication\Entities\Claim;
use App\Domains\Communication\Entities\Notice;
use App\Domains\Product\Entities\ProductAttribute;
use App\Domains\Product\Entities\ProductTemplate;
use App\Domains\System\Entities\FileAttachment;
use App\Domains\User\Entities\Admin;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Entities\Employee;
use App\Domains\User\Entities\User;
use Framework\Database\ORM\Rel;

Rel::setConfig([
    // ===================================================================
    // Common 
    // ===================================================================
    Role::class => [
        Rel::belongsToMany('users', User::class, 'role_users', 'role_id', 'user_id'),
        Rel::belongsToMany('permissions', Permission::class, 'role_permissions', 'role_id', 'permission_id'),
    ],
    Permission::class => [
        Rel::belongsToMany('roles', Role::class, 'role_permissions', 'permission_id', 'role_id'),
    ],

    // ===================================================================
    // User 
    // ===================================================================
    User::class => [
        Rel::belongsToMany('roles', Role::class, 'role_users', 'user_id', 'role_id'),
        Rel::morphTo([
            Admin::class, Employee::class, Dealer::class
        ]),
        Rel::hasMany('notices', Notice::class, 'user_id'),
        Rel::hasMany('claims',  Claim::class, 'user_id'),
    ],

    Admin::class => [
        Rel::morphOne(User::class),
    ],
    Employee::class => [
        Rel::morphOne(User::class),
    ],
    Dealer::class => [
        Rel::morphOne(User::class),
    ],

    // ===================================================================
    // Communication
    // ===================================================================
    Notice::class => [
        Rel::belongsTo('user',  User::class, 'user_id'),
        Rel::morphMany(FileAttachment::class),
    ],
    Claim::class => [
        Rel::belongsTo('user',  User::class, 'user_id'),
        Rel::morphMany(FileAttachment::class),
    ],
    FileAttachment::class => [
        Rel::morphTo([
            Notice::class, Claim::class, ProductTemplate::class,
        ]),
    ],

    // ===================================================================
    // Product
    // ===================================================================
    ProductTemplate::class => [
        Rel::morphOne(FileAttachment::class),
        Rel::hasMany('attributes', ProductAttribute::class, 'template_id')
    ],
    ProductAttribute::class => [
        Rel::belongsTo('template', ProductTemplate::class, 'template_id'),
    ],
    // ===================================================================
    // Order
    // ===================================================================
]);