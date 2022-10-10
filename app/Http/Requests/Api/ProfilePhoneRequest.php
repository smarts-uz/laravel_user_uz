<?php

namespace App\Http\Requests\Api;

use JetBrains\PhpStorm\ArrayShape;

class ProfilePhoneRequest extends BaseRequest
{
    #[ArrayShape([])]
    public function rules()
    {
        return [
            'phone_number' => 'required'
        ];
    }

    #[ArrayShape([])]
    public function messages()
    {
        return [
            'phone_number.required' => trans('trans.Enter your phone number.'),
        ];
    }
}
