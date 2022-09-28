<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TaskComplaintRequest extends FormRequest
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
            'compliance_type_id' => 'required|int',
            'text' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'compliance_type_id.*' => trans('trans.Choose the type.'),
            'text.*' => trans('trans.Enter the text.')
        ];
    }
}
