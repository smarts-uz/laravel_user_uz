<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PerformerCreateRequest extends FormRequest
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
            'location' => 'required|string',
            'name' => 'required|string',
            'last_name' => 'required|string',
            'born_date' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'location.required' => __('Требуется заполнение!'),
            'name.required' => __('Требуется заполнение!'),
            'last_name.required' => __('Требуется заполнение!'),
            'born_date.required' => __('Требуется заполнение!'),
        ];
    }
}
