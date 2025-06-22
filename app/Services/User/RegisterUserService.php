<?php

namespace App\Services\User;

use App\Domains\Auth\Entities\Role;
use App\Domains\Auth\Repositories\RoleRepository;
use App\Domains\User\Entities\Admin;
use App\Domains\User\Entities\User;
use App\Domains\User\Repositories\UserRepository;
use App\Services\Auth\SaveRoleService;
use App\Supports\Services\Service;
use Exception;
use Framework\Database\ORM\Entities\Entity;
use Framework\Support\Exceptions\ValidationException;

class RegisterUserService extends Service
{
    protected Entity $userable;

    public function __construct(Entity $userable)
    {
        $this->userable = $userable;
    }

    protected function handle(array $payload): array
    {
        /** @var class-string<Entity> $userableClass */
        $userableClass = get_class($this->userable);

        // 1. userable 저장 (ex. Admin)
        $this->userable = $userableClass::resolveRepository()->save($this->userable);

        // 2. User 생성 속성 준비 (morph 포함)
        $attributes = User::morphAttributes(
            $userableClass::alias(),
            $this->userable->getPrimaryKey(),
            $payload
        );

        // 3. 이메일 중복 검사
        $email = $attributes['email'] ?? null;
        if ($email && UserRepository::queryStatic()->existsBy(['email' => $email])) {
            throw new ValidationException(transfer('rule.unique', 'system.email'));
        }

        // 4. User 생성
        $user = User::make($attributes);
        $user = UserRepository::make()->save($user);

        $this->userable->load(['user']);

        // 5. 관리자 Role이 없다면 생성
        $roleRepo = RoleRepository::make();
        $adminRole = $roleRepo->query()->where('name', 'admin')->first();

        if ($this->userable instanceof Admin) {
            $adminRole = [
                'name' => 'admin',
                'description' => '최고 관리자',
            ];

            $service = new SaveRoleService();
            $result = $service->run([
                'role' => $adminRole,
                'permissions' => context()->get('permissions', []),
            ]);

            $adminRole = Role::make($result->getData()['role']);
            $user->setRelation(Role::alias(), $adminRole);
            $user->roles()->attach($adminRole);
        }

        $user->setRelation($userableClass::alias(), $this->userable->toArray());
        return ['user' => $user->toArray()];
    }
}
