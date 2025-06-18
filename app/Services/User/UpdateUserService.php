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

class UpdateUserService extends Service
{
    protected Entity $userable;

    public function __construct(Entity $userable)
    {
        $this->userable = $userable;
    }

    protected function handle(array $payload): array
    {
        $userableClass = get_class($this->userable);

        // 1. userable 업데이트
        $this->userable = $userableClass::resolveRepository()->save($this->userable);

        // 2. user 검색 (morph 관계)
        $user = UserRepository::queryStatic()
            ->where(User::getMorphType(), $userableClass::alias())
            ->where(User::getMorphId(), $this->userable->getPrimaryKey())
            ->first();

        if (!$user) {
            throw new ValidationException("연결된 정보가 없습니다.");
        }

        // 3. 이메일 중복 검사 (본인 제외)
        $email = $payload['email'] ?? null;
        if ($email && UserRepository::queryStatic()
            ->where('email', $email)
            ->where('id', '!=', $user->getPrimaryKey())
            ->exists()) {
            throw new ValidationException(transfer('rule.unique', 'system.email'));
        }

        // 4. user 업데이트
        $attributes = User::morphAttributes(
            $userableClass::alias(),
            $this->userable->getPrimaryKey(),
            $payload
        );

        $user->fill($attributes);
        $user = UserRepository::make()->save($user);

        // 5. 관계 재지정
        $user->setRelation($userableClass::alias(), $this->userable->toArray());
        return ['user' => $user->toArray()];
    }
}
