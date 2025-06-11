<?php

namespace Framework\Security\Session;

use Framework\Security\Session\Contracts\SessionInterface;
use RuntimeException;

class SessionManager implements SessionInterface
{
    /**
     * @var SessionStore[]
     */
    protected array $drivers = [];

    protected ?string $current = null;

    /** @var array<string, SessionBag> */
    protected array $bags = [];

    public function setDriver(string $name, SessionStore $driver): void
    {
        $this->drivers[$name] = $driver;
    }

    public function use(string $name): void
    {
        if (!isset($this->drivers[$name])) {
            throw new RuntimeException("Session driver [$name] not registered.");
        }

        $this->current = $name;
        $this->drivers[$name]->start();
    }

    public function driver(?string $name = null): SessionStore
    {
        $name = $name ?? $this->current;

        if (!isset($this->drivers[$name])) {
            throw new RuntimeException("Session driver [$name] not registered.");
        }

        return $this->drivers[$name];
    }

    public function start(): void
    {
        $this->driver()->start();
    }

    public function get(string $key, $default = null)
    {
        return $this->driver()->get($key, $default);
    }

    public function set(string $key, $value): void
    {
        $this->driver()->set($key, $value);
    }

    public function has(string $key): bool
    {
        return $this->driver()->has($key);
    }

    public function forget(string $key): void
    {
        $this->driver()->forget($key);
    }

    public function flush(): void
    {
        $this->driver()->flush();
    }

    public function all(): array
    {
        return $this->driver()->all();
    }

    public function regenerate(): void
    {
        $this->driver()->regenerate();
    }

    public function invalidate(): void
    {
        $this->driver()->invalidate();
    }

    public function id(): string
    {
        return $this->driver()->id();
    }

    public function bag(string $key): SessionBag
    {
        if (!isset($this->bags[$key])) {
            $this->bags[$key] = $this->driver()->bag($key);
        }

        return $this->bags[$key];
    }

    public function flashBag(): FlashBag
    {
        return $this->driver()->flashBag();
    }

    public function hasFlashBag(): bool
    {
        return $this->driver()->hasFlashBag();
    }

    public function save(): void
    {
        foreach ($this->drivers as $driver) {
            if (!$driver->isStarted()) {
                continue;
            }
            
            $driver->save();
        }
    }
}
