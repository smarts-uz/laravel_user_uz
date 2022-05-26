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

    public function messages()
    {
        return [
            'phone_number.required' => trans('trans.Enter phone number.'),
            'phone_number.numeric' => trans('trans.Phone number should be number.'),
            'password.required' => trans('trans.Enter password.'),
            'password.confirmed' => trans('trans.Confirm password.'),
        ];
    }
}
