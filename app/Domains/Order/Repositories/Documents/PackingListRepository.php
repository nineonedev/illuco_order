<?php

namespace App\Domains\Order\Repositories\Documents;

use App\Domains\Order\Entities\Documents\PackingList;
use Framework\Database\ORM\Repositories\Repository;

class PackingListRepository extends Repository
{
    public static function table(): string
    {
        return 'order_document_packing_lists';
    }

    public static function entityClass(): string
    {
        return PackingList::class;
    }
}
