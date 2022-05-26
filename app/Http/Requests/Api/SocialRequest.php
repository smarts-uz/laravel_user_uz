<?php

namespace App\Http\Requests\Api;

class SocialRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'access_token' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'access_token.required' => "access_token  required",
        ];
    }
}
