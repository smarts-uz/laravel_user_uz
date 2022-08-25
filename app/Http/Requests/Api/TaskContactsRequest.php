<?php

namespace App\Http\Requests\Api;

use Illuminate\Validation\Rule;

class TaskContactsRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'phone_number' => [
                'required', 'integer', 'min:13',
                Rule::unique('users')->ignore(auth()->id())
            ],
            'task_id' => 'required',
        ];
    }
}
