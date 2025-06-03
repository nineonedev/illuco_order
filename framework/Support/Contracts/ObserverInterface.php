<?php

namespace Framework\Support\Contracts;

interface ObserverInterface
{
    public function targetClass(): string;
    public function hook(): string;
    public function handle(object $target): void;
}