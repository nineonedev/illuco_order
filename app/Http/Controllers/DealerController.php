<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Repositories\RoleRepository;
use App\Domains\Product\Repositories\CategoryRepository;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Entities\User;
use App\Domains\User\Enums\UserType;
use App\Domains\User\Repositories\DealerRepository;
use App\Domains\User\Repositories\UserRepository;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\UpdateUserRequest;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Framework\Validation\Validator;
use Framework\Support\Facades\Hash;
use RuntimeException;

class DealerController extends Controller
{
    public function index(Request $request)
    {
        $query = UserRepository::make()
            ->with([UserType::DEALER])
            ->query()
            ->where('type', UserType::DEALER);

        // 대리점명
        $query->when(
            $name = $request->query('name'),
            fn($q) => $q->where('name', 'like', "%{$name}%")
        );

        // 국가
        $query->when(
            $country = $request->query('country'),
            function ($q) use ($country) {
                $q->whereHas(UserType::DEALER, fn($dealerQ) =>
                    $dealerQ->where('country', $country)
                );
            }
        );

        // 코드
        $query->when(
            $code = $request->query('code'),
            function ($q) use ($code) {
                $q->whereHas(UserType::DEALER, fn($dealerQ) =>
                    $dealerQ->where('code', 'like', "%{$code}%")
                );
            }
        );

        // 연락처
        $query->when(
            $phone = $request->query('phone'),
            fn($q) => $q->where('phone', 'like', "%{$phone}%")
        );

        // 이메일
        $query->when(
            $email = $request->query('email'),
            fn($q) => $q->where('email', 'like', "%{$email}%")
        );

        // 정렬
        $sort = $request->query('sort');
        if ($sort) {
            switch ($sort) {
                case 'created_at_asc':
                    $query->orderBy('created_at');
                    break;
                case 'created_at_desc':
                    $query->orderByDesc('created_at');
                    break;
                case 'name_asc':
                    $query->orderBy('name');
                    break;
                case 'name_desc':
                    $query->orderByDesc('name');
                    break;
                default:
                    $query->orderByDesc('created_at');
                    break;
            }
        } else {
            $query->orderByDesc('created_at');
        }

        $perpage = $request->query('perpage', 15);
        $page = $request->query('page', 1);
        $dealers = $query->paginate($perpage, $page);

        return $this->render('admin.pages.dealers.index', [
            'dealers'   => $dealers,
            'query'     => $request->query(),
            'countries' => __('system.countries'),
        ]);
    }

    public function create()
    {
        $categories = CategoryRepository::make()->all();
        return $this->render('admin.pages.dealers.create', [
            'categories' => $categories,
        ]);
    }

    public function edit(string $id)
    {
        $dealer = UserRepository::make()->findOrFail($id);
        $dealer->load([UserType::DEALER]);
        $categories = CategoryRepository::make()->all();

        if (!$dealer) {
            throw new RuntimeException("대리점 정보를 찾을 수 없습니다.");
        }

        return $this->render('admin.pages.dealers.edit', [
            'dealer'     => $dealer,
            'categories' => $categories,
        ]);
    }

    public function store(RegisterRequest $request)
    {
        return $this->runInTransaction(function () use ($request) {
            $user = new User($request->safe());
            $user->type = UserType::DEALER;
            $user = UserRepository::make()->save($user);

            if (!$user) {
                throw new RuntimeException("사용자 생성에 실패하였습니다.");
            }

            $dealerRole = RoleRepository::make()
                ->query()
                ->where('name', 'dealer')
                ->first();

            if (!$dealerRole) {
                throw new RuntimeException("대리점 전용 권한을 찾을 수 없습니다.");
            }

            $user->roles()->attach($dealerRole);

            $validator = Validator::make($request->body($user->type), [
                'country'     => 'required',
                'code'        => 'required|unique:dealers',
                'address'     => 'nullable',
                'description' => 'nullable',
                'category_id' => 'nullable|integer',
            ]);

            $validator->validateOrFail();
            $dealerData = $validator->validated();

            $dealer = new Dealer($dealerData);
            $dealer->id = $user->id;
            $dealer->category_id = $dealerData['category_id'] ?: null;
            $dealer = DealerRepository::make()->save($dealer);

            if (!$dealer) {
                throw new RuntimeException("대리점 생성에 실패하였습니다.");
            }

            return $this->render(null, ['dealer' => $dealer->toArray()], '성공적으로 생성되었습니다.');
        });
    }

    public function update(string $id, UpdateUserRequest $request)
    {
        return $this->runInTransaction(function () use ($id, $request) {
            $user = UserRepository::make()->findOrFail($id);
            $data = $request->safe();
            $user->load([UserType::DEALER]);

            if (empty($data['password'])) {
                unset($data['password']);
            }

            $isActive = $request->body('is_active') ? true : false;
            $data['is_active'] = $isActive;

            $user->fill($data);
            $user = UserRepository::make()->save($user);

            if (!$user) {
                throw new RuntimeException("대리점 기본정보 수정에 실패하였습니다.");
            }

            $dealerData = $request->body($user->type);
            $dealer = $user->{$user->type};

            $dealer = $dealer->fill($dealerData);

            if (!user()->isDealer()) {
                $dealer->category_id = $dealerData['category_id'] ?: null;
            }

            $dealer = DealerRepository::make()->save($dealer);

            if (!$dealer) {
                throw new RuntimeException("대리점 기본정보 수정에 실패하였습니다.");
            }

            $dealer->setRelation('user', $user);

            return $this->render(null, [
                'dealer' => $dealer,
            ], '정상적으로 수정되었습니다.');
        });
    }

    public function destroy(string $id)
    {
        return $this->runInTransaction(function () use ($id) {
            $employee = UserRepository::make()->findOrFail($id);
            $success = UserRepository::make()->delete($employee);

            if (!$success) {
                throw new RuntimeException("대리점 삭제에 실패하였습니다.");
            }

            return $this->render(null, [], '정상적으로 삭제되었습니다.');
        });
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->body('ids', []);

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            return $this->render(null, [], "삭제할 항목이 없습니다.");
        }

        return $this->runInTransaction(function () use ($ids) {
            $totalDeleted = 0;

            foreach ($ids as $id) {
                $dealer = UserRepository::make()
                    ->with([UserType::DEALER])
                    ->find($id);

                if (!$dealer) {
                    continue;
                }

                $success = UserRepository::make()->delete($dealer);
                if ($success) {
                    $totalDeleted++;
                }
            }

            return $this->render(null, [], "선택된 대리점이 삭제되었습니다. (삭제된 수: {$totalDeleted})");
        });
    }

    /**
     * 비밀번호 변경 (관리자에서 직접 변경)
     * Route: PUT /dealers/{id}/password  -> name: dealers.password.update
     */
    public function updatePassword(Request $request, string $id)
    {
        // 간단 검증: 최소 길이 8, 확인값 일치 등
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:8', // confirmed -> password_confirmation 필요
        ]);
        $validator->validateOrFail();

        return $this->runInTransaction(function () use ($id, $request) {
            $user = UserRepository::make()->findOrFail($id);

            // 실제 해시 저장 (UserRepository 내부에서 해시한다면 이 줄은 repo 정책에 맞게 조정)
            $user->password = Hash::make($request->body('password'));

            // 비번 수동 변경 시 기존 임시비번은 즉시 무효화
            $user->temp_password_hash = null;
            $user->temp_password_expires_at = null;

            $saved = UserRepository::make()->save($user);
            if (!$saved) {
                throw new RuntimeException('비밀번호 변경에 실패하였습니다.');
            }

            return $this->render(null, [], '비밀번호가 변경되었습니다.');
        });
    }

    /**
     * 임시 비밀번호 발급
     * Route: POST /dealers/{id}/temp-password  -> name: dealers.temp-password.issue
     * 응답: temp_password(평문, 1회 노출), expires_at
     */
    public function issueTempPassword(Request $request, string $id)
    {
        $hours = (int)($request->body('hours', 24));
        if ($hours < 1) $hours = 24;

        return $this->runInTransaction(function () use ($id, $hours) {
            $user = UserRepository::make()->findOrFail($id);

            $plain = $this->readableToken(12);
            $hash  = Hash::make($plain);

            // 문자열로 저장
            $expiresAt = (new \DateTimeImmutable('now'))
                            ->modify("+{$hours} hours")
                            ->format('Y-m-d H:i:s');

            $user->temp_password_hash = $hash;
            $user->temp_password_expires_at = $expiresAt;

            $saved = UserRepository::make()->save($user);
            if (!$saved) {
                throw new RuntimeException('임시 비밀번호 생성에 실패하였습니다.');
            }

            return $this->render(null, [
                'temp_password' => $plain,
                'expires_at'    => $expiresAt,
            ], '임시 비밀번호가 생성되었습니다.');
        });
    }


    /**
     * 임시 비밀번호 회수(무효화)
     * Route: DELETE /dealers/{id}/temp-password  -> name: dealers.temp-password.revoke
     */
    public function revokeTempPassword(string $id)
    {
        return $this->runInTransaction(function () use ($id) {
            $user = UserRepository::make()->findOrFail($id);
            $user->temp_password_hash = null;
            $user->temp_password_expires_at = null;

            $saved = UserRepository::make()->save($user);
            if (!$saved) {
                throw new RuntimeException('임시 비밀번호 해제에 실패하였습니다.');
            }

            return $this->render(null, [], '임시 비밀번호를 해제했습니다.');
        });
    }

    /**
     * 사람이 읽기 쉬운 임시 토큰 생성 (혼동문자 제외)
     */
    private function readableToken(int $len = 12): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789';
        $n = strlen($alphabet);
        $bytes = random_bytes($len);
        $out = '';
        for ($i = 0; $i < $len; $i++) {
            $out .= $alphabet[ord($bytes[$i]) % $n];
        }
        return $out;
    }
}
