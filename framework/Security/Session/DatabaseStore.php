<?php

namespace Framework\Security\Session;

use Framework\Security\Contracts\SessionInterface;

class DatabaseStore implements SessionInterface
{
    protected string $id; 
    protected array $data = []; 
    protected bool $started = false; 

    public function start(): void
    {
        if ($this->started) return; 

        $this->id = $_COOKIE['SESSION_ID'] ?? bin2hex(random_bytes(20)); 

        $record = db()->table('sessions')->where('id', '=', $this->id)->first(); 
        $this->data = $record ? unserialize($record->payload) : []; 

        setcookie('SESSION_ID', $this->id, time() * 60 * 60 * 24, '/');

        $this->started = true; 
    }

    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default; 
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
        $this->save();
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->data); 
    }

    public function forget(string $key): void
    {
        unset($this->data[$key]); 
        $this->save(); 
    }

    public function flush(): void
    {
        $this->data = []; 
        $this->save(); 
    }

    public function id(): string
    {
        return $this->id; 
    }

    public function all(): array
    {
        return $this->data; 
    }

    public function regenerate(): void
    {
        $this->id = bin2hex(random_bytes(20)); 
        $this->save(); 
        setcookie('SESSION_ID', $this->id, time() * 60 * 60 * 24, '/'); 
    }

    public function invalidate(): void
    {
        db()->table('sessions')->where('id', '=', $this->id)->delete(); 
        $this->regenerate(); 
        $this->data = []; 
    }

    protected function save(): void
    {
        db()->table('sessions')->updateOrInsert(
            ['id' => $this->id], 
            ['payload' => serialize($this->data), 'updated_at' => now()]
        );
    }
}