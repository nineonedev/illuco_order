<?php

namespace Framework\Database\ORM\Entities\Contracts; 

interface Serialable
{
    public function getSerialMorphType(): string;
    public function getSerialPrefix(): string;
    public function getSerialOptionCode(): ?string;
    public function getSerialDateCode(): string;
    public function getSerialRevisionCode(): ?string;

}