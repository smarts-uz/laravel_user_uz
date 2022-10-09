<?php

namespace App\Http\Requests\Api;

class ReviewRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'comment' => 'required',
            'good' => 'required',
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'comment.required' => __('Требуется заполнение!'),
            'good.required' => __('Требуется заполнение!'),
            'status.required' => __('Требуется заполнение!'),
        ];
    }
}
