<?php

namespace App\Http\Requests\Api;

class UserUpdateRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'required|min:5|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|max:16',
        ];
    }
}
