<?php

namespace Framework\State;

use Framework\Support\Str;

class Env 
{
    protected array $values = []; 

    public function __construct(string $path = null)
    {
        if ($path && file_exists($path)) {
            $this->load($path); 
        }
    }

    public function load(string $path): void
    {
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); 

        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; 
            }

            if (!Str::contains($line, '=')) {
                continue;
            }

            [$key, $value] = explode('=', $line, 2); 

            $key = trim($key); 
            $value = trim($value); 

            if ( (Str::startsWith($value, '"') && Str::endsWith($value, '"'))
                || (Str::startsWith($value, "'") && Str::endsWith($value, "'")) ) {
                $value = substr($value, 1, -1);
            }

            $this->values[$key] = $value;
        }
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return $this->values[$key] ?? $default; 
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->values);
    }

    public function all(): array
    {
        return $this->values;
    }

    public function set(string $key, $value): void
    {
        $this->values[$key] = $value; 
    }
}