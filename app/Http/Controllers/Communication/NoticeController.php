<?php

namespace App\Http\Controllers\Communication;


use App\Domains\Communication\Entities\Notice;
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
        $query = $this->repo()->with([FileAttachment::class])->query();

        $query->when($t = $request->query('title'), fn($q) => $q->where('title', 'like', "%{$t}%"))
              ->when($s = $request->query('status'), fn($q) => $q->where('status', $s))
              ->when(!is_null($p = $request->query('is_pinned')), fn($q) => $q->where('is_pinned', (bool) $p))
              ->when($request->query('visible') === 'now', function ($q) {
                  $now = now();
                  $q->where('status', Notice::STATUS_PUBLISHED)
                    ->where(fn($q) => $q->whereNull('visible_from')->orWhere('visible_from', '<=', $now))
                    ->where(fn($q) => $q->whereNull('visible_to')->orWhere('visible_to', '>=', $now));
              })
              ->orderByDesc('is_pinned');

        return $this->render('admin.pages.notices.index', [
            'notices' => $query->paginate($request->query('perpage', 15), $request->query('page', 1)),
            'query'   => $request->query(),
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

            $this->save($notice);
            $this->fileRepo()->handleUpload($notice, $this->uploadConfig());

            return $this->render(null, ['notice' => $notice]);
        });
    }

    public function update(string $id, SaveNoticeRequest $request)
    {
        return $this->runInTransaction(function () use ($id, $request) {
            $notice = $this->repo()->findOrFail($id);
            $notice->fill($request->all());
            $notice->user_id = guard()->id();

            $this->save($notice);
            $this->fileRepo()->handleDelete($notice);
            $this->fileRepo()->handleUpload($notice, $this->uploadConfig());

            return $this->render(null, ['notice' => $notice]);
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

    protected function save(Notice $notice): void
    {
        if (!$this->repo()->save($notice)) {
            throw new RuntimeException("공지 저장에 실패했습니다.");
        }
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
