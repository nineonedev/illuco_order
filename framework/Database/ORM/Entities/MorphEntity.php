<?php 

namespace Framework\Database\ORM\Entities;

use Framework\Database\ORM\Contracts\Morphable;

abstract class MorphEntity extends Entity implements Morphable
{
    protected function setup(): void
    {
        $this->addFillableMorph();
        $this->addCastsMorph();
    }

    protected function addFillableMorph()
    {
        $this->fillable = array_merge($this->fillable, [static::getMorphType(), static::getMorphId()]);
    }

    protected function addCastsMorph()
    {
        $this->casts = array_merge($this->casts, [static::getMorphId() => "int"]);
    }

    public static function morphAttributes(string $morphTypeValue, int $moprhIdValue, array $data): array
    {
        return array_merge([
            static::getMorphType() => $morphTypeValue,
            static::getMorphId() => $moprhIdValue,
        ], $data);
    }

    public static function getMorphType(): string
    {
        $type = static::morphType();
        return "{$type}_type";
    }

    public static function getMorphId(): string
    {
        $type = static::morphType();
        return "{$type}_id";
    }
}