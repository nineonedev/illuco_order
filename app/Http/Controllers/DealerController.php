<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Entities\Role;
use App\Domains\Auth\Repositories\RoleRepository;
use App\Domains\Order\Entities\Customer;
use App\Domains\User\Entities\Dealer;
use App\Domains\User\Entities\User;
use App\Domains\User\Enums\UserType;
use App\Domains\User\Repositories\DealerRepository;
use App\Domains\User\Repositories\UserRepository;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Services\Auth\SaveRoleService;
use App\Services\User\DeleteUserService;
use App\Services\User\RegisterUserService;
use App\Services\User\UpdateUserService;
use Exception;
use Framework\Database\ORM\Entities\Entity;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Framework\Validation\Validator;
use RuntimeException;

class DealerController extends Controller
{
    protected function repo(): DealerRepository
    {
        return DealerRepository::make();
    }

    public function index(Request $request)
    {
        $query = $this->repo()->with(['user'])->query();

        $query->when($keyword = $request->query('q'), function ($q) use ($keyword) {
            $q->where('code', 'like', "%{$keyword}%")
                ->orWhere('phone', 'like', "%{$keyword}%");
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
        $dealer = $this->repo()->with(['user'])->find($id);

        if (!$dealer) {
            throw new RuntimeException("대리점 정보를 찾을 수 없습니다.");
        }

        return $this->render('admin.pages.dealers.edit', [
            'dealer' => $dealer,
        ]);
    }

    public function store(RegisterRequest $request)
    {
        return $this->runInTransaction(function() use ($request) {
            $user = new User($request->safe());
            $user->type = UserType::DEALER; 
            $user = UserRepository::make()->save($user);

            if (!$user) {
                throw new RuntimeException("사용자 생성에 실패하였습니다.");
            }

            $dealerRole = RoleRepository::make()
                ->query()
                ->where('name', 'admin')
                ->first();
            
            if (!$dealerRole) {
                throw new RuntimeException("대리점 전용 권한을 찾을 수 없습니다.");
            }

            $user->roles()->attach($dealerRole);

            $validator = Validator::make($request->all(), [
                'country' => 'required',
                'code' => 'required|unique:dealers',
                'address' => 'nullable',
                'description' => 'nullable',
            ]);

            $validator->validateOrFail();
            $dealer = $validator->validated();
            $dealer = new Dealer($dealer); 
            $dealer->id = $user->id; 
            $dealer = DealerRepository::make()->save($dealer); 

            if (!$dealer) { 
                throw new RuntimeException("대리점 생성에 실패하였습니다.");
            }

            return $this->render(null, ['dealer' => $dealer->toArray()], '성공적으로 생성되었습니다.');
        });
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
