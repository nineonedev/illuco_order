<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Entities\Role;
use App\Domains\Auth\Repositories\RoleRepository;
use App\Domains\Order\Entities\Customer;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Entities\User;
use App\Domains\User\Repositories\DealerRepository;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\User\DeleteUserService;
use App\Services\User\RegisterUserService;
use App\Services\User\UpdateUserService;
use Exception;
use Framework\Database\ORM\Entities\Entity;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class DealerController extends Controller
{
    protected function repo(): DealerRepository
    {
        return DealerRepository::make();
    }

    public function index(Request $request)
    {
        $query = $this->repo()->with([User::alias()])->query();

        $query->when($keyword = $request->query('q'), function ($q) use ($keyword) {
            $q->where('code', 'like', "%{$keyword}%")
              ->orWhere('phone_number', 'like', "%{$keyword}%");
        });

        $perpage = $request->query('perpage', 15);
        $page = $request->query('page', 1);
        $dealers = $query->paginate($perpage, $page);

        return $this->render('admin.pages.dealers.index', [
            'dealers' => $dealers,
            'query'   => $request->query(),
        ]);
    }

    public function create()
    {
        return $this->render('admin.pages.dealers.create');
    }

    public function edit(string $id)
    {
        $dealer = $this->repo()->with([User::alias()])->find($id);

        if (!$dealer) {
            throw new RuntimeException("대리점 정보를 찾을 수 없습니다.");
        }

        return $this->render('admin.pages.dealers.edit', [
            'dealer' => $dealer,
        ]);
    }

    public function store(RegisterRequest $request)
    {
        // 1. Dealer 엔티티 생성
        $dealer = new Dealer($request->all());

        // 2. 사용자 등록 서비스 호출
        $result = (new RegisterUserService($dealer))
            ->runInTransaction($request->safe());

        // 3. 'dealer' 역할을 가져옴
        $dealerRole = RoleRepository::make()
            ->with(['users']) // 'users' 관계를 가져옴
            ->query()
            ->where('name', 'dealer')
            ->first();

        // 4. Role이 존재하면 사용자 연결
        if ($dealerRole) {
            $dealerRole->users()->attach($dealer->user);
        }

        // 5. 결과 반환
        return $result->toResponse();
    }

    public function update(string $id, UpdateUserRequest $request)
    {
        $dealer = $this->repo()->findOrFail($id);
        $dealer->fill($request->safe());

        $data = $request->safe();

        if (empty($data['password'])) {
            unset($data['password']); 
        }

        $result = (new UpdateUserService($dealer))
            ->runInTransaction($data);

        return $result->toResponse();
    }

    public function destroy(string $id)
    {
        $dealer = $this->repo()->findOrFail($id);
        $result = (new DeleteUserService($dealer))->runInTransaction([]);
        return $result->toResponse();
    }

    protected function save(Dealer $dealer): void
    {
        if (!$this->repo()->save($dealer)) {
            throw new RuntimeException("대리점 저장에 실패했습니다.");
        }
    }
}
