<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateDataRequest extends FormRequest
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
        $validation = [
            'email' => 'required|email|unique:users',
            'age' => 'nullable|int',
            'gender' => 'nullable|int',
            'phone_number' => 'required|numeric|unique:users|min:13',
            'description' => 'nullable',
            'location' => 'nullable',
        ];
        if (auth()->user()->email == $this->request->get('email')) {
            $validation['email'] = "required|email";
        }
        if (auth()->user()->phone_number == $this->request->get('phone_number')) {
            $validation['phone_number'] = "required|min:13";
        }

        return $validation;
    }


    public function messages()
    {
        return [
            'name.required' => __('login.name.required'),
            'email.required' => __('login.name.required'),
            'age.int' => __('login.name.required'),
            'phone_number.int' => __('login.phone_number.int'),
            'phone_number.min' => __('login.phone_number.min'),
            'phone_number.required' => __('login.phone_number.required'),
            'phone_number.unique' => __('Этот номер зарегистрирован'),
            'role_id.required' => __('login.name.required'),

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
