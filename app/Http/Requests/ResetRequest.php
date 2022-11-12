<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetRequest extends FormRequest
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
            'phone_number' => 'required|integer|exists:users|min:13'
        ];
    }
    public function messages()
    {
        return [
            'phone_number.integer' => __('login.phone_number.integer'),
            'phone_number.required' =>  __('login.phone_number.required'),
            'phone_number.exists' => __('login.phone_number.exists'),
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
                'phone_number' => str_replace(['-','(',')'], '', $this->request->get('phone_number'))
            ]);
        }
    }
}
