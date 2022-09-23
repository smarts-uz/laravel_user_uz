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
            'comment.required' => 'Требуется заполнение!',
            'good.required' => 'Требуется заполнение!',
            'status.required' => 'Требуется заполнение!'
        ];
    }
}
