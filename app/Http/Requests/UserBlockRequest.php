<?php

namespace App\Http\Requests;

use App\Http\Requests\Api\BaseRequest;

class UserBlockRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'blocked_user_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'blocked_user_id.required' => __('login.name.required'),
        ];
    }
}
