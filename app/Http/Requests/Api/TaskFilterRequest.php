<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TaskFilterRequest extends FormRequest
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
            'categories' => 'nullable',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
            'budget' => 'nullable|int',
            'is_remote' => 'nullable|boolean',
            'without_response' => 'nullable',
            'difference' => 'nullable|int',
        ];
    }
}
