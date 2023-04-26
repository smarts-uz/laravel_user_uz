<?php

namespace App\Http\Requests\Api;

class TaskUpdateRemoteRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'radio' => 'required|in:address,remote'
        ];
    }
}
