<?php

namespace Framework\Database\ORM\Loaders;

use Framework\Database\ORM\Entities\Entity;

abstract class AbstractLoader implements LoaderInterface
{
    /** 
     * 첫 엔티티 반환 (유틸) 
     * @param Entity[] $entities
     * @return Entity|null
     */
    protected function getFirstEntity(array $entities): ?Entity
    {
        return reset($entities) ?: null;
    }
}
