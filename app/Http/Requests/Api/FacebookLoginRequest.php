<?php

namespace App\Http\Requests\Api;

class FacebookLoginRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'facebook_id' => 'required|nullable',
            'email' => 'nullable', //email
            'name' => 'string', //required
            'avatar' => 'string', //required
            'server_code' => 'string', //required
        ];
    }

    public function messages()
    {
        return [
            'facebook_id.required' => "facebook_id  required",
            'email.required' => "Email  required",
            'name.required' => "name  required",
            'avatar.required' => "avatar  required",
            'server_code.required' => "server_code  required",
        ];
    }
}
