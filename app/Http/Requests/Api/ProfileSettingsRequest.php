<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ProfileSettingsRequest extends BaseRequest
{

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
}
