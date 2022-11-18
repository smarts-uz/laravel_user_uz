<?php

namespace App\Http\Requests\Api;

class UserUpdateRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'required|min:5|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|max:16',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('Требуется заполнение!'),
            'email.required' => __('Требуется заполнение!'),
            'password.required' => __('Требуется заполнение!'),
            'email.email' => __('Введите e-mail в правильном формате!'),
            'email.unique' => __('Пользователь с такой почтой уже существует!'),
            'name.min' => __('Проверяемое поле должно иметь минимальное значение.'),
            'name.max' => __('Проверяемое поле должно быть меньше или равно максимальному значению.'),
            'password.min' => __('Проверяемое поле должно иметь минимальное значение.'),
            'password.max' => __('Проверяемое поле должно быть меньше или равно максимальному значению.'),
        ];
    }
}
