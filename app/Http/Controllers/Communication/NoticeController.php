<?php

namespace App\Http\Controllers\Communication;


use App\Domains\Communication\Entities\Notice;
use App\Domains\Communication\Enums\NoticeStatus;
use App\Domains\Communication\Repositories\NoticeRepository;
use App\Domains\System\Entities\FileAttachment;
use App\Domains\System\Repositories\FileAttachmentRepository;
use App\Domains\System\Supports\FileAttachmentList;
use App\Http\Requests\Communication\SaveNoticeRequest;
use Framework\Http\Request;
use Framework\Routing\Controller;
use RuntimeException;

class NoticeController extends Controller
{
    protected function repo(): NoticeRepository
    {
        return NoticeRepository::make();
    }

    protected function fileRepo(): FileAttachmentRepository
    {
        return FileAttachmentRepository::make();
    }

    public function index(Request $request)
    {
        $query = $this->repo()
            ->with([FileAttachment::class])
            ->query();

        $query
            ->when(
                $t = $request->query('title'),
                fn($q) => $q->where('title', 'like', "%{$t}%")
            )
            ->when(
                $s = $request->query('status'),
                fn($q) => $q->where('status', $s)
            )
            ->when(
                ($p = $request->query('is_pinned')) !== null && $p !== '',
                fn($q) => $q->where('is_pinned', (bool) $p)
            )
            ->when(
                $request->query('author'),
                fn($q, $author) => $q->whereHas('user', fn($subQ) => $subQ->where('name', 'like', "%{$author}%"))
            )
            ->when(
                $request->query('visible') === 'now',
                function ($q) {
                    $now = now();

                    $q->where('status', NoticeStatus::PUBLISHED)
                        ->where(fn($q) =>
                            $q->whereNull('visible_from')
                                ->orWhere('visible_from', '<=', $now)
                        )
                        ->where(fn($q) =>
                            $q->whereNull('visible_to')
                                ->orWhere('visible_to', '>=', $now)
                        );
                }
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
                    $query->orderByDesc('is_pinned');
                    $query->orderByDesc('created_at');
                    break;
            }
        } else {
            $query->orderByDesc('is_pinned');
            $query->orderByDesc('created_at');
        }

        return $this->render('admin.pages.notices.index', [
            'notices' => $query->paginate(
                $request->query('perpage', 15),
                $request->query('page', 1)
            ),
            'query' => $request->query(),
        ]);
    }


    public function create()
    {
        return $this->render('admin.pages.notices.create');
    }

    public function show(string $id)
    {
        return $this->render('admin.pages.notices.show', ['id' => $id]);
    }

    public function edit(string $id)
    {
        $notice = $this->repo()->with([FileAttachment::class])->find($id);
        if (!$notice) {
            throw new RuntimeException("정보를 찾을 수 없습니다.");
        }

        $notice->setRelation(FileAttachment::alias(), FileAttachmentList::make($notice->fileattachment));
        return $this->render('admin.pages.notices.edit', ['notice' => $notice]);
    }

    public function store(SaveNoticeRequest $request)
    {
        return $this->runInTransaction(function () use ($request) {
            $notice = new Notice($request->all());
            $notice->user_id = guard()->id();

            $notice = NoticeRepository::make()->save($notice);
            if (!$notice) {
                throw new RuntimeException("공지 생성에 실패했습니다.");
            }
            
            $this->fileRepo()->handleUpload($notice, $this->uploadConfig());
            
            $notice->mailToDealers(); 
            return $this->render(null, ['notice' => $notice], '정상적으로 생성되었습니다.');
        });
    }

    public function update(string $id, SaveNoticeRequest $request)
    {
        return $this->runInTransaction(function () use ($id, $request) {
            $notice = $this->repo()->findOrFail($id);
            $notice->fill($request->all());
            $notice->user_id = guard()->id();

            $notice = NoticeRepository::make()->save($notice);
            if (!$notice) {
                throw new RuntimeException("공지 수정에 실패했습니다.");
            }
            
            $this->fileRepo()->handleDelete($notice);
            $this->fileRepo()->handleUpload($notice, $this->uploadConfig());

            return $this->render(null, ['notice' => $notice], '정상적으로 수정되었습니다.');
        });
    }

    public function destroy(string $id)
    {
        return $this->runInTransaction(function () use ($id) {
            $notice = $this->repo()->with([FileAttachment::class])->findOrFail($id);
            $this->fileRepo()->deleteAllFor($notice);

            if (!$this->repo()->delete($notice)) {
                throw new RuntimeException("공지 삭제에 실패했습니다.");
            }

            return $this->render(null, [], '성공적으로 삭제되었습니다.');
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
