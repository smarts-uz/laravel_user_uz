<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyProfileRequest extends FormRequest
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
            'sms_otp' => 'required',
            'for_ver_func'=> 'required'
        ];
    }

    public function messages()
    {
        return [
            'sms_otp.required' => 'Требуется заполнение!',
            'for_ver_func.required'=> 'Требуется заполнение!'
        ];
    }

}
