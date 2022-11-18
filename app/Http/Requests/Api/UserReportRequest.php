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
            'reported_user_id.required' => __('Требуется заполнение!'),
            'message.required' => __('Требуется заполнение!'),
        ];
    }
}
