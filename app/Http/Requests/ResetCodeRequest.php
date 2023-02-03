<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetCodeRequest extends FormRequest
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
            'code' => 'required|numeric|min:6'
        ];
    }

    public function messages()
    {
        return [
            'code.required' => __('Требуется заполнение!'),
            'code.numeric' =>  __('Поле должно быть числом'),
            'code.min' => __('Поле должно содержать не менее 6 символов')
        ];
    }
}
