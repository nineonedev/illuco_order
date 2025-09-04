<?php 

namespace App\Domains\Communication\Entities;
use App\Domains\Communication\Repositories\SalesInfoRepository;
use Framework\Database\ORM\Entities\Entity;

class SalesInfo extends Entity
{
    protected array $fillable = [
        'beneficiary',
        'bank_name',
        'bank_address',
        'swift_code',
        'account_no',
        'remarks',
        'company_name',
        'company_address',
        'company_tel',
        'company_fax',
        'company_email',
        'company_website',
    ];

    protected array $casts = [
        'beneficiary' => '?string',
        'bank_name' => '?string',
        'bank_address' => '?string',
        'swift_code' => '?string',
        'account_no' => '?string',
        'remarks' => '?string',
        'company_name' => '?string',
        'company_address' => '?string',
        'company_tel' => '?string',
        'company_fax' => '?string',
        'company_email' => '?string',
        'company_website' => '?string',
    ];

    public static function repositoryClass(): string
    {
        return SalesInfoRepository::class;
    }
}