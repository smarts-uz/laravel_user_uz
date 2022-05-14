<?php

namespace App\Http\Requests\Api;

class ResetPasswordRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'phone_number' => 'required|numeric',
            'password' => 'required|string|confirmed',
        ];
    }
}
