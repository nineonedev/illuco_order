<?php

use App\Domains\Auth\Entities\Permission;
use App\Domains\Auth\Entities\Role;
use App\Domains\Communication\Entities\Claim;
use App\Domains\Communication\Entities\Notice;
use App\Domains\Order\Entities\Customer;
use App\Domains\Order\Entities\Cart;
use App\Domains\Order\Entities\CartItem;
use App\Domains\Order\Entities\Documents\CommercialInvoice;
use App\Domains\Order\Entities\Documents\PackingList;
use App\Domains\Order\Entities\Documents\ProductRequest;
use App\Domains\Order\Entities\Documents\ProformaInvoice;
use App\Domains\Order\Entities\Order;
use App\Domains\Order\Entities\OrderDocument;
use App\Domains\Order\Entities\OrderItem;
use App\Domains\Product\Entities\Category;
use App\Domains\Product\Entities\Headlight;
use App\Domains\Product\Entities\Loupe;
use App\Domains\Product\Entities\Product;
use App\Domains\Product\Entities\ProductAttribute;
use App\Domains\Product\Entities\ProductOption;
use App\Domains\Product\Entities\ProductTemplate;
use App\Domains\Product\Entities\ProductValue;
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
        Rel::hasMany('notices', Notice::class, 'user_id'),
        Rel::hasMany('claims',  Claim::class, 'user_id'),
        Rel::hasMany('customers', Customer::class, 'user_id'),
        Rel::hasOne('dealer', Dealer::class, 'id'),
    ],
    Dealer::class => [
        Rel::belongsTo('user', User::class, 'id'),
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
    Category::class => [
        Rel::hasMany('templates', ProductTemplate::class, 'category_id')
    ],
    ProductTemplate::class => [
        Rel::belongsTo('category', Category::class, 'category_id'),
        Rel::morphMany(FileAttachment::class),
    ],
    Product::class => [
        Rel::belongsTo('template', ProductTemplate::class, 'template_id'),
        Rel::hasOne('loupe', Loupe::class, 'id'),
        Rel::hasOne('headlight', Headlight::class, 'id'),
    ],
    Loupe::class => [
        Rel::belongsTo('product', Product::class, 'id'),
    ],
    Headlight::class => [
        Rel::belongsTo('product', Product::class, 'id'),
    ],
    Customer::class => [
        Rel::hasOne('cart', Cart::class, 'customer_id'),
        Rel::belongsTo('user', User::class, 'user_id'),
    ],
    Cart::class => [
        Rel::belongsTo('customer', Customer::class, 'customer_id'),
        Rel::hasMany('cartitems', CartItem::class, 'cart_id'),
    ],
    CartItem::class => [
        Rel::belongsTo('cart', Cart::class, 'cart_id'),
        Rel::belongsTo('product', Product::class, 'product_id'),
    ],
    // ===================================================================
    // Order
    // ===================================================================
    Order::class => [
        Rel::belongsTo('user', User::class, 'user_id'),
        Rel::belongsTo('customer', Customer::class, 'customer_id'),
        Rel::hasMany('items', OrderItem::class, 'order_id'),
        Rel::hasMany('documents', OrderDocument::class, 'order_id'),
    ],
    OrderItem::class => [
        Rel::belongsTo('order', Order::class, 'order_id'),
        Rel::belongsTo('product', Product::class, 'product_id'),
    ],
    OrderDocument::class => [
        Rel::belongsTo('order', Order::class, 'order_id'),
        Rel::hasOne('proforma_invoice', ProformaInvoice::class, 'id'),
        Rel::hasOne('product_request', ProductRequest::class, 'id'),
        Rel::hasOne('packing_list', PackingList::class, 'id'),
        Rel::hasOne('commercial_invoice', CommercialInvoice::class, 'id'),
    ],
    ProformaInvoice::class => [
        Rel::belongsTo('document', OrderDocument::class, 'id')
    ],
    ProductRequest::class => [
        Rel::belongsTo('document', OrderDocument::class, 'id')
    ],
    PackingList::class => [
        Rel::belongsTo('document', OrderDocument::class, 'id')
    ],
    CommercialInvoice::class => [
        Rel::belongsTo('document', OrderDocument::class, 'id')
    ],
]);