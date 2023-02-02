<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserRegisterRequest extends FormRequest
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
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'phone_number' =>  'numeric|unique:users|min:13',
            'password' => 'required|confirmed|min:8'
        ];
    }
    public function messages()
    {
        return [
            'name.required' => trans('login.name.required'),
            'name.unique' => trans('login.name.unique'),
            'email.required' => trans('login.email.required'),
            'email.email' => trans('login.email.email'),
            'email.unique' => trans('login.email.unique'),
            'password.required' => trans('login.password.required'),
            'password.min' => trans('login.password.min'),
            'password.confirmed' => trans('login.password.confirmed'),
            'phone_number.unique' => trans('login.phone_number.unique')
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
