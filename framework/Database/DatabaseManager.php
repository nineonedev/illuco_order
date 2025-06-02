<?php 

namespace Framework\Database;

use Framework\Database\Connections\MysqlConnection;
use Framework\Database\Contracts\ConnectionInterface;
use InvalidArgumentException;

class DatabaseManager {
    /**
     * @var array<string,ConnectionInterface> 
     */
    protected array $connections = [];

    protected array $configurations = [];

    protected string $default; 

    public function __construct(array $config)
    {
        $this->configurations = $config;
    }

    public function connection(?string $name = null): ConnectionInterface
    {
        $name = $name ?: $this->default;

        if (!isset($this->connections[$name])) {
            if (!isset($this->configurations[$name])) {
                throw new InvalidArgumentException("Database connection config [{$name}] not defined.");
            }

            $this->connections[$name] = $this->createConnection($this->configurations[$name]);
        }

        return $this->connections[$name];
    }
    
    public function createConnection(array $config): ConnectionInterface
    {
        $driver = $config['driver'] ?? 'mysql';

        switch ($driver) {
            case 'mysql':
                return new MysqlConnection($config);
            default: 
                throw new InvalidArgumentException("Unsupported database driver: {$driver}");
        }
    }

    public function getDefaultConnection(): string
    {
        return $this->default;
    }

    public function setDefaultConnection(string $name): void
    {
        if (!isset($this->configurations[$name])) {
            throw new InvalidArgumentException("Connection config [{$name}] is not defined.");
        }

        $this->default = $name;
    }

}
