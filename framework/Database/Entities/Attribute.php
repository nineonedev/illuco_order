<?php

namespace Framework\Database\Entities; 

class Attribute
{
    protected $get; 
    protected $set; 

    public function __construct(?\Closure $get = null, ?\Closure $set = null)
    {
        $this->get = $get; 
        $this->set = $set; 
    }

    public static function cast(callable $castType): self
    {
        return new self($castType, $castType);
    }

    public function get($value)
    {
        return $this->get ? ($this->get)($value) : $value; 
    }

    public function set($value)
    {
        return $this->set ? ($this->set)($value) : $value; 
    }
}