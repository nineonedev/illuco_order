<?php 

namespace Framework\Bus\Contracts; 

interface JobInterface
{
    public function handle(): void;

    public function queue(): string;

    public function delay(): int; 
}