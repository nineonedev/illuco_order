<?php 

namespace Framework\Filesystem;

use RuntimeException;

class DiskManager
{
    protected array $config; 
    protected string $default; 

    /** @var Disk[] */
    protected array $disks = [];

    protected File $fileHelper; 

    public function __construct(
        array $config,
        string $default = 'local',
        ?File $file = null
    )
    {
        $this->config = $config; 
        $this->default = $default; 
        $this->fileHelper = $file ?? new File();
    }

    public function disk(?string $name = null): Disk
    {
        $name = $name ?? $this->default; 

        if (isset($this->disks[$name])) {
            return $this->disks[$name];
        }

        if (!isset($this->config[$name])) {
            throw new RuntimeException("Disk [$name] is not defined."); 
        }

        $path = $this->config[$name];

        return $this->disks[$name] = new Disk($path, $this->fileHelper); 
    }

    public function has(string $name): bool
    {
        return isset($this->config[$name]); 
    }

    public function names(): array
    {
        return array_keys($this->disks); 
    }

    /**
     * @return Disk[]
     */
    public function disks(): array
    {
        return $this->disks;
    }

    public function default(): string
    {
        return $this->default; 
    }
}