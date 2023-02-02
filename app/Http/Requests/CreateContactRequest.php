<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateContactRequest extends FormRequest
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
            'phone_number' => 'required|numeric|min:13|unique:users,phone_number,' . auth()->id(),
        ];
    }
    public function messages()
    {
        return  [
            'phone_number.required' => __('login.phone_number.required'),
            'phone_number.unique' => __('login.phone_number.unique'),
            'phone_number.min' => __('login.phone_number.min'),
            'phone_number.numeric' => __('login.phone_number.numeric'),
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
