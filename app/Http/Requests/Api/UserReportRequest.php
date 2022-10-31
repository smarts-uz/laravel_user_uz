<?php

namespace App\Http\Requests\Api;

class UserReportRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'reported_user_id' => 'required',
            'message' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'reported_user_id.required' => __('login.name.required'),
            'message.required' => __('login.name.required'),
        ];
    }
}
