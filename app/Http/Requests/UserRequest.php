<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use JetBrains\PhpStorm\ArrayShape;

class UserRequest extends FormRequest
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
    #[ArrayShape([])]
    public function rules()
    {
        session()->flash('phone_another');
        return [
            'name' => 'required',
            'email' => ['required','email','unique:users'],
            'phone_number' => 'required|unique:users|min:13',
            'password' => 'required|confirmed|min:8'
        ];
    }

    public function messages()
    {
        return [
                'name.required' => __('Требуется заполнение!'),
                'phone_number.required' =>  __('Требуется заполнение!'),
                'phone_number.regex' => __('login.phone_number.regex'),
                'phone_number.unique' => __('Этот номер есть в системе!'),
                'phone_number.min' => __('Неверный формат номера телефона!'),
                'email.required' => __('Требуется заполнение!'),
                'email.email' => __('Введите адрес электронной почты в правильном формате!'),
                'email.unique' => __('login.email.unique'),
                'password.required' => __('Требуется заполнение!'),
                'password.min' => __('login.password.min'),
                'password.confirmed' => __('login.password.confirmed'),
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
