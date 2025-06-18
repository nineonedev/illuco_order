<?php

use App\Domains\Order\Entities\Customer;
use App\Domains\Product\Entities\ProductTemplate;
use App\Domains\System\Entities\FileAttachment;
use App\Domains\Communication\Entities\Claim;
use App\Domains\Communication\Entities\Notice;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Entities\Employee;

return [
    Employee::alias() => 'Employee',
    Notice::alias() => 'Notice',
    Dealer::alias() => 'Dealer',
    Claim::alias() => 'Claim',
    FileAttachment::alias() => 'File Attachment',
    Customer::alias() => 'Customer',
    ProductTemplate::alias() => 'Product Manage',

    'email' => 'Email',

    'create' => 'Create',
    'read'   => 'Read',
    'update' => 'Update',
    'delete' => 'Delete',

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
    ]
];
