<?php

use App\Domains\Auth\Entities\Permission;
use App\Domains\Auth\Entities\Role;
use App\Domains\Communication\Entities\Claim;
use App\Domains\Communication\Entities\Notice;
use App\Domains\Order\Entities\Customer;
use App\Domains\Order\Entities\Cart;
use App\Domains\Order\Entities\CartItem;
use App\Domains\Product\Entities\ProductAttribute;
use App\Domains\Product\Entities\ProductOption;
use App\Domains\Product\Entities\ProductTemplate;
use App\Domains\System\Entities\FileAttachment;
use App\Domains\User\Entities\Admin;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Entities\Employee;
use App\Domains\User\Entities\User;
use Framework\Database\ORM\Rel;
use Framework\Database\Schema\AbstractColumnDefinition;

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
        Rel::morphMany(FileAttachment::class),
        Rel::belongsToMany('attributes', ProductAttribute::class, 'product_attribute_template', 'template_id', 'attribute_id'),
    ],
    ProductAttribute::class => [
        Rel::belongsToMany('templates', ProductTemplate::class, 'product_attribute_template', 'attribute_id', 'template_id'),
        Rel::hasMany('options', ProductOption::class, 'attribute_id'),
    ],
    ProductOption::class => [
        Rel::belongsTo('attribute', ProductAttribute::class, 'attribute_id'),
    ],
    Customer::class => [
        Rel::hasOne('cart', Cart::class, 'customer_id'),
    ],
    Cart::class => [
        Rel::belongsTo('customer', Customer::class, 'customer_id'),
        Rel::hasMany('cartitems', CartItem::class, 'cart_id'),
    ],
    CartItem::class => [
        Rel::belongsTo('cart', Cart::class, 'cart_id'),
    ],
    // ===================================================================
    // Order
    // ===================================================================
]);