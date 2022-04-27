<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BecomePerformerEmailPhone extends FormRequest
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
            'phone_number' =>
                [
                    'required',
                    'min:9',
                    'numeric',
                    Rule::unique('users')->ignore(auth()->user()->id),
                ],
            'email' =>  [
                'required',
                'email',
                Rule::unique('users')->ignore(auth()->user()->id),
            ],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Email is must be compatible with EMAIL',
            'email.uniques' => 'Email is must be auth email',
            'phone_number.required' => 'Phone is required',
            'phone_number.numeric' => 'Phone is must be numeric',
            'phone_number.min' => 'Entered Phone must be minimum 9',
            'phone_number.unique' => 'Entered Phone must be auth phone 9',
        ];
    }
}
