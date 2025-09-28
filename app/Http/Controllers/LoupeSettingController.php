<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Product\Entities\LoupeSetting;
use App\Domains\Product\Repositories\LoupeSettingRepository;
use App\Domains\Product\Repositories\LoupeFrameColorRepository;
use App\Domains\Product\Repositories\ProductTemplateRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Framework\Validation\Validator;
use RuntimeException;

class LoupeSettingController extends Controller 
{
    /** 목록 + 폼 화면 */
    public function index()
    {
        $settings  = LoupeSettingRepository::make()
            ->query()
            ->orderBy('id', 'DESC')
            ->get();

        $templates = ProductTemplateRepository::make()
            ->query()
            ->orderBy('sort_order', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->get();

        $colors    = LoupeFrameColorRepository::make()->listActiveOrdered();

        return $this->render('admin.pages.loupe-setting.index', [
            'settings'  => $settings,
            'templates' => $templates,
            'colors'    => $colors,
        ]);
    }

    /** 생성 (POST /loupe-settings) */
    public function store(Request $request)
    {
        // 들어오는 페이로드 전처리(별칭/타입 통일)
        $payload = $this->normalizePayload($request);

        Validator::make($payload, [
            'template_id'       => 'required|integer',
            'vd_min'            => 'nullable|numeric',
            'vd_max'            => 'nullable|numeric',

            'pd_right_min'      => 'nullable|numeric',
            'pd_right_max'      => 'nullable|numeric',
            'pd_left_min'       => 'nullable|numeric',
            'pd_left_max'       => 'nullable|numeric',
            'pd_total_distance' => 'nullable|numeric',

            'wd_min'            => 'nullable|numeric',
            'wd_max'            => 'nullable|numeric',

            'frame_color_ids'   => 'nullable|array',
            'frame_color_ids.*' => 'integer',
        ])->validateOrFail();

        return $this->runInTransaction(function () use ($payload) {
            // template_id 중복 방지 (template별 1개 세팅 가정)
            $dup = LoupeSettingRepository::make()
                ->query()
                ->where('template_id', (int)$payload['template_id'])
                ->first();

            if ($dup) {
                throw new RuntimeException('해당 제품 템플릿의 설정이 이미 존재합니다.');
            }

            $model = new LoupeSetting([
                'template_id'       => (int)$payload['template_id'],

                'vd_min'            => $this->numOrNull($payload['vd_min'] ?? null),
                'vd_max'            => $this->numOrNull($payload['vd_max'] ?? null),

                'pd_right_min'      => $this->numOrNull($payload['pd_right_min'] ?? null),
                'pd_right_max'      => $this->numOrNull($payload['pd_right_max'] ?? null),
                'pd_left_min'       => $this->numOrNull($payload['pd_left_min'] ?? null),
                'pd_left_max'       => $this->numOrNull($payload['pd_left_max'] ?? null),
                'pd_total_distance' => $this->numOrNull($payload['pd_total_distance'] ?? null),

                'wd_min'            => $this->numOrNull($payload['wd_min'] ?? null),
                'wd_max'            => $this->numOrNull($payload['wd_max'] ?? null),

                // Entity에서 json 캐스트라고 가정
                'frame_color_ids'   => $payload['frame_color_ids'] ?? [],
            ]);

            if (!LoupeSettingRepository::make()->save($model)) {
                throw new RuntimeException('저장에 실패했습니다.');
            }

            return $this->render(null, ['id' => $model->id], '저장되었습니다.');
        });
    }

    /** 수정 (PUT /loupe-settings/{id}) — 부분 업데이트 허용 */
    public function update(Request $request, string $id)
    {
        $payload = $this->normalizePayload($request);

        // 부분 업데이트 허용: 전달된 키만 검증
        $rules = [
            'template_id'       => 'integer',
            'vd_min'            => 'numeric',
            'vd_max'            => 'numeric',

            'pd_right_min'      => 'numeric',
            'pd_right_max'      => 'numeric',
            'pd_left_min'       => 'numeric',
            'pd_left_max'       => 'numeric',
            'pd_total_distance' => 'numeric',

            'wd_min'            => 'numeric',
            'wd_max'            => 'numeric',

            'frame_color_ids'   => 'array',
            'frame_color_ids.*' => 'integer',
        ];

        // 실제 들어온 필드만 뽑아서 검증
        $toValidate = [];
        foreach ($rules as $k => $rule) {
            if (array_key_exists($k, $payload)) {
                $toValidate[$k] = $rule;
            }
        }
        if ($toValidate) {
            Validator::make($payload, $toValidate)->validateOrFail();
        }

        return $this->runInTransaction(function () use ($id, $payload) {
            /** @var LoupeSetting $model */
            $model = LoupeSettingRepository::make()->findOrFail($id);

            // 전달된 항목만 반영
            $setIf = function (string $key, callable $filter = null) use (&$model, $payload) {
                if (array_key_exists($key, $payload)) {
                    $model->{$key} = $filter ? $filter($payload[$key]) : $payload[$key];
                }
            };

            $setIf('template_id', fn($v) => (int)$v);

            foreach (['vd_min','vd_max','pd_right_min','pd_right_max','pd_left_min','pd_left_max','pd_total_distance','wd_min','wd_max'] as $numKey) {
                $setIf($numKey, fn($v) => $this->numOrNull($v));
            }

            $setIf('frame_color_ids', function ($v) {
                return is_array($v) ? array_values(array_unique(array_map('intval', $v))) : [];
            });

            if (!LoupeSettingRepository::make()->save($model)) {
                throw new RuntimeException('저장에 실패했습니다.');
            }

            return $this->render(null, ['id' => $model->id], '저장되었습니다.');
        });
    }

    /** 삭제 (DELETE /loupe-settings/{id}) */
    public function destroy(string $id)
    {
        return $this->runInTransaction(function () use ($id) {
            $repo  = LoupeSettingRepository::make();
            $model = $repo->findOrFail($id);

            if (!$repo->delete($model)) {
                throw new RuntimeException('삭제에 실패했습니다.');
            }

            return $this->render(null, [], '삭제되었습니다.');
        });
    }

    // ---------------------
    // Helpers
    // ---------------------

    /** 숫자 or null */
    private function numOrNull($v): ?float
    {
        if ($v === '' || $v === null) return null;
        return (float)$v;
    }

    /**
     * 들어오는 요청을 표준화:
     * - fd_* => pd_* 로 키치환(구 명칭 호환)
     * - frame_color_ids/frame_colors: 배열/JSON/콤마문자열 모두 배열로
     */
    private function normalizePayload(Request $request): array
    {
        $payload = $request->all();

        // 1) fd_* => pd_* (구형 키 호환)
        $aliases = [
            'fd_right_min'       => 'pd_right_min',
            'fd_right_max'       => 'pd_right_max',
            'fd_left_min'        => 'pd_left_min',
            'fd_left_max'        => 'pd_left_max',
            'fd_total_distance'  => 'pd_total_distance',
        ];
        foreach ($aliases as $old => $new) {
            if (array_key_exists($old, $payload) && !array_key_exists($new, $payload)) {
                $payload[$new] = $payload[$old];
            }
        }

        // 2) frame colors: frame_color_ids / frame_colors 모두 처리
        $colorRaw = $payload['frame_color_ids'] ?? ($payload['frame_colors'] ?? null);
        $payload['frame_color_ids'] = $this->parseIdArray($colorRaw);

        return $payload;
    }

    /**
     * id 배열 파서: 배열/JSON 문자열/콤마 문자열 모두 허용
     * 존재하지 않는 컬러 ID를 배제하고 싶으면 여기서 필터링 추가 가능
     */
    private function parseIdArray($raw): array
    {
        if (is_array($raw)) {
            $ids = $raw;
        } elseif (is_string($raw) && $raw !== '') {
            $json = json_decode($raw, true);
            if (is_array($json)) {
                $ids = $json;
            } else {
                $ids = array_filter(array_map('trim', explode(',', $raw)), 'strlen');
            }
        } else {
            $ids = [];
        }

        // 정수화 + 중복제거
        $ids = array_values(array_unique(array_map('intval', $ids)));

        // (선택) 실제 존재하는 컬러만 허용하려면 주석 해제
        // $valid = LoupeFrameColorRepository::make()
        //     ->query()->whereIn('id', $ids)->pluck('id');
        // $ids = array_values(array_intersect($ids, $valid));

        return $ids;
    }
}
