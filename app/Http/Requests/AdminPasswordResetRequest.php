<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminPasswordResetRequest extends FormRequest
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
            'password' => 'required|confirmed|min:8'
        ];
    }
    public function messages()
    {
        return [
            'password.required' => __('login.password.required'),
            'password.min' => __('login.password.min'),
            'password.confirmed' => __('login.password.confirmed'),
        ];
    }
}
