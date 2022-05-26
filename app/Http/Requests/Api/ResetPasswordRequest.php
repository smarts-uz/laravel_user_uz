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
            'phone_number.required' => trans('trans.Enter phone number.'),
            'password.required' => trans('trans.Enter password.'),
            'password.confirmed' => trans('trans.Confirm password.'),
        ];
    }
}
