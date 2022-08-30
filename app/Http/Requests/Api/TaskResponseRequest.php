<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TaskResponseRequest extends FormRequest
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
            'description' => 'required|string',
            'price' => 'int|required',
            'notificate' => 'nullable',
            'not_free' => 'nullable|int'
        ];
    }


    public function messages()
    {
        return [
            'description.required' => __('login.name.required'),
            'price.required' => __('login.name.required'),
            'price.int' => __('login.name.int'),
            'not_free.int' => __('login.name.int'),
        ];
    }
}