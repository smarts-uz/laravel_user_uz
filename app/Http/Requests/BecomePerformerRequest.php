<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BecomePerformerRequest extends FormRequest
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
            'name' => 'required|string',
            'location' => 'required|string',
            'born_date' => 'required|date',
        ];
    }

    public function messages()
    {
        return [
            'name.required'  => 'Name is required!',
            'location.required'  => 'location is required!',
            'born_date.required'  => 'born_date is required!',
        ];
    }
}
