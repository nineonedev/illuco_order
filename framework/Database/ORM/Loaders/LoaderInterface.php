<?php

namespace Framework\Database\ORM\Loaders;

use Framework\Database\ORM\Entities\Entity;

interface LoaderInterface
{
    /**
     * 지정된 관계를 엔티티(또는 컬렉션)에 로드한다.
     * @param Entity[] $entities  (혹은 단일 Entity도 지원)
     * @param string[] $relations
     * @return void
     */
    public function load(array $entities, array $relations): void;
}
