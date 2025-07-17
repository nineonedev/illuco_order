<?php

namespace App\Http\Controllers\Communication;

use App\Domains\Communication\Entities\Claim;
use App\Domains\Communication\Repositories\ClaimRepository;
use App\Domains\Product\Repositories\ProductTemplateRepository;
use App\Domains\System\Entities\FileAttachment;
use App\Domains\System\Repositories\FileAttachmentRepository;
use App\Domains\System\Supports\FileAttachmentList;
use App\Http\Requests\Communication\SaveClaimRequest;
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

    protected function fileRepo(): FileAttachmentRepository
    {
        return FileAttachmentRepository::make();
    }

    public function index(Request $request)
    {
        $query = $this->repo()->with(['dealer.user'])->query();


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
            fn($q) => $q->where('product_serial_number', 'like', "%{$serial}%")
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

        $dealers = array_values(array_filter($dealers, fn($dealer) => $dealer->user));

        $claims = $query->paginate(
            $request->query('perpage', 15),
            $request->query('page', 1)
        );

        return $this->render('admin.pages.claims.index', [
            'claims' => $claims,
            'query' => $request->query(),
            'dealers' => $request->expectsJson()
                ? array_map(fn($d) => $d->toArray(), $dealers)
                : $dealers,
        ]);
    }

    public function create()
    {
        return $this->render('admin.pages.claims.create');
    }

    public function show(string $id)
    {
        $claim = $this->repo()
            ->with([FileAttachment::alias()])
            ->find($id);

        if (!$claim) {
            throw new RuntimeException("클레임을 찾을 수 없습니다.");
        }

        $claim->setRelation(
            FileAttachment::alias(),
            FileAttachmentList::make($claim->fileattachment)
        );

        $template = ProductTemplateRepository::make()
            ->query()
            ->with(['fileattachment'])
            ->where('model', $claim->product_model)
            ->first();

        $productImage = null; 

        if ($template && $template->fileattachment) {
            $productImage = $template->fileattachment[0]->upload_path;
        }

        return $this->render('admin.pages.claims.show', [
            'claim' => $claim,
            'template' => $template,
            'productImage' => $productImage,
        ]);
    }

    public function store(Request $request)
    {
        return $this->runInTransaction(function () use ($request) {
            $request->validateOrFail([
                'title' => 'required|string|maxLength:255',
                'content' => 'required|string',
                'product_serial_number' => 'required|string|maxLength:255',
            ]);

            $data = $request->all();
            $data['product_name'] = $data['product']['name'];
            $data['product_code'] = $data['product']['code'];
            $data['product_model'] = $data['product']['model'];
                
            $claim = new Claim($data);
            $claim->user_id = guard()->id();
            $claim->dealer_id = user()->isDealer() ? user()->dealer->id : null;
            
            $claim = ClaimRepository::make()->save($claim);
            
            if (!$claim) {
                throw new RuntimeException("클레임 저장에 실패했습니다.");
            }

            $this->fileRepo()->handleUpload($claim, $this->uploadConfig());

            return $this->render(null, ['claim' => $claim], '정상적으로 생성되었습니다.');
        });
    }

    public function destroy(string $id)
    {
        return $this->runInTransaction(function () use ($id) {
            $claim = $this->repo()->with([FileAttachment::class])->findOrFail($id);

            $this->fileRepo()->deleteAllFor($claim);

            if (!$this->repo()->delete($claim)) {
                throw new RuntimeException("클레임 삭제에 실패했습니다.");
            }

            return $this->render(null, [], '클레임이 성공적으로 삭제되었습니다.');
        });
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->body('ids', []);

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            return $this->render(null, [], "삭제할 클레임이 없습니다.");
        }

        return $this->runInTransaction(function () use ($ids) {
            $totalDeleted = 0;

            foreach ($ids as $id) {
                $claim = $this->repo()->with([FileAttachment::class])->find($id);
                if (!$claim) continue;

                if ($this->repo()->delete($claim)) {
                    $totalDeleted++;
                }
            }

            return $this->render(null, [], "선택된 클레임이 삭제되었습니다. (삭제된 수: {$totalDeleted})");
        });
    }

    public function update(string $id, Request $request)
    {
        return $this->runInTransaction(function () use ($id, $request) {
            $request->validateOrFail([
                'status' => 'required|string',
            ]);

            $claim = $this->repo()->findOrFail($id);
            $claim->status = $request->body('status');

            if (!$this->repo()->save($claim)) {
                throw new RuntimeException("클레임 상태 변경에 실패했습니다.");
            }

            // 처리 완료될 경우 이메일!

            return $this->render(null, ['claim' => $claim], '상태가 성공적으로 변경되었습니다.');
        });
    }


    protected function uploadConfig(): array
    {
        return [
            'attach_1' => 'uploaded|uploadedOk',
            'attach_2' => 'uploaded|uploadedOk',
            'attach_3' => 'uploaded|uploadedOk',
            'attach_4' => 'uploaded|uploadedOk',
            'attach_5' => 'uploaded|uploadedOk',
        ];
    }
}
