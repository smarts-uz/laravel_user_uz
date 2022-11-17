<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BecomePerformerEmailPhone extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'phone_number' =>
                [
                    'required',
                    'min:9',
                    'numeric',
                    Rule::unique('users')->ignore(auth()->user()->id),
                ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore(auth()->user()->id),
            ],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('Требуется заполнение!'),
            'email.email' => __('Введите адрес электронной почты в правильном формате!'),
            'email.unique' => __('Пользователь с такой почтой уже существует!'),
            'phone_number.required' => __('Требуется заполнение!'),
            'phone_number.numeric' => __('Поле должно быть числом'),
            'phone_number.min' => __('Неверный формат номера телефона!'),
            'phone_number.unique' => __('Этот номер есть в системе!'),
        ];
    }
}
