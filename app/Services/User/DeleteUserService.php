<?php

namespace App\Services\User;

use App\Domains\User\Entities\User;
use App\Domains\User\Repositories\UserRepository;
use App\Supports\Services\Service;
use Exception;
use Framework\Database\ORM\Entities\Entity;
use Framework\Support\Exceptions\ValidationException;

class DeleteUserService extends Service
{
    protected Entity $userable;

    public function __construct(Entity $userable)
    {
        $this->userable = $userable;
    }

    protected function handle(array $payload): array
    {
        $userableClass = get_class($this->userable);

        // 1. 연결된 user 찾기
        $user = UserRepository::queryStatic()
            ->where(User::getMorphType(), $userableClass::alias())
            ->where(User::getMorphId(), $this->userable->getPrimaryKey())
            ->first();

        // 2. user 삭제
        if ($user) {
            if (!UserRepository::make()->delete($user)) {
                throw new ValidationException("사용자 삭제에 실패했습니다.");
            }
        }

        // 3. userable 삭제
        $userableRepo = $userableClass::resolveRepository();
        
        if (!$userableRepo->delete($this->userable)) {
            throw new ValidationException("연결된 개체 삭제에 실패했습니다.");
        }

        return ['message' => '삭제되었습니다.'];
    }
}
