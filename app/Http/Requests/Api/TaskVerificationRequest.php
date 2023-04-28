<?php

namespace App\Http\Requests\Api;

class TaskVerificationRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'sms_otp' => 'required',
            'task_id' => 'required',
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
