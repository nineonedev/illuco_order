<?php

namespace Framework\Database\ORM\Relations;

use Framework\Database\ORM\Entities\Entity;

interface Pivotable 
{
    public function attach(Entity $related, array $attributes = []): bool;
    public function detach(array $relatedIds = []): int;
    public function sync(array $relatedData): void;
}