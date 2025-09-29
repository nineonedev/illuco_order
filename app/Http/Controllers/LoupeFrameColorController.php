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

        return $this->render('admin.pages.loupe-frame-colors.index', [
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
            $model = new LoupeFrameColor([
                'name'       => trim((string)$data['name']),
                'code'       => (string)$data['code'],
                'hex'       => $this->normalizeHex((string)$data['hex']),
                'is_active'  => (int) (!empty($data['is_active'])),
                'sort_order' => isset($data['sort_order']) ? (int)$data['sort_order'] : 0,
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

            if (array_key_exists('name', $data)) {
                $model->name = trim((string)$data['name']);
            }
            if (array_key_exists('code', $data)) {
                $model->code = trim((string)$data['code']);
            }
            if (array_key_exists('hex', $data)) {
                $model->hex = $this->normalizeHex((string)$data['hex']);
            }
            if (array_key_exists('is_active', $data)) {
                $model->is_active = (int) (!empty($data['is_active']));
            }
            if (array_key_exists('sort_order', $data)) {
                $model->sort_order = (int)$data['sort_order'];
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
            $repo  = LoupeFrameColorRepository::make();
            $model = $repo->findOrFail($id);

            if (!$repo->delete($model)) {
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
}
