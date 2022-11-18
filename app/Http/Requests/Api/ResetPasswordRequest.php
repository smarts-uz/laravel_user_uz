<?php

namespace App\Http\Requests\Api;

class ResetPasswordRequest extends BaseRequest
{

    public function rules()
    {
        return [
            'phone_number' => 'required',
            'password' => 'required|string|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'phone_number.required' => __('Требуется заполнение!'),
            'password.required' => __('Требуется заполнение!'),
            'password.confirmed' => __('Значение поля не соответствует проверке'),
        ];
    }
}
