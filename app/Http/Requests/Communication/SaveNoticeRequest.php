<?php

namespace App\Http\Requests\Communication;

use Framework\Http\FormRequest;

class SaveNoticeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'        => 'required|string',
            'content'      => 'nullable|string',
            'visible_from' => 'nullable|date',
            'visible_to'   => 'nullable|date',
            'is_pinned'    => 'nullable|boolean',
            // 'status'       => 'required|in:draft,published,archived,scheduled',
            'status'       => 'required',
        ];
    }
}