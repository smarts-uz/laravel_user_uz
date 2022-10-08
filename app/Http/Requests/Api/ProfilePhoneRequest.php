<?php

namespace App\Http\Requests\Api;

class ProfilePhoneRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'phone_number' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'phone_number.required' => trans('trans.Enter your phone number.'),
        ];
    }
}
