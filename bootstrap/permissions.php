<?php

use App\Domains\Auth\Entities\Role;
use App\Domains\Order\Entities\Cart;
use App\Domains\Order\Entities\Customer;
use App\Domains\Order\Entities\Order;
use App\Domains\Product\Entities\Category;
use App\Domains\Product\Entities\ProductTemplate;
use App\Domains\Communication\Entities\Claim;
use App\Domains\Communication\Entities\Notice;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Entities\Employee;

return [
    Employee::class => ['create', 'read', 'update', 'delete'],
    Dealer::class => ['create', 'read', 'update', 'delete'],
    Claim::class => ['create', 'read', 'update', 'delete'],
    Notice::class => ['create', 'read', 'update', 'delete'],
    Customer::class => ['create', 'read', 'update', 'delete'],
    ProductTemplate::class => ['create', 'read', 'update', 'delete'],
    Order::class => ['create', 'read', 'update', 'delete'],
    Cart::class => ['create', 'read', 'update', 'delete'],
    Role::class => ['create', 'read', 'update', 'delete'],
    Category::class => ['create', 'read', 'update', 'delete'],
];
