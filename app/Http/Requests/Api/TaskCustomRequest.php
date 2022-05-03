<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TaskCustomRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'task_id' => 'required'
        ];
    }
}
