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
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore(auth()->user()->id),
            ],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('login.email.required'),
            'email.email' => __('login.email.email'),
            'email.uniques' => __('login.email.unique'),
            'phone_number.required' => __('login.phone_number.unique'),
            'phone_number.numeric' => __('login.phone_number.numeric'),
            'phone_number.min' => __('login.phone_number.min'),
            'phone_number.unique' => __('login.phone_number.unique'),
        ];
    }
}
