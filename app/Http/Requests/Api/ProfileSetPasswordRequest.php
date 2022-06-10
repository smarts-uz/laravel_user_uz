<?php

namespace App\Http\Requests\Api;


class ProfileSetPasswordRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'password' => 'required|confirmed|min:6',
        ];
    }

    public function messages()
    {
        return [
            'password.required' => trans('trans.Enter new password.'),
            'password.confirmed' => trans('trans.Confirm new password.'),
            'password.min' => trans('trans.Password length should be more than 6.'),
        ];
    }
}
