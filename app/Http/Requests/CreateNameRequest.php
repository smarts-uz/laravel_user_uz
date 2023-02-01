<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateNameRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'category_id' => 'required'
        ];
    }

    public function messages()
    {
        return  [
            'name.required'=>__('Требуется заполнение!'),
            'name.string'=>__('Требуется заполнение!'),
            'name.max'=>__('Требуется заполнение!'),
            'category_id.required'=>__('Требуется заполнение!')
        ];
    }
}
