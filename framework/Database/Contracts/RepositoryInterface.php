<?php

namespace Framework\Database\Contracts;

use Framework\Database\Model\Entities\Entity;
use Framework\Database\Query\Builder;

interface RepositoryInterface
{

    public function getTable(): string;
    
    public function toEntity(array $row = []): Entity;

    /**
     * @return Entity[]
     */
    public function toEntities(array $rows): array;

    public function query(): Builder;
    
    public static function find($id): ?Entity;

    public static function all(): array;

    public static function where(string $column, $operator, $value): self;

    public static function first(): ?Entity;

    public static function create(array $attributes): bool;

    public function update(): bool;

    public function delete(): bool;

    public function save(): bool;

    public function with(array $relations): self;
}
