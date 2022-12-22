<?php

namespace App\Http\Requests\Api;


class ResponseTemplateRequest extends BaseRequest
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
            'title' => 'required|string',
            'text' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __('Требуется заполнение!'),
            'title.string' => __('Текстовое поле должно быть строкой.'),
            'text.required' => __('Требуется заполнение!'),
            'text.string' => __('Текстовое поле должно быть строкой.'),
        ];
    }
}
