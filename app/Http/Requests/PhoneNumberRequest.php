<?php

namespace App\Http\Requests;

use App\Http\Requests\Api\BaseRequest;

class PhoneNumberRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'phone_number' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'phone_number.required' =>  __('login.phone_number.required'),
            'phone_number.numeric' => __('login.phone_number.numeric'),
        ];

    }
}
