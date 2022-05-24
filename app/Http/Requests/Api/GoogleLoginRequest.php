<?php

namespace App\Http\Requests\Api;

class GoogleLoginRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'id' => 'required',
            'email' => '', //email
            'name' => 'string', //required
            'avatar' => 'string', //required
            'server_code' => 'string', //required
        ];
    }

    public function messages()
    {
        return [
            'id.required' => "id  required",
            'email.required' => "Email  required",
            'name.required' => "name  required",
            'avatar.required' => "avatar  required",
            'server_code.required' => "server_code  required",
        ];
    }
}
