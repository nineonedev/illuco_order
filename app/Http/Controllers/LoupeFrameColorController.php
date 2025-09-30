<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domains\Product\Entities\LoupeFrameColor;
use App\Domains\Product\Repositories\LoupeFrameColorRepository;
use Framework\Http\Request;
use Framework\Routing\Controller;
use Framework\Validation\Validator;
use RuntimeException;

class LoupeFrameColorController extends Controller
{
    /** 목록 화면 */
    public function index()
    {
        $colors = LoupeFrameColorRepository::make()
            ->query()
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'DESC')
            ->get();

        return $this->render('admin.pages.loupe-settings.frame-colors', [
            'colors' => $colors,
        ]);
    }

    /** 생성 (POST /loupe-frame-colors) */
    public function store(Request $request)
    {
        $data = $request->all();

        Validator::make($data, [
            'name'       => 'required|string',
            'hex'        => 'nullable|string',
            'code'       => 'required|string',
            'is_active'  => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
        ])->validateOrFail();

        return $this->runInTransaction(function () use ($data) {
            $name = trim((string) $data['name']);
            $code = $this->normalizeCode($data['code']);

            // --- 중복 검사: 동일 name 또는 code 존재 시 에러 ---
            $dupName = LoupeFrameColorRepository::make()->query()->where('name', $name)->first();
            if ($dupName) {
                throw new RuntimeException('이미 존재하는 이름입니다.');
            }
            $dupCode = LoupeFrameColorRepository::make()->query()->where('code', '=', $code)->get();

            if ($dupCode) {
                throw new RuntimeException('이미 존재하는 코드입니다.');
            }

            $model = new LoupeFrameColor([
                'name'       => $name,
                'code'       => $code,
                'hex'        => $this->normalizeHex((string) ($data['hex'] ?? '')),
                'is_active'  => isset($data['is_active']) ? (int) $data['is_active'] : 0,
                'sort_order' => isset($data['sort_order']) ? (int) $data['sort_order'] : 0,
            ]);

            if (!LoupeFrameColorRepository::make()->save($model)) {
                throw new RuntimeException('색상 저장에 실패했습니다.');
            }

            return $this->render(null, ['id' => $model->id], '저장되었습니다.');
        });
    }

    /** 수정 (PUT /loupe-frame-colors/{id}) — 부분 업데이트 허용 */
    public function update(Request $request, string $id)
    {
        $data = $request->all();

        // 실제 들어온 필드만 골라서 검증
        $rules = [
            'name'       => 'string',
            'code'       => 'string',
            'hex'        => 'string',
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
        $toValidate = [];
        foreach ($rules as $k => $rule) {
            if ($request->has($k)) {
                $toValidate[$k] = $rule;
            }
        }
        if ($toValidate) {
            Validator::make($data, $toValidate)->validateOrFail();
        }

        return $this->runInTransaction(function () use ($id, $data) {
            /** @var LoupeFrameColor $model */
            $model = LoupeFrameColorRepository::make()->findOrFail($id);

            // name 변경 시 중복 확인
            if (array_key_exists('name', $data)) {
                $nextName = trim((string) $data['name']);
                $dupModel = LoupeFrameColorRepository::make()->query()
                    ->where('name', '=', $nextName)
                    ->first();

                if ($dupModel && $dupModel->id != $id) {
                    throw new RuntimeException('이미 존재하는 이름입니다.');
                }
                $model->name = $nextName;
            }

            // code 변경 허용 시(현재 UI에서는 disabled이지만 서버는 대비)
            if (array_key_exists('code', $data)) {
                $nextCode = $this->normalizeCode((string) $data['code']);
                $dupCode = LoupeFrameColorRepository::make()->query()
                    ->where('code', $nextCode)
                    ->get();
                if ($dupCode) {
                    throw new RuntimeException('이미 존재하는 코드입니다.');
                }
                $model->code = $nextCode;
            }

            if (array_key_exists('hex', $data)) {
                $model->hex = $this->normalizeHex((string) $data['hex']);
            }
            if (array_key_exists('is_active', $data)) {
                $model->is_active = (int) (!empty($data['is_active']));
            } else {
                $model->is_active = 0;
            }
            if (array_key_exists('sort_order', $data)) {
                $model->sort_order = (int) $data['sort_order'];
            }

            if (!LoupeFrameColorRepository::make()->save($model)) {
                throw new RuntimeException('저장에 실패했습니다.');
            }

            return $this->render(null, ['id' => $model->id], '저장되었습니다.');
        });
    }

    /** 삭제 (DELETE /loupe-frame-colors/{id}) */
    public function destroy(string $id)
    {
        return $this->runInTransaction(function () use ($id) {
            $model = LoupeFrameColorRepository::make()->findOrFail($id);

            if (!LoupeFrameColorRepository::make()->delete($model)) {
                throw new RuntimeException('삭제에 실패했습니다.');
            }

            return $this->render(null, [], '삭제되었습니다.');
        });
    }

    // ---------------------
    // helpers
    // ---------------------
    private function normalizeHex(string $code): string
    {
        $c = ltrim(trim($code), '#');
        $c = strtoupper($c);
        return '#' . $c;
    }

    /** 공백 포함한 code를 저장 안전한 형태로 변환: 모든 공백→'_' , 연속 '_' 축약, 앞뒤 '_' 제거 */
    private function normalizeCode(string $code): string
    {
        $c = trim($code);
        $c = preg_replace('/\s+/', '_', $c ?? '');   // space→underscore
        $c = preg_replace('/_+/', '_', $c);          // collapse multiple _
        $c = trim($c, '_');                          // trim edge _
        return $c;
    }
}
