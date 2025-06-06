<?php 

namespace Framework\Database\ORM\Repositories;

use Framework\Database\ORM\Entities\Entity;

interface Obserable
{
    public function observe(Entity $entity): void;
}