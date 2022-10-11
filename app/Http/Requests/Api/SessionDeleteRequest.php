<?php

namespace App\Http\Requests\Api;

class SessionDeleteRequest extends BaseRequest
{

   public function rules()
    {
        return [
            'session_id' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'session_id.required' => __('login.name.required'),
        ];
    }
}
