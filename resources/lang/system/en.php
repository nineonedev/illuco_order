<?php

use App\Domains\Communication\Enums\ClaimStatus;
use App\Domains\Order\Entities\Customer;
use App\Domains\Order\Entities\Order;
use App\Domains\Order\Enums\OrderStatus;
use App\Domains\Product\Entities\ProductTemplate;
use App\Domains\System\Entities\FileAttachment;
use App\Domains\Communication\Entities\Claim;
use App\Domains\Communication\Entities\Notice;
use App\Domains\User\Entities\Dealer;

return [
    // 역할
    'role' => [
        'employee' => 'Employee',
        'dealer' => 'Dealer',
        'claim' => 'Claim',
        'notice' => 'Notice',
        'customer' => 'Customer',
        'product' => 'Product',
        'order' => 'Order',
        'cart' => 'Cart',
        'role' => 'Role',
        'category' => 'Category',
    ],

    // 권한
    'action' => [
        'create' => 'Create',
        'read'   => 'Read',
        'update' => 'Update',
        'delete' => 'Delete',
    ],
    
    // 라벨
    'email' => 'Email',

    'order' => [
        'status' => [
            OrderStatus::CANCELED   => 'Order Canceled',
            OrderStatus::NEW        => 'Order Received',
            OrderStatus::CONFIRMED  => 'Order Confirmed',
            OrderStatus::PREPARING  => 'Product Preparing',
            OrderStatus::SHIPPED    => 'Shipped',
            OrderStatus::COMPLETED    => 'Order Completed',
            OrderStatus::REJECTED    => 'Order Rejected',
        ]
    ],

    'claim' => [
        'status' => [
            ClaimStatus::RECEIVED   => 'Claim Received',
            ClaimStatus::PROCESSING   => 'Processing',
            ClaimStatus::COMPLETED    => 'Completed',
        ]
    ],
    
    'countries' => [
        'US' => 'United States',
        'KR' => 'South Korea',
        'JP' => 'Japan',
        'CN' => 'China',
        'FR' => 'France',
        'DE' => 'Germany',
        'ES' => 'Spain',
        'RU' => 'Russia',
        'IN' => 'India',
        'GB' => 'United Kingdom',
        'IT' => 'Italy',
        'BR' => 'Brazil',
        'CA' => 'Canada',
        'AU' => 'Australia',
        'MX' => 'Mexico',
        'NL' => 'Netherlands',
        'TR' => 'Turkey',
        'ID' => 'Indonesia',
        'SA' => 'Saudi Arabia',
        'AR' => 'Argentina',
        'TH' => 'Thailand',
        'VN' => 'Vietnam',
        'PH' => 'Philippines',
        'PL' => 'Poland',
        'SE' => 'Sweden',
        'CH' => 'Switzerland',
        'BE' => 'Belgium',
        'NO' => 'Norway',
        'FI' => 'Finland',
        'DK' => 'Denmark',
        'UA' => 'Ukraine',
        'ZA' => 'South Africa',
        'EG' => 'Egypt',
        'NG' => 'Nigeria',
        'KE' => 'Kenya',
        'NZ' => 'New Zealand',
        'MY' => 'Malaysia',
        'SG' => 'Singapore',
        'IL' => 'Israel',
        'IR' => 'Iran',
        'GR' => 'Greece',
        'PT' => 'Portugal',
        'AT' => 'Austria',
        'CZ' => 'Czech Republic',
        'HU' => 'Hungary',
        'RO' => 'Romania',
        'BG' => 'Bulgaria',
        'SK' => 'Slovakia',
        'HR' => 'Croatia',
        'SI' => 'Slovenia',
        'RS' => 'Serbia',
        'TW' => 'Taiwan',
        'AE' => 'United Arab Emirates',
        'PK' => 'Pakistan',
        'MA' => 'Morocco',
        'HK' => 'Hong Kong',
        'IQ' => 'Iraq',
        'AM' => 'Armenia',
    ],
    'loupe' => [
        'type'              => 'Type',
        'frame_type'        => 'Frame Type',
        'working_distance'  => 'Working Distance (mm)',

        'od_sph'            => 'OD SPH',
        'os_sph'            => 'OS SPH',

        'od_cyl'            => 'OD CYL',
        'os_cyl'            => 'OS CYL',

        'od_axis'           => 'OD Axis',
        'os_axis'           => 'OS Axis',

        'od_add'            => 'OD Add',
        'os_add'            => 'OS Add',

        'pd_right'          => 'PD Right',
        'pd_left'           => 'PD Left',
        'pd_total'          => 'PD Total',
        'vertex_distance'   => 'Vertex Distance (mm)',
        'add_option'        => 'Additional Option',
    ],
];
