<?php

namespace App\Http\Requests\Api;


class TaskNameRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string',
            'category_id' => 'required'
        ];
    }
}
