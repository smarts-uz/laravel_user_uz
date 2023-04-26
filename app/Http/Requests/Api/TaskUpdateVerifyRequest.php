<?php

namespace App\Http\Requests\Api;

class TaskUpdateVerifyRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'sms_otp' => 'required',
            'phone_number' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'sms_otp.required' => 'Требуется заполнение!'
        ];
    }
}
