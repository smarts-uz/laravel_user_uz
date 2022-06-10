<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersonalInfoRequest extends FormRequest
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
            'email' => 'required',
            'phone_number' => 'required|integer|unique:users|min:13',
        ];
    }
    public function messages()
    {
        return [
            'email.required' => __('email.required'),
            'phone_number.required' => __('phone_number.required'),
            'phone_number.integer' => __('phone_number.integer'),
            'phone_number.unique' => __('phone_number.unique'),
            'phone_number.min' => __('phone_number.min'),
        ];
    }
    public function getValidatorInstance()
    {
        $this->cleanPhoneNumber();
        return parent::getValidatorInstance();
    }

    protected function cleanPhoneNumber()
    {
        if($this->request->has('phone_number')){
            $this->merge([
                'phone_number' => str_replace(['-','(',')'], '', $this->request->get('phone_number'))
            ]);
        }
    }
}
