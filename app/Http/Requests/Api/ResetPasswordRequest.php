<?php

namespace App\Http\Requests\Api;

class ResetPasswordRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'phone_number' => 'required',
            'password' => 'required|string|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'phone_number.required' => __('login.phone_number.required'),
            'password.required' => __('login.password.required'),
            'password.confirmed' => __('login.password.confirmed'),
        ];
    }
}
