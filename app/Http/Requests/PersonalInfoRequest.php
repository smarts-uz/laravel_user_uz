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
            'email' => 'required|email',
            'phone_number' => 'required|numeric|min:13',
        ];
    }
    public function messages()
    {
        return [
            'email.required' => __('login.email.required'),
            'email.email' => __('login.email.email'),
            'email.unique' => __('login.email.unique'),
            'phone_number.required' => __('login.phone_number.required'),
            'phone_number.numeric' => __('login.phone_number.numeric'),
            'phone_number.unique' => __('login.phone_number.unique'),
            'phone_number.min' => __('login.phone_number.min'),
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
                'phone_number' => str_replace(['-','_','(',')'], '', $this->request->get('phone_number'))
            ]);
        }
    }
}
