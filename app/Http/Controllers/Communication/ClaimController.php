<?php

namespace App\Http\Controllers\Communication;

use App\Domains\Communication\Entities\Claim;
use App\Domains\Communication\Repositories\ClaimRepository;
use App\Domains\User\Repositories\DealerRepository;
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
        $query = $this->repo()->query();

        // ✅ 제목 검색
        $query->when(
            $title = $request->query('title'),
            fn($q) => $q->where('title', 'like', "%{$title}%")
        );

        // ✅ 상태 검색
        $query->when(
            $status = $request->query('status'),
            fn($q) => $q->where('status', $status)
        );

        // ✅ 제품 시리얼 검색
        $query->when(
            $serial = $request->query('product_serial'),
            fn($q) => $q->whereHas('product', fn($subQ) => 
                $subQ->where('serial_number', 'like', "%{$serial}%"))
        );

        // ✅ 작성자 검색
        $query->when(
            $author = $request->query('author'),
            fn($q) => $q->whereHas('user', fn($subQ) => 
                $subQ->where('name', 'like', "%{$author}%"))
        );

        // ✅ 대리점 검색
        $query->when(
            $dealerId = $request->query('dealer_id'),
            fn($q) => $q->where('dealer_id', $dealerId)
        );

        // ✅ 주문자 검색
        $query->when(
            $orderer = $request->query('orderer_name'),
            fn($q) => $q->where('orderer_name', 'like', "%{$orderer}%")
        );

        // ✅ 정렬
        $sort = $request->query('sort');
        if ($sort) {
            switch ($sort) {
                case 'created_at_desc':
                    $query->orderByDesc('created_at');
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at');
                    break;
                case 'title_asc':
                    $query->orderBy('title');
                    break;
                case 'title_desc':
                    $query->orderByDesc('title');
                    break;
                default:
                    $query->orderByDesc('created_at');
                    break;
            }
        } else {
            $query->orderByDesc('created_at');
        }

        $dealers = DealerRepository::make()
            ->withoutTrashed()    
            ->with(['user'])
            ->all();
            
        $dealers = array_filter($dealers, fn($dealer) => $dealer->user);

        $claims = $query->paginate(
            $request->query('perpage', 15),
            $request->query('page', 1)
        );

        return $this->render('admin.pages.claims.index', [
            'claims' => $claims,
            'query' => $request->query(),
            'dealers' => $dealers,
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
