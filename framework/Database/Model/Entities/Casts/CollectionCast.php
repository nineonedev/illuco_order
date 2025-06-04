<?php

namespace Framework\Database\Model\Entities\Casts;

use Framework\Database\Contracts\CastInterface;

use Framework\Support\Collection;

class CollectionCast implements CastInterface
{
    public function cast($value)
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        return new Collection((array) $value);
    }

    public function recast($value)
    {
        return json_encode($value instanceof Collection ? $value->all() : $value);
    }
}
