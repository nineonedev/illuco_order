<?php 

namespace Framework\Database\ORM;

use Framework\Database\ORM\Entities\Entity;
use App\Domains\Permission\Repositories\PermissionRepository;
use PhpOffice\PhpSpreadsheet\Calculation\Token\Stack;

class PermissionMap
{
    protected static $map = [];
    protected static $registered = false;

    /**
     * @param array<class-string<Entity>, string[]> $config
     */
    public static function config(array $config): void
    {
        foreach ($config as $entityClass => $actions) {
            static::register($entityClass, $actions);
        }

        static::handle();
    }

    public static function register(string $class, array $actions = ['create', 'read', 'update', 'delete']): void
    {
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
                // PermissionRepository::firstOrCreate([
                //     'resource' => $resource,
                //     'action'   => $action,
                // ]);
            }
        }

        db()->connection()->commit();
        static::$registered = true;
    }
}
