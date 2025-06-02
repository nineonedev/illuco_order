<?php 

namespace Framework\Support;

class LockManager 
{
    protected string $directory; 
    protected array $locks = [];

    public function __construct(string $directory = 'storage/locks')
    {
        $this->directory = trim($directory, '/'); 
    }

    public function get(string $name): Lock
    {
        if (!isset($this->locks[$name])) {
            $this->locks[$name] = new Lock($name, $this->directory); 
        }

        return $this->locks[$name]; 
    }

    public function releaseAll(): void
    {
        foreach ($this->locks as $lock) {
            /** @var Lock $lock */
            $lock->release();
        }
    }
}