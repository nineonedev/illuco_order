<?php

namespace App\Services\User;

use App\Domains\User\Entities\User;
use App\Domains\User\Repositories\UserRepository;
use App\Supports\Services\Service;
use Framework\Database\ORM\Entities\Entity;
use Framework\Support\Exceptions\ValidationException;

class RegisterUserService extends Service
{
    protected Entity $userable;

    public function __construct(Entity $userable)
    {
        $this->userable = $userable;
    }

    /**
     * 실제 실행 로직 (BaseService에서 runInTransaction에 의해 호출됨)
     */
    protected function handle(array $payload): array
    {
        /** @var class-string<Entity> $userableClass */
        $userableClass = get_class($this->userable);

        // userable 먼저 저장
        $this->userable = $userableClass::resolveRepository()->save($this->userable);

        $attributes = User::morphAttributes($userableClass::alias(), $this->userable->getPrimaryKey(), $payload);

        $email = $attributes['email'] ?? null;
        if ($email && UserRepository::existsBy(['email' => $email])) {
            throw new ValidationException(transfer('rule.unique', 'system.email'));
        }

        // user 생성
        $user = User::make($attributes);
        $user = UserRepository::make()->save($user);

        $user->setRelation('userable', $this->userable->toArray());
        return ['user' => $user->toArray()];
    }
}