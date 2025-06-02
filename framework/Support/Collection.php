<?php 

namespace Framework\Support;

class Collection implements \IteratorAggregate, \Countable {
    protected array $items = []; 

    public function __construct(array $items = [])
    {
        $this->items = $items; 
    }

    public static function create(array $items = [])
    {
        return new static($items); 
    }
    
    public function pluck($key): self
    {
        $results = []; 

        foreach ($this->items as $item) {
            if (is_array($item) && isset($item[$key])) {
                $results[] = $item[$key]; 
            } elseif (is_object($item) && isset($item->{$key})) {
                $results[] = $item->{$key}; 
            }
        }

        return new static($results); 
    }

    public function all(): array
    {
        return $this->items; 
    }

    public function get($key, $default = null)
    {
        return $this->items[$key] ?? $default; 
    }

    public function has($key): bool
    {
        return isset($this->items[$key]); 
    }

    public function map(callable $callback): self 
    {
        $this->items = array_map($callback, $this->items); 

        return $this; 
    }

    public function filter(callable $callback): self
    {
        $this->items = array_filter($this->items, $callback); 

        return $this; 
    }

    public function reduce(callable $callback, $initial = null)
    {
        return array_reduce($this->items, $callback, $initial); 
    }

    public function each(callable $callback): self
    {
        foreach ($this->items as $key => $item) {
            $callback($item, $key); 
        }

        return $this; 
    }

    public function push($item): self
    {
        $this->items[] = $item; 
        return $this; 
    }

    public function pop()
    {
        return array_pop($this->items); 
    }

    public function shift()
    {
        return array_shift($this->items); 
    }

    public function unshift()
    {
        return array_unshift($this->items); 
    }

    public function sortAsc(): self 
    {
        asort($this->items); 

        return $this; 
    }

    public function sortDesc(): self 
    {
        arsort($this->items); 
        
        return $this;
    }

    public function sort(?callable $callback = null): self 
    {
        if ($callback) {
            uasort($this->items, $callback); 
        } else {
            sort($this->items); 
        }

        return $this; 
    }

    public function merge(array $items): self
    {
        if ($items instanceof self) {
            $items = $items->all(); 
        }

        $this->items = array_merge($this->items, $items); 

        return $this; 
    }

    public function reverse(): self 
    {
        $this->items = array_reverse($this->items); 

        return $this; 
    }

    public function splice(int $offset, ?int $length = null, array $replacement = []): self
    {
        $removed = array_splice($this->items, $offset, $length ?? count($this->items), $replacement); 

        return new static($removed); 
    }

    public function slice(int $offset, ?int $length = null): self
    {
        $sliced = array_slice($this->items, $offset, $length); 

        return new static($sliced); 
    }

    public function count(): int
    {
        return count($this->items); 
    }

    public function find(callable $callback, $default = null) 
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key)) {
                return $item; 
            }
        }

        return $default; 
    }

    public function findIndex(callable $callback) 
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key)) {
                return $key; 
            }
        }

        return null;
    }

    public function first($default = null)
    {
        return count($this->items) > 0 ? reset($this->items) : $default; 
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items); 
    }
}