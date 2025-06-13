<?php 

namespace Framework\Database\ORM\Repositories;

use Framework\Database\ORM\Entities\Entity;
use Framework\Http\Request;

interface Obserable
{
    public function observe(Entity $entity, Request $request): void;
}