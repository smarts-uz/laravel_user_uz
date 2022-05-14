<?php

namespace App\Http\Requests;

use App\Http\Requests\Api\BaseRequest;

class GiveTaskRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'task_id' => 'required|numeric',
            'performer_id' => 'required|numeric'
        ];
    }
}
