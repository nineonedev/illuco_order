<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Repositories\RoleRepository;
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
use RuntimeException;

class DealerController extends Controller
{
    public function index(Request $request)
    {
        $query = UserRepository::make()
            ->with([UserType::DEALER])
            ->query()
            ->where('type', UserType::DEALER);

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
        $dealer = UserRepository::make()->findOrFail($id); 
        $dealer->load([UserType::DEALER]);

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
                ->where('name', 'dealer')
                ->first();
            
            if (!$dealerRole) {
                throw new RuntimeException("대리점 전용 권한을 찾을 수 없습니다.");
            }

            $user->roles()->attach($dealerRole);

            $validator = Validator::make($request->body($user->type), [
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
        return $this->runInTransaction(function() use ($id) {
            $employee = UserRepository::make()->findOrFail($id);
            $success = UserRepository::make()->delete($employee); 

            if (!$success) {
                throw new RuntimeException("대리점 삭제에 실패하였습니다.");
            }

            return $this->render(null, [], '정상적으로 삭제되었습니다.');
        });
    }
}
