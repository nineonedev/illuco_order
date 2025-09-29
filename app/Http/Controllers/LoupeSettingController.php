<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Product\Entities\LoupeSetting;
use App\Domains\Product\Repositories\LoupeFrameColorRepository;
use App\Domains\Product\Repositories\LoupeSettingRepository;
use App\Domains\Product\Repositories\ProductTemplateRepository;
use App\Domains\System\Entities\FileAttachment;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Framework\Validation\Validator;
use RuntimeException;

class LoupeSettingController extends Controller
{
    /* ============================================================
     |  Repositories (helper)
     |============================================================ */
    public function repo(): LoupeSettingRepository
    {
        return LoupeSettingRepository::make();
    }

    public function colorRepo(): LoupeFrameColorRepository
    {
        return LoupeFrameColorRepository::make();
    }

    public function templateRepo(): ProductTemplateRepository
    {
        return ProductTemplateRepository::make();
    }

    /* ============================================================
     |  Index (목록 + 필터/정렬/페이지네이션)
     |============================================================ */
    public function index(Request $request)
    {
        $query = $this->repo()
            ->with(['template.fileattachment', 'frameColors'])
            ->query();

        // ------- 검색 필터 -------
        $query->when(
            $name = $request->query('name'),
            static fn($q) => $q->whereHas('template', static fn($tq) => $tq->where('name', 'like', "%{$name}%"))
        );

        $query->when(
            $code = $request->query('code'),
            static fn($q) => $q->whereHas('template', static fn($tq) => $tq->where('code', 'like', "%{$code}%"))
        );

        $query->when(
            $model = $request->query('model'),
            static fn($q) => $q->whereHas('template', static fn($tq) => $tq->where('model', 'like', "%{$model}%"))
        );

        // 특정 프레임 컬러로 필터링 (color_id=)
        $query->when(
            $colorId = (int) $request->query('color_id', 0),
            static fn($q) => $q->whereHas('frameColors', static fn($cq) => $cq->where('id', $colorId))
        );

        // ------- 정렬 -------
        $sort = (string) $request->query('sort', '');
        if ($sort) {
            switch ($sort) {
                case 'name_asc':
                    $query
                        ->leftJoin('product_templates as pt', 'loupe_settings.template_id', '=', 'pt.id')
                        ->orderBy('pt.name', 'asc');
                    break;
                case 'name_desc':
                    $query
                        ->leftJoin('product_templates as pt', 'loupe_settings.template_id', '=', 'pt.id')
                        ->orderBy('pt.name', 'desc');
                    break;
                case 'created_at_asc':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'created_at_desc':
                    $query->orderBy('created_at', 'desc');
                    break;
                default:
                    $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        // ------- 페이지네이션 -------
        $perPage   = (int) $request->query('perpage', 15);
        $page      = (int) $request->query('page', 1);
        $paginator = $query->paginate($perPage, $page);
        $settings  = $request->expectsJson() ? $paginator->toArray() : $paginator;
        // 프레임 컬러 (필터/표시용)
        $colors = $this->colorRepo()->listActiveOrdered();

        return $this->render('admin.pages.loupe-settings.index', [
            'settings' => $settings,
            'colors'   => $request->expectsJson()
                ? array_map(static fn($c) => (array) $c, $colors)
                : $colors,
            'query'    => $request->query(),
        ]);
    }

    /* ============================================================
    |  Create (화면)
     |============================================================ */
    public function create()
    {
        $colors = $this->colorRepo()->listActiveOrdered();

        return $this->render('admin.pages.loupe-settings.create', [
            'colors' => $colors,
        ]);
    }

    /* ============================================================
    |  Store (POST /loupe-settings)
     |============================================================ */
    public function store(Request $request)
    {
        $payload = $this->normalizePayload($request);

        Validator::make($payload, [
            'template_id'        => 'required|number',

            'vd_min'             => 'nullable|number',
            'vd_max'             => 'nullable|number',

            'pd_right_min'       => 'nullable|number',
            'pd_right_max'       => 'nullable|number',
            'pd_left_min'        => 'nullable|number',
            'pd_left_max'        => 'nullable|number',
            'pd_total_distance'  => 'nullable|number',

            'wd_min'             => 'nullable|number',
            'wd_max'             => 'nullable|number',

            'frame_color_ids'    => 'nullable|array',
        ])->validateOrFail();

        return $this->runInTransaction(function () use ($payload) {
            // 동일 template 중복 방지
            $exists = $this->repo()
                ->query()
                ->where('template_id', (int) $payload['template_id'])
                ->first();

            if ($exists) {
                throw new RuntimeException('해당 제품의 루페 세팅이 이미 존재합니다.');
            }

            $data = [
                'template_id'        => (int) $payload['template_id'],
                'vd_min'             => $this->numOrNull($payload['vd_min'] ?? null),
                'vd_max'             => $this->numOrNull($payload['vd_max'] ?? null),
                'pd_right_min'       => $this->numOrNull($payload['pd_right_min'] ?? null),
                'pd_right_max'       => $this->numOrNull($payload['pd_right_max'] ?? null),
                'pd_left_min'        => $this->numOrNull($payload['pd_left_min'] ?? null),
                'pd_left_max'        => $this->numOrNull($payload['pd_left_max'] ?? null),
                'pd_total_distance'  => $this->numOrNull($payload['pd_total_distance'] ?? null),
                'wd_min'             => $this->numOrNull($payload['wd_min'] ?? null),
                'wd_max'             => $this->numOrNull($payload['wd_max'] ?? null),
            ];
            
            $model = new LoupeSetting($data);

            dd($data,$model);

            $this->repo()->save($model);

            // 프레임 컬러 sync
            $colorIds = $this->filterValidColorIds($payload['frame_color_ids'] ?? []);
            $model->frameColors()->sync($colorIds);

            return $this->render(null, [
                'id'       => $model->id,
                // 'redirect' => route('admin.loupe_settings.edit', ['id' => $model->id]),
                'redirect' => route('admin.loupe_settings.index'),
            ], '루페 세팅이 생성되었습니다.');
        });
    }

    /* ============================================================
    |  Edit (화면)
     |============================================================ */
    public function edit(string $id)
    {
        /** @var LoupeSetting $setting */
        $setting = $this->repo()
            ->with(['template' => [FileAttachment::class], 'frameColors'])
            ->findOrFail($id);

        $colors = $this->colorRepo()->listActiveOrdered();

        return $this->render('admin.pages.loupe-settings.edit', [
            'setting' => $setting,
            'colors'  => $colors,
        ]);
    }

    /* ============================================================
    |  Update (PUT /loupe-settings/{id})
     |============================================================ */
    public function update(Request $request, string $id)
    {
        $payload = $this->normalizePayload($request);

        // 전달된 필드만 검증
        $rules = [
            'template_id'        => 'number',
            'vd_min'             => 'number',
            'vd_max'             => 'number',
            'pd_right_min'       => 'number',
            'pd_right_max'       => 'number',
            'pd_left_min'        => 'number',
            'pd_left_max'        => 'number',
            'pd_total_distance'  => 'number',
            'wd_min'             => 'number',
            'wd_max'             => 'number',
            'frame_color_ids'    => 'array',
        ];

        $toValidate = [];
        foreach ($rules as $k => $rule) {
            if (\array_key_exists($k, $payload)) {
                $toValidate[$k] = $rule;
            }
        }
        if ($toValidate) {
            Validator::make($payload, $toValidate)->validateOrFail();
        }

        return $this->runInTransaction(function () use ($id, $payload) {
            /** @var LoupeSetting $model */
            $model = $this->repo()->findOrFail($id);

            // 전달된 것만 반영
            $assign = function (string $key, $value, callable $filter = null) use ($model) {
                if (\array_key_exists($key, $value)) {
                    $model->{$key} = $filter ? $filter($value[$key]) : $value[$key];
                }
            };

            $assign('template_id', $payload, static fn($v) => (int) $v);

            foreach ([
                'vd_min', 'vd_max',
                'pd_right_min', 'pd_right_max',
                'pd_left_min', 'pd_left_max',
                'pd_total_distance',
                'wd_min', 'wd_max',
            ] as $numKey) {
                $assign($numKey, $payload, fn($v) => $this->numOrNull($v));
            }

            $this->repo()->save($model);

            // 프레임 컬러 동기화 (옵션)
            if (\array_key_exists('frame_color_ids', $payload)) {
                $colorIds = $this->filterValidColorIds($payload['frame_color_ids'] ?? []);
                $model->frameColors()->sync($colorIds);
            }

            return $this->render(null, ['id' => $model->id], '루페 세팅이 수정되었습니다.');
        });
    }

    /* ============================================================
     |  Destroy (DELETE /loupe-settings/{id})
     |============================================================ */
    public function destroy(string $id)
    {
        return $this->runInTransaction(function () use ($id) {
            $model = $this->repo()->findOrFail($id);

            if (!$this->repo()->delete($model)) {
                throw new RuntimeException('루페 세팅 삭제에 실패했습니다.');
            }

            return $this->render(null, [], '루페 세팅이 삭제되었습니다.');
        });
    }

    /* ============================================================
     |  Helpers
     |============================================================ */
    private function numOrNull($v): ?float
    {
        if ($v === '' || $v === null) return null;
        return (float) $v;
    }

    private function normalizePayload(Request $request): array
    {
        $payload = $request->all();

        // 구 키 호환: fd_* -> pd_*
        $aliases = [
            'fd_right_min'      => 'pd_right_min',
            'fd_right_max'      => 'pd_right_max',
            'fd_left_min'       => 'pd_left_min',
            'fd_left_max'       => 'pd_left_max',
            'fd_total_distance' => 'pd_total_distance',
        ];
        foreach ($aliases as $old => $new) {
            if (\array_key_exists($old, $payload) && !\array_key_exists($new, $payload)) {
                $payload[$new] = $payload[$old];
            }
        }

        // frame_color_ids / frame_colors -> 배열 정규화
        $raw = $payload['frame_color_ids'] ?? ($payload['frame_colors'] ?? null);
        $payload['frame_color_ids'] = $this->parseIdArray($raw);

        return $payload;
    }

    private function parseIdArray($raw): array
    {
        if (\is_array($raw)) {
            $ids = $raw;
        } elseif (\is_string($raw) && $raw !== '') {
            $json = json_decode($raw, true);
            if (\is_array($json)) {
                $ids = $json;
            } else {
                $ids = array_filter(array_map('trim', explode(',', $raw)), 'strlen');
            }
        } else {
            $ids = [];
        }

        // 정수화 + 중복제거
        $ids = array_values(array_unique(array_map('intval', $ids)));
        return $ids;
    }

    private function filterValidColorIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static fn($v) => $v > 0)));

        if (!$ids) return [];

        $valid = $this->colorRepo()
            ->query()
            ->whereIn('id', $ids)
            ->pluck('id');

        // 일부 ORM은 pluck이 컬렉션/배열 혼합일 수 있으니 배열화
        $validIds = array_map('intval', (array) $valid);

        return array_values(array_intersect($ids, $validIds));
    }
}
