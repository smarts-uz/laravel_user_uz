<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfileSettingsRequest extends FormRequest
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
            'gender' => 'required',
            'location' => 'nullable',
            'born_date' => 'required|date',
            'age' => 'required',
            'email' => 'required|email',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => trans('file.Enter your name.'),
            'name.string' => trans('file.Name should be in format of text.'),
            'gender.required' => trans('file.Choose your gender.'),
            'born_date.required' => trans('file.Enter your date of birth.'),
            'born_date.date' => trans('file.Date of birth should be in format of date.'),
            'age.required' => trans('file.Enter your age.'),
            'email.required' => trans('file.Enter your email.'),
            'email.email' => trans('file.Email should be in format of email.'),
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'data' => [
                'message' => $validator->errors()
            ]
        ]));
    }
}