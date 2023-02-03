<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8'
        ];
    }

    public function messages()
    {
        return [
            'password.required' => trans('login.password.required'),
            'password.min' => trans('login.password.min'),
            'password.confirmed' => trans('login.password.confirmed'),
            'password_confirmation.required' => trans('login.password.required'),
            'password_confirmation.min' => trans('login.password.min')
        ];
    }
}
