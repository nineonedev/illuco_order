<?php 

namespace Framework\Database\ORM;

use App\Domains\Auth\Repositories\PermissionRepository;
use Framework\Database\ORM\Entities\Entity;

class PermissionMap
{
    static array $allowedActions = ['create', 'read', 'update','delete'];
    protected static array $map = [];
    protected static bool $registered = false;
    public static function config(array $config): void
    {
        foreach ($config as $entityClass => $actions) {
            static::register($entityClass, $actions);
        }

        static::handle();
    }

    public static function register(string $class, ?array $actions = null): void
    {
        if ($actions === null){
            $actions = static::$allowedActions;
        }
        
        static::$map[$class] = $actions;
    }

    public static function permissionsFor(string $class): array
    {
        return static::$map[$class] ?? [];
    }

    public static function handle(): void
    {
        if (static::$registered) return;

        db()->connection()->beginTransaction();

        foreach (static::$map as $class => $actions) {
            if (!is_subclass_of($class, Entity::class)) {
                continue;
            }

            /** @var Entity $class */
            $resource = $class::alias();

            foreach ($actions as $action) {
                if (!in_array($action, static::$allowedActions)) {
                    continue;
                }

                PermissionRepository::firstOrCreate([
                    'resource' => $resource,
                    'action'   => $action,
                ]);
            }
        }

        db()->connection()->commit();
        static::$registered = true;
    }
}
