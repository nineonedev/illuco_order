<?php

namespace App\Http\Requests\Product;

use App\Domains\Product\Entities\Loupe;
use Framework\Http\FormRequest;

class SaveLoupeRequest extends FormRequest
{
    protected function rules(): array
    {
        $rules = [
            'type' => 'required|string|in:ready-made,custom-made',
            'model' => 'required|string|exists:product_templates,model',
            'frame_type' => 'nullable|string',
            'working_distance' => 'nullable|numeric',
            'od_sph' => 'nullable|numeric|min:-20|max:20',
            'os_sph' => 'nullable|numeric|min:-20|max:20',
            'od_cyl' => 'nullable|numeric|min:-10|max:10',
            'os_cyl' => 'nullable|numeric|min:-10|max:10',
            'od_axis' => 'nullable|numeric|min:0|max:180',
            'os_axis' => 'nullable|numeric|min:0|max:180',
            'od_add' => 'nullable|numeric|min:0|max:4',
            'os_add' => 'nullable|numeric|min:0|max:4',
            'vd' => 'nullable|numeric|min:10|max:25',
            'pd_right' => 'nullable|numeric|min:27|max:40',
            'pd_left' => 'nullable|numeric|min:27|max:40',
            'add_option' => 'nullable|string|in:ignore,include,zero_diopter',
        ];

        if ($this->input('type') === 'custom-made') {
            $rules['frame_type'] = 'required|string';
            $rules['working_distance'] = 'required|numeric';
            $rules['od_sph'] = 'required|numeric|min:-20|max:20';
            $rules['os_sph'] = 'required|numeric|min:-20|max:20';
            $rules['od_cyl'] = 'required|numeric|min:-10|max:10';
            $rules['os_cyl'] = 'required|numeric|min:-10|max:10';
            $rules['od_axis'] = 'required|numeric|min:0|max:180';
            $rules['os_axis'] = 'required|numeric|min:0|max:180';
            $rules['od_add'] = 'required|numeric|min:0|max:4';
            $rules['os_add'] = 'required|numeric|min:0|max:4';
            $rules['vd'] = 'required|numeric|min:10|max:25';
            $rules['pd_right'] = 'required|numeric|min:27|max:40';
            $rules['pd_left'] = 'required|numeric|min:27|max:40';
        } else {
            $rules['frame_type'] = 'required|string';
            $rules['working_distance'] = 'required|numeric';
        }

        return $rules;
    }


    public function authorize(): bool
    {
        return true;
    }

    protected function beforeValidation(): void
    {
        $model = $this->input('model');

        if (!$model) {
            return;
        }

        $specs = Loupe::getSpecsForModel($model);
        $type = $this->input('type');

        if ($type === 'ready-made') {
            $this->validateWorkingDistance($specs);
            $this->validateFrameType($specs);
        } else {
            $this->validateWorkingDistance($specs);
            $this->validateFrameType($specs);
            $this->validateVertextDistance();
            $this->validatePdTotal();
            $this->validateSphCylAxisAdd();
        }

        $this->validateAddOption();
    }

    protected function validateWorkingDistance(array $specs)
    {
        $wdInput = $this->input('working_distance');

        if ($wdInput === null || $wdInput === '') {
            $this->validator->addError(
                'working_distance',
                '작업거리는 반드시 입력해야 합니다.'
            );
            return;
        }

        $wd = (float) $wdInput;

        if (isset($specs['working_distance'])) {
            $min = $specs['working_distance']['min'];
            $max = $specs['working_distance']['max'];

            if ($wd < $min || $wd > $max) {
                $this->validator->addError(
                    'working_distance',
                    "작업거리는 {$min}~{$max} cm 이어야 합니다."
                );
            }
        }
    }


    protected function validateFrameType(array $specs)
    {
        $frame = $this->input('frame_type');
        $allowed = array_column($specs['frame_types'] ?? [], 'value');

        if ($frame === null || $frame === '') {
            $this->validator->addError(
                'frame_type',
                "프레임은 반드시 선택해야 합니다."
            );
            return;
        }

        if (!in_array($frame, $allowed, true)) {
            $this->validator->addError(
                'frame_type',
                "선택하신 프레임은 해당 모델에서 사용할 수 없습니다."
            );
        }
    }


    protected function validateVertextDistance()
    {
        $vd = $this->input('vertex_distance');

        if ($vd === null || $vd === '') {
            $this->validator->addError(
                'vertex_distance',
                "Vertext Distance는 반드시 입력해야 합니다."
            );
            return;
        }

        $vd = (float) $vd;

        if ($vd < 10 || $vd > 25) {
            $this->validator->addError(
                'vertex_distance',
                "Vertext Distance는 10~25mm 사이여야 합니다."
            );
        }
    }

    protected function validatePdTotal()
    {
        $pdRight = $this->input('pd_right');
        $pdLeft = $this->input('pd_left');

        if ($pdRight === null || $pdRight === '') {
            $this->validator->addError(
                'pd_right',
                "PD Right는 반드시 입력해야 합니다."
            );
            return;
        }

        if ($pdLeft === null || $pdLeft === '') {
            $this->validator->addError(
                'pd_left',
                "PD Left는 반드시 입력해야 합니다."
            );
            return;
        }

        $pdRight = (float) $pdRight;
        $pdLeft = (float) $pdLeft;

        if (abs($pdRight - $pdLeft) > 2) {
            $this->validator->addError(
                'pd_total',
                "좌우 PD 차이가 ±2mm를 초과합니다."
            );
        }
    }


    protected function validateSphCylAxisAdd()
    {
        $fields = ['od_sph', 'os_sph', 'od_cyl', 'os_cyl', 'od_axis', 'os_axis', 'od_add', 'os_add'];
        foreach ($fields as $field) {
            $value = $this->input($field);

            if ($value === null || $value === '') {
                $this->validator->addError(
                    $field,
                    "{$field}는 반드시 입력해야 합니다."
                );
                continue;
            }

            $value = (float) $value;
            switch ($field) {
                case 'od_sph':
                case 'os_sph':
                    if ($value < -20 || $value > 20) {
                        $this->validator->addError($field, "{$field}는 -20 ~ +20 범위여야 합니다.");
                    }
                    break;
                case 'od_cyl':
                case 'os_cyl':
                    if ($value < -10 || $value > 10) {
                        $this->validator->addError($field, "{$field}는 -10 ~ +10 범위여야 합니다.");
                    }
                    break;
                case 'od_axis':
                case 'os_axis':
                    if ($value < 0 || $value > 180) {
                        $this->validator->addError($field, "{$field}는 0 ~ 180 범위여야 합니다.");
                    }
                    break;
                case 'od_add':
                case 'os_add':
                    if ($value < 0 || $value > 4) {
                        $this->validator->addError($field, "{$field}는 0 ~ 4 범위여야 합니다.");
                    }
                    break;
            }
        }
    }

    protected function validateAddOption()
    {
        $value = $this->input('add_option');
        $allowed = ['ignore', 'include', 'zero_diopter'];

        if ($value && !in_array($value, $allowed, true)) {
            $this->validator->addError(
                'add_option',
                "선택하신 ADD 옵션이 유효하지 않습니다."
            );
        }
    }
}
