<?php 

namespace Framework\Database\ORM;

use App\Domains\Auth\Repositories\PermissionRepository;
use Framework\Database\ORM\Entities\Entity;

class PermissionMap
{
    static array $allowedActions = ['create', 'read', 'update', 'delete'];
    public static array $map = [];
    protected static bool $registered = false;

    // 엔티티에 대한 권한 설정을 추가하는 메서드
    public static function config(array $config): void
    {
        foreach ($config as $entityClass => $actions) {
            static::register($entityClass, $actions);
        }

        static::handle();
    }

    // 권한 등록 메서드 (기본적으로 'create', 'read', 'update', 'delete'가 모두 활성화됨)
    public static function register(string $class, ?array $actions = null): void
    {
        if ($actions === null) {
            $actions = static::$allowedActions;
        }

        static::$map[$class] = $actions;
    }

    // 클래스에 대한 권한 정보를 가져오는 메서드
    public static function permissionsFor(string $class): array
    {
        return static::$map[$class] ?? [];
    }

    // 권한을 처리하는 메서드
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

                PermissionRepository::queryStatic()->firstOrCreate([
                    'resource' => $resource,
                    'action'   => $action,
                ]);
            }
        }

        db()->connection()->commit();
        static::$registered = true;
    }
}
