<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfilePasswordRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'old_password' => 'required',
            'password' => 'required|confirmed|min:6',
        ];
    }

    public function messages()
    {
        return [
            'old_password.required' => trans('trans.Enter old password.'),
            'password.required' => trans('trans.Enter new password.'),
            'password.confirmed' => trans('trans.Confirm new password.'),
            'password.min' => trans('trans.Password length should be more than 6.'),
        ];
    }
}
