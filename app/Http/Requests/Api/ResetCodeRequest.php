<?php

namespace App\Http\Requests\Api;


class ResetCodeRequest extends BaseRequest
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
            'code' => 'required|numeric|min:6',
            'phone_number' => 'required|numeric'
        ];
    }

    public function messages()
    {
        return [
            'phone_number.required' => __('Требуется заполнение!'),
            'phone_number.numeric' => __('login.phone_number.numeric'),
            'code.required' => __('Требуется заполнение!'),
            'code.numeric' =>  __('Поле должно быть числом'),
            'code.min' => __('Поле должно содержать не менее 6 символов')
        ];
    }
}
