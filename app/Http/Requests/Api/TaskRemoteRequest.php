<?php

namespace App\Http\Requests\Api;

class TaskRemoteRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'task_id' => 'required',
            'radio' => 'required|in:address,remote'
        ];
    }
}
