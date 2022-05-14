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
}
