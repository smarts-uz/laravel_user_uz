<?php

namespace App\Http\Requests\Api;

class SocialRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'type' => 'required',
            'access_token' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'type.required' => "type required",
            'access_token.required' => "access_token  required",
        ];
    }
}
