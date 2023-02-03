<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetEmailRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('Требуется заполнение!'),
            'email.email' => __('Введите адрес электронной почты в правильном формате!'),
            'email.exists' => __('Этот адрес электронной почты не имеет учетной записи!'),
        ];
    }
}
