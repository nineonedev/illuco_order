<?php

namespace App\Http\Controllers\Communication;

use App\Domains\Communication\Entities\Claim;
use App\Domains\Communication\Repositories\ClaimRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class ClaimController extends Controller
{
    protected function repo(): ClaimRepository
    {
        return ClaimRepository::make();
    }

    public function index(Request $request)
    {
        $perpage = $request->query('perpage', 15);
        $page = $request->query('page', 1);
        
        $query = $this->repo()->query();
        
        // 'title' 쿼리 파라미터가 존재하면 이를 조건에 추가
        $query->when($title = $request->query('title'), function ($q) use ($title) {
            $q->where('title', 'like', "%{$title}%");
        });

        $claims = $query->paginate($perpage, $page);
        
        return $this->render('admin.pages.claims.index', [
            'claims' => $claims,
            'query'  => $request->query(),
        ]);
    }

    public function show(string $id)
    {
        $claim = $this->repo()->find($id);

        if (!$claim) {
            throw new RuntimeException("클레임을 찾을 수 없습니다.");
        }

        return $this->render('admin.pages.claims.show', ['claim' => $claim]);
    }

    public function create()
    {
        return $this->render('admin.pages.claims.create');
    }

    public function edit(string $id)
    {
        $claim = $this->repo()->find($id);

        if (!$claim) {
            throw new RuntimeException("클레임을 찾을 수 없습니다.");
        }

        return $this->render('admin.pages.claims.edit', ['claim' => $claim]);
    }

    public function store(Request $request)
    {
        return $this->runInTransaction(function () use ($request) {
            // 클레임 데이터 유효성 검증
            $request->validateOrFail([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                // 필요한 다른 필드들에 대해서도 검증 추가
            ]);

            // 클레임 생성
            $claim = new Claim($request->all());
            $claim = $this->repo()->save($claim);

            if (!$claim) {
                throw new RuntimeException("클레임 저장에 실패했습니다.");
            }

            return $this->render('admin.pages.claims.index', [
                'claim' => $claim,
            ], '클레임이 성공적으로 저장되었습니다.');
        });
    }

    public function update(string $id, Request $request)
    {
        return $this->runInTransaction(function () use ($id, $request) {
            // 클레임 조회
            $claim = $this->repo()->find($id);

            if (!$claim) {
                throw new RuntimeException("클레임을 찾을 수 없습니다.");
            }

            // 클레임 데이터 유효성 검증
            $request->validateOrFail([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                // 필요한 다른 필드들에 대해서도 검증 추가
            ]);

            // 클레임 데이터 업데이트
            $claim->fill($request->all());
            $claim = $this->repo()->save($claim);

            if (!$claim) {
                throw new RuntimeException("클레임 업데이트에 실패했습니다.");
            }

            return $this->render('admin.pages.claims.show', [
                'claim' => $claim,
            ], '클레임이 성공적으로 업데이트되었습니다.');
        });
    }

    public function destroy(string $id)
    {
        return $this->runInTransaction(function () use ($id) {
            // 클레임 조회
            $claim = $this->repo()->find($id);

            if (!$claim) {
                throw new RuntimeException("클레임을 찾을 수 없습니다.");
            }

            // 클레임 삭제
            if (!$this->repo()->delete($claim)) {
                throw new RuntimeException("클레임 삭제에 실패했습니다.");
            }

            return $this->render('admin.pages.claims.index', [], '클레임이 성공적으로 삭제되었습니다.');
        });
    }
}
