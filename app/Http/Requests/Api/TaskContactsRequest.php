<?php

namespace App\Http\Requests\Api;

class TaskContactsRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'phone_number' => 'required|integer|min:9|unique:users,phone_number,' . auth()->id(),
            'task_id' => 'required',
        ];
    }
}
